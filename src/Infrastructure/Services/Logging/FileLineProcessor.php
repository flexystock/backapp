<?php

namespace App\Infrastructure\Services\Logging;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class FileLineProcessor implements ProcessorInterface
{
    public function __invoke(LogRecord $record): LogRecord
    {
        // Obtener el stack trace
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        // Buscar la primera llamada que no sea del sistema de logging
        foreach ($backtrace as $trace) {
            if (isset($trace['file']) && isset($trace['line'])) {
                $file = $trace['file'];

                // Ignorar archivos del sistema de logging
                if (false === strpos($file, 'vendor/monolog') &&
                    false === strpos($file, 'vendor/symfony') &&
                    false === strpos($file, '/Logger')) {
                    // Obtener solo el nombre del archivo (sin ruta)
                    $fileName = basename($file);

                    $record->extra['file'] = $fileName;
                    $record->extra['line'] = $trace['line'];

                    // También agregar la clase si está disponible
                    if (isset($trace['class'])) {
                        $record->extra['class'] = $trace['class'];
                    }

                    break;
                }
            }
        }

        return $record;
    }
}
