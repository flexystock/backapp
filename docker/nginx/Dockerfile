FROM nginx:1.19

ARG UID=1000

# Crear usuario con UID especificado y sin contraseña
RUN adduser --disabled-password --gecos "" --uid ${UID} appuser

# Copia la configuración de NGINX
COPY default.conf /etc/nginx/conf.d/

# Crear directorio si no existe y asegurar que todos los archivos y directorios pertenecen al nuevo usuario
RUN mkdir -p /var/www/html && \
    chown -R appuser:appuser /var/cache/nginx /var/log/nginx /var/www/html

# Correr NGINX como el usuario appuser (esto podría requerir ajustes adicionales)
USER appuser

# Exponer el puerto estándar de NGINX (solo para documentación, NGINX escucha el puerto 80 por defecto)
EXPOSE 80