<?php

namespace App\DependencyInjection\Compiler;

use App\Logger\DoctrineSQLLogger;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DoctrineSQLLoggerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(DoctrineSQLLogger::class)) {
            return;
        }

        $loggerDefinition = $container->getDefinition(DoctrineSQLLogger::class);

        // Iterar sobre todas las conexiones DBAL y asignar el SQLLogger
        foreach ($container->findTaggedServiceIds('doctrine.dbal.connection') as $id => $tags) {
            $connectionConfigId = $id.'.config';
            if ($container->hasDefinition($connectionConfigId)) {
                $configDefinition = $container->getDefinition($connectionConfigId);
                $configDefinition->addMethodCall('setSQLLogger', [new Reference(DoctrineSQLLogger::class)]);
            }
        }
    }
}
