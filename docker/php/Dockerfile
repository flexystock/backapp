FROM php:8.2-fpm

# Argumento para el UID del usuario
ARG UID

# Crear el usuario con el UID especificado y configurar su directorio de inicio
RUN adduser --disabled-password --gecos "" --uid ${UID} appuser \
    && mkdir /home/appuser/.ssh \
    && chown -R appuser:appuser /home/appuser/ \
    && echo "StrictHostKeyChecking no" >> /home/appuser/.ssh/config \
    && echo "export COLUMNS=300" >> /home/appuser/.bashrc \
    && echo "alias sf=/appdata/www/bin/console" >> /home/appuser/.bashrc

# Copiar archivo php.ini
COPY ./php.ini /usr/local/etc/php/php.ini

# Instalar dependencias y extensiones de PHP
RUN apt-get update \
    && apt-get install -y git acl openssl openssh-client wget zip vim libssh-dev \
    && apt-get install -y libpng-dev zlib1g-dev libzip-dev libxml2-dev libicu-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip gd soap bcmath sockets \
    && pecl install xdebug \
    && docker-php-ext-enable --ini-name 05-opcache.ini opcache xdebug

# Instalar Composer
RUN curl https://getcomposer.org/composer.phar -o /usr/bin/composer \
    && chmod +x /usr/bin/composer \
    && composer self-update

# Instalar PHP CS Fixer
RUN wget https://cs.symfony.com/download/php-cs-fixer-v2.phar -O php-cs-fixer \
    && chmod a+x php-cs-fixer \
    && mv php-cs-fixer /usr/local/bin/php-cs-fixer

# Crear directorio de trabajo y establecer permisos
RUN mkdir -p /appdata/www \
    && chown -R appuser:appuser /appdata/www

# Establecer el directorio de trabajo
WORKDIR /appdata/www

# Cambiar al usuario creado
USER appuser