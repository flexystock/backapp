<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

// Cargar variables de entorno para tests
if (file_exists(dirname(__DIR__).'/.env.test')) {
    (new Dotenv())->loadEnv(dirname(__DIR__).'/.env.test');
}