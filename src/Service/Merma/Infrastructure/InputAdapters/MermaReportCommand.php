<?php

namespace App\Service\Merma\Infrastructure\InputAdapters;

use App\Service\Merma\Application\InputPorts\MermaReportGeneratorInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * MermaReportCommand
 *
 * Genera el informe mensual de merma para todas las balanzas activas.
 *
 * Uso manual:
 *   php bin/console flexystock:merma:report
 *   php bin/console flexystock:merma:report --month=2026-02
 *
 * Cron (día 1 de cada mes a las 06:00):
 *   0 6 1 * * /var/www/html/bin/console flexystock:merma:report >> /var/log/flexystock/merma.log 2>&1
 */
#[AsCommand(
    name: 'flexystock:merma:report',
    description: 'Genera el informe mensual de merma y lo envía por email a los clientes',
)]
final class MermaReportCommand extends Command
{
    public function __construct(
        private readonly MermaReportGeneratorInterface $generator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'month', 'm',
            InputOption::VALUE_OPTIONAL,
            'Mes en formato YYYY-MM (por defecto: mes anterior)',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('FlexyStock — Informe Mensual de Merma');

        $month = null;
        if ($monthOption = $input->getOption('month')) {
            try {
                $month = new \DateTime($monthOption . '-01 00:00:00');
            } catch (\Exception) {
                $io->error('Formato inválido. Usa YYYY-MM (ej: 2026-02)');
                return Command::FAILURE;
            }
        }

        $label = $month ? $month->format('F Y') : 'mes anterior';
        $io->text("Procesando: {$label}");

        $generated = $this->generator->generateForAllScales($month);

        $io->success("{$generated} informes generados y enviados.");
        return Command::SUCCESS;
    }
}