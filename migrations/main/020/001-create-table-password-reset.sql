CREATE TABLE password_reset (
    id CHAR(36) NOT NULL PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Opcional: Añadir clave foránea si corresponde
ALTER TABLE password_reset
ADD CONSTRAINT fk_password_reset_user_email FOREIGN KEY (email) REFERENCES users(email) ON DELETE CASCADE;
