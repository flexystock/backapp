#!/bin/sh

# Reemplazar variables de entorno en la configuración de Nginx
envsubst '$PHP_FPM_HOST' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

# Ejecutar el comando dado (nginx en este caso)
exec "$@"
