<?php

declare(strict_types=1);

namespace App\ControlPanel\Ttn\Infrastructure\OutputAdapters\Services;

use App\ControlPanel\Ttn\Application\OutputPorts\TtnApiServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TtnApiService implements TtnApiServiceInterface
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private string $ttnApiBaseUrl;
    private string $ttnApiKey;
    private string $ttnApplicationId;

    public function __construct(
        HttpClientInterface $httpClient,
        LoggerInterface $logger,
        string $ttnApiBaseUrl,
        string $ttnApiKey,
        string $ttnApplicationId
    ) {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->ttnApiBaseUrl = $ttnApiBaseUrl;
        $this->ttnApiKey = $ttnApiKey;
        $this->ttnApplicationId = $ttnApplicationId;
    }

    public function deleteDevice(string $endDeviceId): bool
    {
        try {
            $url = sprintf(
                '%s/applications/%s/devices/%s',
                $this->ttnApiBaseUrl,
                $this->ttnApplicationId,
                $endDeviceId
            );

            $this->logger->info("Deleting device from TTN: {$url}");

            $response = $this->httpClient->request('DELETE', $url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->ttnApiKey,
                ],
            ]);

            $statusCode = $response->getStatusCode();

            if (Response::HTTP_OK === $statusCode) {
                $this->logger->info("Successfully deleted device from TTN: {$endDeviceId}");

                return true;
            }

            $this->logger->error("Failed to delete device from TTN. Status code: {$statusCode}");

            return false;
        } catch (HttpExceptionInterface | TransportExceptionInterface $e) {
            $this->logger->error("HTTP/Transport exception while deleting device from TTN: {$e->getMessage()}");

            return false;
        } catch (\Exception $e) {
            $this->logger->error("Unexpected exception while deleting device from TTN: {$e->getMessage()}");

            return false;
        }
    }
}
