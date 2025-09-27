<?php

namespace App\Ttn\Application\UseCases;

use App\Entity\Client\LogMail as ClientLogMail;
use App\Entity\Client\WeightsLog;
use App\Entity\Main\Client as MainClient;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Scales\Application\OutputPorts\ScalesRepositoryInterface;
use App\Ttn\Application\DTO\TtnUplinkRequest;
use App\Ttn\Application\InputPorts\HandleTtnUplinkUseCaseInterface;
use App\Ttn\Application\OutputPorts\PoolTtnDeviceRepositoryInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class HandleTtnUplinkUseCase implements HandleTtnUplinkUseCaseInterface
{
    private PoolTtnDeviceRepositoryInterface $poolTtnDeviceRepository;
    private ClientConnectionManager $connectionManager;
    private ScalesRepositoryInterface $ScaleRepository;
    private LoggerInterface $logger;
    private EntityManagerInterface $mainEntityManager;
    private MailerInterface $mailer;
    private string $stockAlertEmailSender;

    public function __construct(
        PoolTtnDeviceRepositoryInterface $poolTtnDeviceRepo,
        ClientConnectionManager $connManager,
        ScalesRepositoryInterface $ScaleRepository,
        LoggerInterface $logger,
        EntityManagerInterface $mainEntityManager,
        MailerInterface $mailer,
        string $stockAlertEmailSender
    ) {
        $this->poolTtnDeviceRepository = $poolTtnDeviceRepo;
        $this->connectionManager = $connManager;
        $this->ScaleRepository = $ScaleRepository;
        $this->logger = $logger;
        $this->mainEntityManager = $mainEntityManager;
        $this->mailer = $mailer;
        $this->stockAlertEmailSender = $stockAlertEmailSender;
    }

    public function execute(TtnUplinkRequest $request): void
    {
        $devEui = $request->getDevEui(); // o deviceId
        $this->logger->info('[TTN Uplink] Iniciando execute.', [
            'devEui' => $devEui,
            'deviceId' => $request->getDeviceId(),
            'voltage' => $request->getVoltage(),
            'weight' => $request->getWeight(),
        ]);
        if (!$devEui) {
            $this->logger->error('DEV_EUI_MISSING');
            throw new \RuntimeException('DEV_EUI_MISSING');
        }
        $deviceId = $request->getDeviceId();
        if (!$deviceId) {
            $this->logger->error('DEVICE_ID_MISSING');
            throw new \RuntimeException('DEVICE_ID_MISSING');
        }
        // 1) Buscar en pool_ttn_device
        $this->logger->debug('[TTN Uplink] Buscando en pool_ttn_device', ['deviceId' => $deviceId]);
        $ttnDevice = $this->poolTtnDeviceRepository->findOneBy($deviceId);
        if (!$ttnDevice) {
            $this->logger->error('DEVICE_NOT_FOUND', ['deviceId' => $deviceId]);
            throw new \RuntimeException('DEVICE_NOT_FOUND');
        }

        $uuidClient = $ttnDevice->getEndDeviceName(); // o si guardas en otra columna
        $this->logger->debug('[TTN Uplink] uuidClient obtenido', ['uuidClient' => $uuidClient]);

        // 2) Conectarte a la BBDD del cliente
        $entityManager = $this->connectionManager->getEntityManager($uuidClient);

        $scaleRepository = $entityManager->getRepository(\App\Entity\Client\Scales::class);
        $scale = $scaleRepository->findOneBy(['end_device_id' => $deviceId]);
        if (!$scale) {
            $this->logger->error('SCALE_NOT_FOUND', ['deviceId' => $deviceId]);
            throw new \RuntimeException('SCALE_NOT_FOUND');
        }
        //porcentaje de las pilas
        $percentage = max(0, min(100, ($request->getVoltage() - 3.2) / (3.6 - 3.2) * 100));
        $this->logger->debug('[TTN Uplink] Calculado voltagePercentage', [
            'percentage' => $percentage,
        ]);

        $scale->setVoltagePercentage($percentage);
        $scale->setLastSend(new \DateTime());
        $entityManager->persist($scale);
        $this->logger->debug('[TTN Uplink] Scale guardada', ['scaleId' => $scale->getId()]);

        $entityManager->flush();

        $product = $scale->getProduct();
        if (!$product) {
            $this->logger->error('PRODUCT_NOT_FOUND', ['scaleId' => $scale->getId()]);
            throw new \RuntimeException('PRODUCT_NOT_FOUND');
        }
        // Esta línea fuerza a Doctrine a cargar todas las propiedades de $product y
        // lo convierte en un objeto real en lugar de un lazy ghost:
        $this->logger->debug('[TTN Uplink] Forzamos la inicialización de product');
        $entityManager->initializeObject($product);

        $weightRange = $product->getWeightRange();
        $this->logger->debug('[TTN Uplink] Obtenido weightRange del product', ['weightRange' => $weightRange]);

        // Buscar el último WeightsLog de esta báscula, ordenado por fecha desc
        $weightsLogRepo = $entityManager->getRepository(WeightsLog::class);
        $lastLog = $weightsLogRepo->findOneBy(
            ['scale' => $scale],       // same scale
            ['date' => 'DESC']         // order by date desc
        );

        $previousWeight = $lastLog ? $lastLog->getRealWeight() : 0.0; // si no existe, p.ej. 0.0
        $newWeight = $request->getWeight();
        $variation = abs($newWeight - $previousWeight);
        if ($variation < $weightRange) {
            // Variación por debajo del umbral => descartar
            $this->logger->info('[TTN Uplink] Variación de peso menor que el rango, se descarta.', [
                'variation' => $variation,
                'weightRange' => $weightRange,
            ]);

            return;  // o "return" si no quieres guardar
        }

        // 3) Insertar en la tabla la “medición”

        $weightLog = new WeightsLog();
        $weightLog->setScale($scale);

        $weightLog->setProduct($scale->getProduct());
        $weightLog->setDate(new \DateTime());
        $weightLog->setRealWeight($request->getWeight());
        $weightLog->setAdjustWeight($request->getWeight());
        $weightLog->setChargePercentage(0.0);
        $weightLog->setVoltage($request->getVoltage());
        $weightLog->setChargePercentage($percentage);

        $entityManager->persist($weightLog);

        $entityManager->flush();
        $this->logger->info('[TTN Uplink] WeightsLog insertado correctamente', [
            'weightsLogId' => $weightLog->getId(),
            'scaleId' => $scale->getId(),
            'productId' => $product->getId(),
        ]);

        $minimumStock = $product->getStock();
        if (null !== $minimumStock && $newWeight <= $minimumStock) {
            $this->logger->info('[TTN Uplink] Peso por debajo del stock mínimo, preparando notificación.', [
                'currentWeight' => $newWeight,
                'minimumStock' => $minimumStock,
                'productId' => $product->getId(),
            ]);

            /** @var MainClient|null $mainClient */
            $mainClient = $this->mainEntityManager->getRepository(MainClient::class)->find($uuidClient);
            if (!$mainClient) {
                $this->logger->error('[TTN Uplink] CLIENT_NOT_FOUND para notificación de stock.', [
                    'uuidClient' => $uuidClient,
                ]);

                return;
            }

            $recipientEmail = $mainClient->getCompanyEmail();
            $clientName = $mainClient->getClientName();
            $productName = $product->getName();
            $subject = sprintf('Alerta de stock bajo: %s', $productName);
            $textBody = sprintf(
                "Hola %s,\n\nLa báscula asociada al producto '%s' ha registrado un peso actual de %.2f, que se encuentra por debajo del stock mínimo configurado (%.2f).\n\nPor favor, revisa tu inventario para reponer existencias.\n\nEquipo FlexyStock",
                $clientName,
                $productName,
                $newWeight,
                $minimumStock
            );
            $htmlBody = sprintf(
                '<p>Hola %s,</p>' .
                "<p>La báscula asociada al producto <strong>%s</strong> ha registrado un peso actual de <strong>%.2f</strong>, que se encuentra por debajo del stock mínimo configurado (<strong>%.2f</strong>).</p>" .
                '<p>Por favor, revisa tu inventario para reponer existencias.</p>' .
                '<p>Equipo FlexyStock</p>',
                htmlspecialchars($clientName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                htmlspecialchars($productName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $newWeight,
                $minimumStock
            );

            $status = 'success';
            $errorMessage = null;
            $errorCode = null;
            $errorType = null;
            $sentAt = new DateTimeImmutable();

            if (!$recipientEmail) {
                $status = 'failure';
                $errorMessage = 'El cliente no tiene un correo electrónico configurado.';
                $errorType = 'missing_recipient';
                $recipientForLog = 'not-configured';
            } else {
                $recipientForLog = $recipientEmail;
                $email = (new Email())
                    ->from($this->stockAlertEmailSender)
                    ->to($recipientEmail)
                    ->subject($subject)
                    ->text($textBody)
                    ->html($htmlBody);

                try {
                    $this->mailer->send($email);
                } catch (TransportExceptionInterface $exception) {
                    $status = 'failure';
                    $errorMessage = $exception->getMessage();
                    $errorCode = $this->normalizeErrorCode($exception->getCode());
                    $errorType = get_debug_type($exception);

                    $this->logger->error('[TTN Uplink] Error enviando alerta de stock.', [
                        'exception' => $exception->getMessage(),
                        'uuidClient' => $uuidClient,
                        'productId' => $product->getId(),
                    ]);
                }
            }

            $logMail = new ClientLogMail();
            $logMail->setRecipient($recipientForLog);
            $logMail->setSubject($subject);
            $logMail->setBody($htmlBody);
            $logMail->setStatus($status);
            $logMail->setErrorMessage($errorMessage);
            $logMail->setSentAt($sentAt);
            $logMail->setAdditionalData([
                'type' => 'stock_alert',
                'uuidClient' => $uuidClient,
                'productId' => $product->getId(),
                'productName' => $productName,
                'scaleId' => $scale->getId(),
                'deviceId' => $deviceId,
                'currentWeight' => $newWeight,
                'minimumStock' => $minimumStock,
                'weightRange' => $weightRange,
            ]);
            $logMail->setErrorCode($errorCode);
            $logMail->setErrorType($errorType);

            $entityManager->persist($logMail);
            $entityManager->flush();

            $this->logger->info('[TTN Uplink] Registro de notificación de stock almacenado en log_mail.', [
                'status' => $status,
                'recipient' => $recipientForLog,
            ]);
        }

        //ahora guardar en la tabla de sacles la fecha del ultimo envío y el porcentaje de carga
    }

    private function normalizeErrorCode(int|string|null $errorCode): ?int
    {
        if (null === $errorCode) {
            return null;
        }

        if (is_int($errorCode)) {
            return 0 !== $errorCode ? $errorCode : null;
        }

        return is_numeric($errorCode) ? ((int) $errorCode ?: null) : null;
    }
}
