FROM php:8.2-fpm

ARG UID

# Usuario de app
RUN adduser --disabled-password --gecos "" --uid ${UID} appuser \
    && mkdir /home/appuser/.ssh \
    && chown -R appuser:appuser /home/appuser/ \
    && echo "StrictHostKeyChecking no" >> /home/appuser/.ssh/config \
    && echo "export COLUMNS=300" >> /home/appuser/.bashrc \
    && echo "alias sf=/appdata/www/bin/console" >> /home/appuser/.bashrc

COPY ./php.ini /usr/local/etc/php/php.ini

# Paquetes base y extensiones PHP
RUN find /etc/apt/ -name '*.list' -exec sed -i 's|http://deb.debian.org|http://ftp.es.debian.org|g' {} + && \
    apt-get update && \
    apt-get install -y --fix-missing \
        git acl openssl openssh-client wget zip vim libssh-dev \
        libpng-dev zlib1g-dev libzip-dev libxml2-dev libicu-dev && \
    docker-php-ext-install intl pdo pdo_mysql mysqli zip gd soap bcmath sockets && \
    docker-php-ext-enable mysqli && \
    docker-php-ext-enable --ini-name 05-opcache.ini opcache && \
    rm -rf /var/lib/apt/lists/*

# Composer
RUN curl -fsSL https://getcomposer.org/composer.phar -o /usr/bin/composer \
    && chmod +x /usr/bin/composer \
    && composer self-update

# PHP CS Fixer
RUN wget https://cs.symfony.com/download/php-cs-fixer-v2.phar -O php-cs-fixer \
    && chmod a+x php-cs-fixer \
    && mv php-cs-fixer /usr/local/bin/php-cs-fixer

# Directorio de trabajo
RUN mkdir -p /appdata/www \
    && chown -R appuser:appuser /appdata/www

# Instalar Docker CLI (sin apt-key, con keyring)
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends ca-certificates curl gnupg; \
    install -m 0755 -d /etc/apt/keyrings; \
    curl -fsSL https://download.docker.com/linux/debian/gpg \
      | gpg --dearmor -o /etc/apt/keyrings/docker.gpg; \
    chmod a+r /etc/apt/keyrings/docker.gpg; \
    . /etc/os-release; \
    echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/debian ${VERSION_CODENAME} stable" \
      > /etc/apt/sources.list.d/docker.list; \
    apt-get update; \
    apt-get install -y --no-install-recommends docker-ce-cli; \
    rm -rf /var/lib/apt/lists/*

# Grupo docker y añadir appuser
RUN groupadd -f docker && usermod -aG docker appuser

WORKDIR /appdata/www
