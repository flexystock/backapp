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
    && docker-php-ext-install intl pdo pdo_mysql mysqli zip gd soap bcmath sockets \
    && docker-php-ext-enable mysqli\
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

# Instalar Docker
RUN apt-get update && \
    apt-get install -y apt-transport-https ca-certificates curl gnupg lsb-release && \
    curl -fsSL https://download.docker.com/linux/debian/gpg | apt-key add - && \
    echo "deb [arch=amd64] https://download.docker.com/linux/debian $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null && \
    apt-get update && \
    apt-get install -y docker-ce-cli

# Crear grupo docker y añadir appuser al grupo
RUN groupadd -f docker \
    && usermod -aG docker appuser

WORKDIR /appdata/www
