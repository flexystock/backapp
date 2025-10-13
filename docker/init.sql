-- Para que root pueda conectar remotamente (útil para debug)
ALTER USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'UZJIvESy5x';
FLUSH PRIVILEGES;