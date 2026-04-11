# 🛠️ BackApp

Este repositorio **BackApp** es el backend de una aplicación diseñada para la gestión eficiente de inventarios y stock a través de balanzas inteligentes.

Este proyecto es parte de una solución completa desarrollada por:

- 🙋🏻 Santiago Fragio Moreno (**Backend Developer**)
- 🙋🏻‍♂️ Sergio Correas Rayo (**Frontend Developer**)

El código fuente es privado, ya que es un software propio desarrollado de forma interna y privada.

## 🚀 Visión General

Este es el backend del proyecto, desarrollado con **Symfony 6.4** y utilizando **Docker** para gestionar un entorno de desarrollo local eficiente. El proyecto sigue una arquitectura hexagonal, lo que asegura la separación de responsabilidades y promueve un código limpio, escalable y fácil de mantener.

## 📦 Tecnologías Utilizadas

Este backend se apoya en diversas tecnologías y herramientas para garantizar un desarrollo eficiente y un funcionamiento óptimo:

- **Symfony 6.4**: Un framework PHP moderno que facilita el desarrollo de aplicaciones web robustas, seguras y mantenibles.
- **PHP 8.2**: La versión del lenguaje PHP utilizada en este proyecto, con soporte para características modernas.
- **Docker**: Utilizado para contenerizar la aplicación, asegurando que todos los desarrolladores trabajen con el mismo entorno.
- **MySQL 8.0**: Base de datos relacional que gestiona el almacenamiento y acceso a los datos.
- **Nginx 1.19**: El servidor web utilizado para manejar las peticiones HTTP.
- **Doctrine ORM 3.1**: Para la gestión de bases de datos y migraciones.
- **JWT (JSON Web Tokens)**: Utilizado para la autenticación segura de los usuarios mediante Lexik JWT Authentication Bundle.
- **Symfony Messenger**: Para la gestión de colas de mensajes y tareas asíncronas.
- **PHPUnit 10.5**: Framework de testing para garantizar la calidad del código.
- **PHP CS Fixer**: Para mantener estándares de código consistentes.
- **Sentry**: Monitoreo de errores en tiempo real con notificaciones por email.
- **Makefile**: Un conjunto de comandos que automatizan las tareas de gestión de Docker y el entorno de desarrollo.

## 📂 Estructura del Repositorio

```bash
/backapp
│
├── bin/                      # Ejecutables de Symfony (console)
│
├── config/                   # Configuración de la aplicación
│  ├── jwt/                  # Claves JWT (private.pem, public.pem)
│  ├── packages/             # Configuración de bundles
│  │  └── sentry.yaml       # Configuración de Sentry
│  ├── routes/               # Definición de rutas
│  ├── bundles.php
│  ├── preload.php
│  ├── router.yaml
│  └── services.yaml
│
├── docker/                   # Configuración de Docker
│  ├── nginx/                # Configuración de Nginx
│  │  ├── Dockerfile
│  │  └── default.conf
│  └── php/                  # Configuración de PHP-FPM
│     ├── Dockerfile
│     ├── php.ini
│     ├── run-with-migrations.sh
│     └── wait-for-db.sh
│
├── migrations/               # Migraciones de base de datos
│  ├── client/               # Migraciones específicas de clientes
│  └── main/                 # Migraciones de la base de datos principal
│
├── public/                   # Punto de entrada web
│  ├── bundles/
│  ├── img/
│  └── index.php
│
├── src/                      # Código fuente de la aplicación
│  ├── Admin/                # Funcionalidad de administración
│  ├── Alarm/                # Sistema de alarmas
│  ├── Client/               # Gestión de clientes
│  ├── Controller/           # Controladores
│  ├── Dashboard/            # Panel de control
│  ├── Entity/               # Entidades Doctrine
│  ├── Event/                # Eventos del sistema
│  ├── EventListener/        # Listeners de eventos
│  ├── EventSubscriber/      # Subscribers de eventos
│  ├── Infrastructure/       # Capa de infraestructura
│  ├── Logger/               # Sistema de logging
│  ├── Message/              # Mensajes Symfony Messenger
│  ├── MessageHandler/       # Manejadores de mensajes
│  ├── Product/              # Gestión de productos
│  ├── Profile/              # Perfiles de usuario
│  ├── Scales/               # Gestión de balanzas
│  ├── Security/             # Seguridad y autenticación
│  ├── Service/              # Servicios de aplicación
│  ├── Stripe/               # Integración con Stripe
│  ├── Subscription/         # Sistema de suscripciones
│  ├── Ttn/                  # Integración con The Things Network
│  ├── User/                 # Gestión de usuarios
│  ├── WeightAnalytics/      # Analíticas de peso
│  └── Kernel.php
│
├── templates/                # Plantillas Twig
│
├── tests/                    # Tests unitarios y funcionales
│  ├── Product/
│  ├── Traits/
│  ├── User/
│  ├── DatabaseTest.php
│  └── bootstrap.php
│
├── var/                      # Archivos temporales
│  ├── cache/                # Cache de la aplicación
│  └── log/                  # Logs de la aplicación
│
├── .env                      # Variables de entorno base
├── .env.test                 # Variables de entorno para tests
├── .gitignore
├── .php-cs-fixer.dist.php   # Configuración de PHP CS Fixer
├── composer.json             # Dependencias PHP
├── composer.lock
├── docker-compose.yml        # Configuración principal de Docker
├── docker-compose.integration.yml  # Configuración para tests de integración
├── Makefile                  # Comandos automatizados
├── phpunit.xml.dist          # Configuración de PHPUnit
├── README.md
└── symfony.lock
```

## 🧑‍💻 Desarrollamos con Symfony

Symfony es un framework PHP diseñado para crear aplicaciones web robustas y escalables. Ofrece una amplia gama de herramientas y bibliotecas que facilitan el desarrollo de aplicaciones seguras y mantenibles. Entre sus características más destacadas están:

- **Enrutamiento avanzado**: Gestiona fácilmente las rutas de la aplicación.
- **ORM (Doctrine)**: Permite interactuar con bases de datos de manera eficiente utilizando el patrón de repositorios.
- **Bundles**: Facilita la reutilización de código y la integración con otras bibliotecas.
- **Seguridad**: Symfony ofrece un sistema de seguridad robusto que permite la autenticación y autorización, integrado en nuestro caso con JWT.
- **Messenger Component**: Sistema de colas para procesamiento asíncrono de tareas.
- **Dependency Injection**: Contenedor de servicios potente para gestión de dependencias.

## 🐳 Trabajamos en un entorno Dockerizado

Docker es una plataforma que facilita la creación, despliegue y ejecución de aplicaciones en contenedores. Los contenedores permiten agrupar una aplicación con todas sus dependencias, asegurando que funcionen de la misma manera independientemente del entorno.

En nuestro proyecto, Docker se utiliza para ejecutar el servidor PHP, la base de datos MySQL y el servidor Nginx, todo en contenedores independientes. Esto simplifica la configuración del entorno, evita problemas de "funciona en mi máquina" y facilita el despliegue en diferentes entornos de producción o pruebas.

### 🧱 Contenedores en uso

El proyecto utiliza los siguientes contenedores Docker:

1. **docker-symfony-web** (Nginx 1.19)
   - Puerto expuesto: 300:80
   - Sirve la aplicación web
   - Depende del contenedor backend

2. **docker-symfony-be** (PHP 8.2-FPM)
   - Contenedor principal del backend
   - Ejecuta migraciones automáticamente en entorno de desarrollo
   - Incluye Composer, PHP CS Fixer y extensiones PHP necesarias
   - Volúmenes montados: código fuente, claves SSH

3. **docker-symfony-messenger** (PHP 8.2-FPM)
   - Procesa tareas asíncronas mediante Symfony Messenger
   - Consume mensajes de la cola `async`
   - Se reinicia automáticamente si falla

4. **docker-symfony-dbMain** (MySQL 8.0)
   - Base de datos principal
   - Puerto expuesto: 40099:3306
   - Zona horaria: Europe/Madrid
   - Modo SQL: STRICT_ALL_TABLES, NO_ENGINE_SUBSTITUTION

### 🔧 Comandos del Makefile

El proyecto incluye un `Makefile` con comandos útiles para gestionar el entorno de desarrollo:

```bash
make help              # Muestra todos los comandos disponibles
make run               # Inicia todos los contenedores y configura el entorno
make stop              # Detiene los contenedores
make clean             # Limpia contenedores, volúmenes y redes
make restart           # Reinicia completamente el entorno (clean + run)
make build             # Reconstruye todos los contenedores
make prepare           # Prepara el entorno ejecutando comandos necesarios
make composer-install  # Instala las dependencias de Composer
make logs              # Muestra los logs de desarrollo de Symfony
make ssh-be            # Accede al contenedor backend mediante SSH
make ssh-messenger     # Accede al contenedor messenger mediante SSH
make code-style        # Arregla el estilo de código según reglas de Symfony
make fix-permissions   # Corrige permisos de var/log y var/cache
```

## ⚙️ Configuración del Entorno

### Variables de Entorno Principales

El archivo `.env` contiene las variables de configuración esenciales:

**Symfony Framework:**
- `APP_ENV`: Entorno de la aplicación (dev, prod, test)
- `APP_DEBUG`: Activar/desactivar modo debug
- `APP_SECRET`: Clave secreta de la aplicación
- `FRONTEND_BASE_URL`: URL del frontend (por defecto: http://localhost:5173)

**Base de Datos:**
- `DATABASE_URL`: mysql://user:password@docker-symfony-dbMain:3306/docker_symfony_databaseMain?serverVersion=8.0

**JWT Authentication:**
- `JWT_SECRET_KEY`: Ruta a la clave privada (%kernel.project_dir%/config/jwt/private.pem)
- `JWT_PUBLIC_KEY`: Ruta a la clave pública (%kernel.project_dir%/config/jwt/public.pem)
- `JWT_PASSPHRASE`: FlexyStock (⚠️ cambiar en producción)

**Symfony Messenger:**
- `MESSENGER_TRANSPORT_DSN`: doctrine://default?auto_setup=0

**The Things Network (TTN):**
- `TTN_API_BASE_URL`: https://eu1.cloud.thethings.network/api/v3
- `TTN_APPLICATION_ID`: ID de la aplicación TTN
- `TTN_NETWORK_SERVER_ADDRESS`: eu1.cloud.thethings.network
- `TTN_LORAWAN_VERSION`: MAC_V1_0_2
- `TTN_FREQUENCY_PLAN_ID`: EU_863_870_TTN

**Stripe (Pagos):**
- `STRIPE_PUBLIC_KEY`: Clave pública de Stripe
- `STRIPE_SECRET_KEY`: Clave secreta de Stripe
- `STRIPE_WEBHOOK_SECRET`: Secret para webhooks de Stripe

**Email y URLs:**
- `MAILER_DSN`: Configuración SMTP para envío de emails
- `MAILER_FROM`: Email remitente para notificaciones
- `APP_URL`: URL del frontend para los enlaces en emails (ej: `https://int.app.flexystock.app`)
- `SENTRY_DSN`: DSN de Sentry para monitoreo de errores (dejar vacío en local)

**Lock System:**
- `LOCK_DSN`: flock (sistema de bloqueos)

### Variables por entorno

| Variable | Local (dev) | Integración | Producción |
|----------|------------|-------------|------------|
| `APP_URL` | `http://localhost:3000` | `https://int.app.flexystock.app` | `https://app.flexystock.app` |
| `FRONTEND_BASE_URL` | `http://localhost:5173` | `https://int.app.flexystock.app` | `https://app.flexystock.app` |
| `SENTRY_DSN` | *(vacío)* | `https://xxx@sentry.io/yyy` | `https://xxx@sentry.io/zzz` |

### 🔐 Claves JWT de ejemplo

El directorio `config/jwt` contiene un par de claves RSA de ejemplo (`private.pem` y `public.pem`) protegidas con la frase de contraseña `FlexyStock`. Estas claves permiten que la autenticación JWT funcione en entornos locales sin configuración adicional.

**⚠️ IMPORTANTE: No utilices estas claves en producción**. Genera unas nuevas con el siguiente comando:

```bash
# Desde el contenedor backend
docker exec -it docker-symfony-be bash
php bin/console lexik:jwt:generate-keypair
```

O con OpenSSL:

```bash
openssl genrsa -out config/jwt/private.pem -aes256 4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

Actualiza la variable `JWT_PASSPHRASE` en tu archivo `.env.local` con tu nueva contraseña.

## 🚀 Inicio Rápido

### Requisitos Previos

- Docker y Docker Compose instalados
- Git
- Puerto 300 disponible (Nginx)
- Puerto 40099 disponible (MySQL)

### Instalación y Ejecución

1. **Clonar el repositorio:**
```bash
git clone <repository-url>
cd backapp
```

2. **Crear el archivo `.env.local`:**
```bash
cp .env .env.local
# Editar .env.local con tus configuraciones específicas
```

3. **Iniciar el entorno con Docker:**
```bash
make run
```

Este comando:
- Crea la red Docker si no existe
- Levanta todos los contenedores
- Configura permisos de var/log
- Instala dependencias de Composer
- Ejecuta migraciones automáticamente (en modo dev)

4. **Verificar que todo funciona:**
```bash
# Ver logs
make logs

# Acceder al contenedor backend
make ssh-be
```

La aplicación estará disponible en: `http://localhost:300`

### Detener el entorno

```bash
make stop    # Detiene los contenedores
make clean   # Limpia todo (contenedores, volúmenes, redes)
```

## 🗄️ Gestión de Base de Datos

### Migraciones

El proyecto utiliza un sistema personalizado de migraciones ubicado en el directorio `migrations/`:

- **migrations/main/**: Migraciones de la base de datos principal
- **migrations/client/**: Migraciones específicas de cada cliente

Las migraciones se ejecutan automáticamente al iniciar el contenedor backend en modo desarrollo mediante el script `docker/php/run-with-migrations.sh`.

### Conexión a la Base de Datos

Desde tu máquina local:
```bash
mysql -h 127.0.0.1 -P 40099 -u user -ppassword docker_symfony_databaseMain
```

Desde el contenedor:
```bash
docker exec -it docker-symfony-dbMain mysql -u user -ppassword docker_symfony_databaseMain
```

### Comandos Útiles de Doctrine

```bash
# Acceder al contenedor
make ssh-be

# Ver estado de las migraciones de Doctrine
php bin/console doctrine:migrations:status

# Crear una nueva migración
php bin/console doctrine:migrations:generate

# Ejecutar migraciones pendientes
php bin/console doctrine:migrations:migrate

# Ver esquema de base de datos
php bin/console doctrine:schema:update --dump-sql
```

## 🧪 Testing

El proyecto utiliza **PHPUnit 10.5** para testing. La configuración se encuentra en `phpunit.xml.dist`.

### Estructura de Tests

```
tests/
├── DatabaseTest.php      # Tests de conectividad de base de datos
├── Product/              # Tests relacionados con productos
├── User/                 # Tests relacionados con usuarios
├── Traits/               # Traits reutilizables para tests
└── bootstrap.php         # Bootstrap de PHPUnit
```

### Configuración de Tests

El archivo `.env.test` contiene las variables de entorno específicas para el entorno de testing:
- Base de datos de test separada
- Configuración JWT para tests
- Variables de Stripe y otros servicios

### Ejecutar Tests

```bash
# Acceder al contenedor backend
make ssh-be

# Ejecutar todos los tests
./vendor/bin/phpunit

# Ejecutar un test específico
./vendor/bin/phpunit tests/DatabaseTest.php

# Ejecutar un suite específico
./vendor/bin/phpunit --testsuite "Database Tests"

# Con modo verbose
./vendor/bin/phpunit --verbose

# Con cobertura de código (requiere Xdebug)
./vendor/bin/phpunit --coverage-html var/coverage
```

### Suites de Tests Disponibles

- **All Tests**: Ejecuta todos los tests del directorio `tests/`
- **Database Tests**: Tests específicos de conectividad y operaciones de base de datos

## 🔄 Symfony Messenger (Colas Asíncronas)

El proyecto utiliza Symfony Messenger para procesar tareas de forma asíncrona. El contenedor `docker-symfony-messenger` está dedicado a consumir mensajes de las colas.

### Configuración

- **Transport**: Doctrine (mensajes almacenados en la base de datos)
- **Queue**: `async`
- **Auto-setup**: Desactivado

### Ver Mensajes en Cola

```bash
make ssh-messenger

# Ver mensajes pendientes
php bin/console messenger:stats

# Consumir mensajes manualmente
php bin/console messenger:consume async -vv
```

### Detener el Worker

```bash
docker stop docker-symfony-messenger
```

## 📅 Sistema de Informes Programados

El proyecto incluye un sistema completo de generación y envío automático de informes de inventario mediante tareas programadas (cron).

### Características del Sistema

- **Generación automática** de informes de stock en horarios programados
- **Tres períodos disponibles**: Diario, Semanal, Mensual
- **Dos formatos**: CSV y PDF
- **Filtros configurables**: Todos los productos o solo productos bajo stock mínimo
- **Envío por email** automático con el informe adjunto
- **Multi-tenant**: Funciona para todos los clientes de forma independiente
- **Registro de ejecuciones** para auditoría y seguimiento
- **Prevención de duplicados** mediante control de ejecuciones por período

### Endpoints API

```bash
POST /api/report_create       # Crear informe programado
GET  /api/reports             # Listar todos los informes
GET  /api/report/{id}         # Obtener un informe específico
PUT  /api/report_update       # Actualizar un informe
DELETE /api/report_delete/{id} # Eliminar un informe
POST /api/report/generate-now  # Generar informe inmediatamente
```

### Tipos de Informes

- **Diario**: Stock actual vs stock de ayer
- **Semanal**: Stock de los últimos 7 días (columnas por día)
- **Mensual**: Stock de los últimos 30 días (columnas por día)

### Formatos Disponibles

- **CSV**: Para importar en Excel o Google Sheets
- **PDF**: Generado con Dompdf, usando plantillas Twig

### Zona Horaria

⚠️ **Importante**: Los contenedores Docker funcionan en UTC. Si tu zona horaria es diferente, ajusta el campo `send_time` en consecuencia.

Ejemplo para España (UTC+1 en invierno, UTC+2 en verano):
- Para enviar a las 08:00 hora local en invierno: `send_time = '07:00:00'`
- Para enviar a las 08:00 hora local en verano: `send_time = '06:00:00'`

## 🚨 Monitoreo de Errores (Sentry)

El proyecto integra **Sentry** para la captura y notificación automática de errores en tiempo real.

### Configuración

```bash
# .env (dev local — vacío para no enviar errores locales)
SENTRY_DSN=

# .env.local del servidor de integración
SENTRY_DSN="https://xxx@sentry.io/yyy"

# .env.local del servidor de producción
SENTRY_DSN="https://xxx@sentry.io/zzz"
```

La configuración del bundle se encuentra en `config/packages/sentry.yaml`.

### Qué errores captura

- Excepciones no capturadas (errores 500)
- Fallos en `HandleTtnUplinkUseCase` (uplinks de balanzas IoT)
- Errores en `MermaNotifier` (emails de anomalías de merma)
- Fallos en los handlers de Symfony Messenger
- Cualquier error crítico en los use cases

### Qué ignora

- `NotFoundHttpException` (404 — rutas inexistentes)
- `AccessDeniedException` (403 — sin permisos)
- `ExpiredTokenException` (tokens JWT caducados — comportamiento normal)

### Activación por entorno

Sentry solo está activo cuando `SENTRY_DSN` tiene un valor. En local basta con dejarlo vacío. Cada entorno (integración, producción) tiene su propio proyecto en sentry.io para mantener las alertas separadas.

### Comandos útiles

```bash
# Test manual de conexión con Sentry
php bin/console sentry:test

# Panel web
# https://sentry.io → proyecto flexystock-integration
```

## 🌐 Integración con The Things Network (TTN)

El proyecto incluye integración completa con **The Things Network** para gestión de dispositivos LoRaWAN y balanzas IoT.

### Configuración TTN

```bash
TTN_API_BASE_URL="https://eu1.cloud.thethings.network/api/v3"
TTN_APPLICATION_ID="pruebas-flexystock"
TTN_NETWORK_SERVER_ADDRESS="eu1.cloud.thethings.network"
TTN_LORAWAN_VERSION="MAC_V1_0_2"
TTN_LORAWAN_PHY_VERSION="PHY_V1_0_2_REV_B"
TTN_FREQUENCY_PLAN_ID="EU_863_870_TTN"
```

### Características

- Registro y gestión de dispositivos LoRaWAN
- Recepción y procesamiento de datos de sensores (uplinks)
- Detección de anomalías de merma fuera de horario
- Notificaciones por email al detectar variaciones inesperadas de peso
- Gestión de aplicaciones TTN
- Soporte para balanzas inteligentes Heltec CubeCell + HX711

## 💳 Integración con Stripe

El proyecto incluye integración con **Stripe** para procesamiento de pagos y gestión de suscripciones.

### Configuración

```bash
STRIPE_PUBLIC_KEY="pk_test_..."
STRIPE_SECRET_KEY="sk_test_..."
STRIPE_WEBHOOK_SECRET="whsec_..."
```

⚠️ **Nota**: Las claves mostradas son de prueba. En producción, usa claves reales de Stripe.

## 📝 Logs y Monitoreo

### Ubicación de Logs

- **Logs de Symfony**: `/appdata/www/var/log/`
- **Logs de Docker**: Configurados con rotación (max-size: 10m, max-file: 2)
- **Errores en tiempo real**: Panel de Sentry (sentry.io)

### Ver Logs

```bash
# Logs de Symfony
make logs

# Logs del contenedor
docker logs docker-symfony-be
docker logs docker-symfony-messenger
docker logs docker-symfony-dbMain
```

## ⚠️ Troubleshooting

### Problemas de Permisos

```bash
make fix-permissions
```

### Contenedores no inician

```bash
# Verificar estado
docker ps -a

# Ver logs de error
docker logs docker-symfony-be

# Reiniciar completamente
make restart
```

### Base de datos no conecta

```bash
# Verificar que el contenedor está corriendo
docker ps | grep dbMain

# Probar conexión
docker exec -it docker-symfony-be php -r "new PDO('mysql:host=docker-symfony-dbMain;port=3306', 'user', 'password');"
```

### Messenger no procesa mensajes

```bash
# Ver logs
docker logs docker-symfony-messenger

# Reiniciar manualmente
docker restart docker-symfony-messenger
```

### Los emails de anomalía apuntan a localhost

Verificar que `APP_URL` está correctamente definido en el `.env.local` del servidor:

```bash
grep APP_URL .env.local
# Debe mostrar: APP_URL=https://int.app.flexystock.app
```

### Errores no llegan a Sentry

```bash
# Verificar que el DSN está configurado
grep SENTRY_DSN .env.local

# Test de conexión
php bin/console sentry:test
```

### Ver errores recientes en Sentry

Acceder al panel web en sentry.io → proyecto correspondiente → Issues.

## 🔒 Seguridad

### Autenticación JWT

- Tokens JWT para autenticación stateless
- Refresh tokens para renovación
- Roles y permisos mediante Symfony Security

### Mejores Prácticas

- Variables sensibles en `.env.local` (no commiteadas)
- Claves JWT únicas por entorno
- Validación de entrada en todos los endpoints
- Protección CORS configurada
- Rate limiting disponible
- Monitoreo de errores con Sentry

## 📚 Recursos Adicionales

- [Documentación de Symfony 6.4](https://symfony.com/doc/6.4/index.html)
- [Doctrine ORM](https://www.doctrine-project.org/projects/orm.html)
- [Lexik JWT Authentication Bundle](https://github.com/lexik/LexikJWTAuthenticationBundle)
- [Symfony Messenger](https://symfony.com/doc/current/messenger.html)
- [The Things Network](https://www.thethingsnetwork.org/docs/)
- [Stripe API Documentation](https://stripe.com/docs/api)
- [Sentry para Symfony](https://docs.sentry.io/platforms/php/guides/symfony/)

## 🚫 Contribución

Este repositorio es privado y no acepta contribuciones, ya que es un software propietario en desarrollo para nuestra futura empresa.
