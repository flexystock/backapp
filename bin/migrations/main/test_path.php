<?php
echo "Current directory: " . __DIR__ . "\n";
echo "Testing path to migrations: " . realpath(__DIR__ . '/../../../migrations/main/') . "\n";
echo "Scandir: " . scandir(__DIR__ . '/../../../migrations/main/')  . "\n";
$path = scandir(__DIR__ . '/../../../migrations/main/');
echo (var_dump($path));