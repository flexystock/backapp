<?php
// src/Command/CheckAlarmsCommand.php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:check-alarms',
    description: 'Verifica las alarmas de los productos y envía notificaciones si es necesario.'
)]
class CheckAlarmsCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Ejecutando verificación de alarmas...');
        // Aquí añade la lógica para revisar la base de datos central y disparar las notificaciones.
        $output->writeln('Verificación de alarmas completada.');
        return Command::SUCCESS;
    }
}
