<?php
echo "Current directory: " . __DIR__ . "\n";
echo "Testing path to autoload: " . realpath(__DIR__ . '/../../../vendor/') . "\n";
echo "Scandir: " . scandir(__DIR__ . '/../../../vendor/')  . "\n";
$path = scandir(__DIR__ . '/../../../vendor/');
echo (var_dump($path));