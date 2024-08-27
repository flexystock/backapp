-- migrations/main/001/001.sql

CREATE TABLE IF NOT EXISTS client (
      id INT AUTO_INCREMENT PRIMARY KEY,
      uuid CHAR(36) NOT NULL,
      databaseName VARCHAR(255) NOT NULL,
      clientName VARCHAR(255) NOT NULL,
      host VARCHAR(255) NOT NULL,  -- Añadido campo host
      username VARCHAR(255) NOT NULL,  -- Añadido campo username
      password VARCHAR(255) NOT NULL,  -- Añadido campo password
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
