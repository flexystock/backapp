#!/bin/bash

CLIENT_NAME=$1
PORT=$2

# Convertir el nombre del cliente a mayúsculas para el nombre del esquema
SCHEME_NAME=$(echo "${CLIENT_NAME^^}_DATABASE_URL")

# Crear configuración del contenedor
CONTAINER_CONFIG=$(cat <<EOL

  docker-symfony-db${CLIENT_NAME^}:
    container_name: docker-symfony-db${CLIENT_NAME^}
    image: mysql:8.0
    ports:
      - "${PORT}:3306"
    environment:
      MYSQL_DATABASE: docker_symfony_database${CLIENT_NAME^}
      MYSQL_USER: user
      MYSQL_PASSWORD: password
      MYSQL_ROOT_PASSWORD: root
    command: mysqld --sql_mode="STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION"
    volumes:
      - docker-symfony-database${CLIENT_NAME^}-data:/var/lib/mysql
    networks:
      - docker-symfony-network
EOL
)

# Crear declaración del volumen
VOLUME_CONFIG=$(cat <<EOL
  docker-symfony-database${CLIENT_NAME^}-data:
EOL
)

# Asegúrate de que los archivos temporales se crean con permisos correctos
touch /appdata/www/docker-compose.yml.tmp
chmod 777 /appdata/www/docker-compose.yml.tmp

# Añadir la configuración del contenedor al archivo docker-compose.yml bajo la clave 'services'
awk -v config="$CONTAINER_CONFIG" '
  /^services:/ { print; print config; next }
  { print }
' /appdata/www/docker-compose.yml > /appdata/www/docker-compose.yml.tmp && mv /appdata/www/docker-compose.yml.tmp /appdata/www/docker-compose.yml

# Asegúrate de que los archivos temporales se crean con permisos correctos
touch /appdata/www/docker-compose.yml.tmp
chmod 777 /appdata/www/docker-compose.yml.tmp

# Añadir la declaración del volumen al archivo docker-compose.yml bajo la clave 'volumes'
awk -v config="$VOLUME_CONFIG" '
  /^volumes:/ { print; print config; next }
  { print }
' /appdata/www/docker-compose.yml > /appdata/www/docker-compose.yml.tmp && mv /appdata/www/docker-compose.yml.tmp /appdata/www/docker-compose.yml

# Asegúrate de que el archivo .env tiene permisos de escritura
chmod 777 /appdata/www/.env

# Añadir la URL de la base de datos al archivo .env
echo "${SCHEME_NAME}=\"mysql://user:password@docker-symfony-db${CLIENT_NAME^}:${PORT}/docker_symfony_database${CLIENT_NAME^}?serverVersion=8.0\"" >> /appdata/www/.env

# Levantar el nuevo contenedor
docker compose up -d docker-symfony-db${CLIENT_NAME^}
