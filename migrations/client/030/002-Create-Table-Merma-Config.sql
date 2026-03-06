CREATE TABLE merma_config (
                id                       INT UNSIGNED AUTO_INCREMENT NOT NULL,
                product_id               INT UNSIGNED NOT NULL,
                rendimiento_esperado_pct TINYINT UNSIGNED NOT NULL DEFAULT 80
                    COMMENT 'De cada kg comprado, qué % llega al plato (0-100)',
                service_start            TIME NOT NULL DEFAULT '09:00:00'
                    COMMENT 'Inicio horario de servicio',
                service_end              TIME NOT NULL DEFAULT '23:59:00'
                    COMMENT 'Fin horario de servicio',
                anomaly_threshold_kg     DECIMAL(5,3) NOT NULL DEFAULT 0.200
                    COMMENT 'Delta mínimo para considerar evento (evita ruido sensor)',
                alert_on_anomaly         TINYINT(1) NOT NULL DEFAULT 1
                    COMMENT 'Enviar email al detectar anomalía fuera de horario',
                created_at               DATETIME NOT NULL,
                updated_at               DATETIME NOT NULL,
                PRIMARY KEY (id),
                UNIQUE INDEX uq_merma_config_product (product_id),
                CONSTRAINT fk_merma_config_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;