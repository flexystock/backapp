# backapp
repositorio para la parte de back de la Aplicación

1. Descargar del repositorio
2. Comprobar que se tiene instalado el paquete para poder ejecutar comando Make en linux
3. Situarse en la raíz del proyecto
4. Verificar que se puede ejecutar el comando 'make' desde la raíz del proyecto y que sale el listado de comandos disponibles en el archivo MakeFile del proyecto
5. Si es la primera vez que se trabaja en este repositorio, ejecutar: 'make build' para crear la imagenes de los contenedores
6. Levantar los contenedores con el comando 'make run'
7. Una vez que esén levantados los contenedores, comprobar que se puede acceder desde el Workbench a las BBDD tanto la BBDD main como las Bases de Datos de los clientes.
8. Ejecutar las migraciones tanto en Main como en los clientes:
    - Accedemos al contenedor desde la terminal con el comando 'make ssh-be'
    - Situarse en /migrations/main y ejecutar 'php migrate_main.php'
    - Situarse en /migrations/client y ejecutar 'php migrate_client.php'
