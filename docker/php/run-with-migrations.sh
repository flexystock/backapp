#!/bin/sh
set -e

echo "Esperando a que la base de datos esté disponible..."
until php -r 'try { new PDO("mysql:host=docker-symfony-dbMain;port=3306", "user", "password"); exit(0); } catch (Exception $e) { exit(1); }' > /dev/null 2>&1; do
  echo "Base de datos no está disponible aún - esperando..."
  sleep 2
done

echo "Base de datos disponible."

if [ "$APP_ENV" = "dev" ]; then
  echo "Entorno de desarrollo detectado - ejecutando migraciones de la base de datos main..."
  php /appdata/www/migrations/main/migrate_main.php
fi

echo "Iniciando php-fpm..."
exec php-fpm
