# 🛠️ BackApp

Este repositorio **BackApp** es el backend de una aplicación diseñada para la gestión eficiente de inventarios y stock a través de balanzas inteligentes.

Este proyecto es parte de una solución completa desarrollada por:

- 🙋🏻 Santiago Fragio Moreno (**Backend Developer**)
- 🙋🏻‍♂️ Sergio Correas Rayo (**Frontend Developer**)

El código fuente es privado, ya que es un software propio desarrollado de forma interna y privada.

## 🚀 Visión General

Este es el backend del proyecto, desarrollado con Symfony 5.1 y utilizando Docker para gestionar un entorno de desarrollo local eficiente. El proyecto sigue una arquitectura hexagonal, lo que asegura la separación de responsabilidades y promueve un código limpio, escalable y fácil de mantener.

## 📦 Tecnologías Utilizadas

Este backend se apoya en diversas tecnologías y herramientas para garantizar un desarrollo eficiente y un funcionamiento óptimo:

- **Symfony 5.1**: Un framework PHP que facilita el desarrollo de aplicaciones web robustas, seguras y mantenibles.
- **Docker**: Utilizado para contenerizar la aplicación, asegurando que todos los desarrolladores trabajen con el mismo entorno.
- **PHP 7.4.6**: La versión del lenguaje PHP que se utiliza en este proyecto.
- **MySQL 8.0**: Base de datos relacional que gestiona el almacenamiento y acceso a los datos.
- **Nginx 1.19**: El servidor web utilizado para manejar las peticiones HTTP.
- **JWT (JSON Web Tokens)**: Utilizado para la autenticación segura de los usuarios que interactúan con la API.
- **Makefile**: Un conjunto de comandos que automatizan las tareas de gestión de Docker y el entorno de desarrollo.

## 📂 Estructura del Repositorio

```bash
/backapp
│
│
├── .bin/                    
│
│
├── config/
│  ├── jwt/
│  ├── packages/
│  ├── routes/
│  ├── bundles.php
│  ├── preload.php
│  ├── router.yaml
│  ├── services.yaml
│
│
├── docker/
│  ├── nginx/
│  ├── php/
│
│
├── migrations/
│  ├── client/
│  ├── main/
│
│
├── public/
│  ├── bundles/
│  ├── img/
│  ├── .env
│  ├── .env.local
│  ├── index.php
│
│
├── src/
│  ├── Client/
│  ├── Controller/
│  ├── Entity/
│  ├── User/
│  ├── Kernel.php
│
│
├── templates/
│
│
├── var/
│  ├── cache/
│  ├── log/
│
│
├── .env
├── .env.local
├── .gitignore
├── composer.json
├── composer.json.lock
├── docker-compose.yml
├── docker-entrypoint.sh
├── Makefile
├── README.md
├── symfony.lock
│
│
```

## 🧑‍💻 Desarrollamos con Symfony

Symfony es un framework PHP diseñado para crear aplicaciones web robustas y escalables. Ofrece una amplia gama de herramientas y bibliotecas que facilitan el desarrollo de aplicaciones seguras y mantenibles. Entre sus características más destacadas están:

- **Enrutamiento avanzado**: Gestiona fácilmente las rutas de la aplicación.
- **ORM (Doctrine)**: Permite interactuar con bases de datos de manera eficiente utilizando el patrón de repositorios.
- **Bundles**: Facilita la reutilización de código y la integración con otras bibliotecas.
- **Seguridad**: Symfony ofrece un sistema de seguridad robusto que permite la autenticación y autorización, integrado en nuestro caso con JWT.

## 🐳 Trabajamos en un entorno Dockerizado

Docker es una plataforma que facilita la creación, despliegue y ejecución de aplicaciones en contenedores. Los contenedores permiten agrupar una aplicación con todas sus dependencias, asegurando que funcionen de la misma manera independientemente del entorno.

En nuestro proyecto, Docker se utiliza para ejecutar el servidor PHP, la base de datos MySQL y el servidor Nginx, todo en contenedores independientes. Esto simplifica la configuración del entorno, evita problemas de "funciona en mi máquina" y facilita el despliegue en diferentes entornos de producción o pruebas.


### 🧱 Contenedores en uso

### 🔐 Claves JWT de ejemplo

El directorio `config/jwt` contiene un par de claves RSA de ejemplo
(`private.pem` y `public.pem`) protegidas con la frase de contraseña
`FlexyStock`. Estas claves permiten que la autenticación JWT funcione en
entornos locales sin configuración adicional. **No utilices estas claves en
producción**; genera unas nuevas con el comando de Lexik o `openssl`
adecuado y actualiza la variable `JWT_PASSPHRASE` en tu entorno.

## 🚫 Contribución

Este repositorio es privado y no acepta contribuciones, ya que es un software propietario en desarrollo para nuestra futura empresa.
