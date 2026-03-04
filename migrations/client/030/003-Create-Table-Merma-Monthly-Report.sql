CREATE TABLE merma_monthly_report (
                id                  INT UNSIGNED AUTO_INCREMENT NOT NULL,
                product_id          INT UNSIGNED NOT NULL,
                scale_id            INT UNSIGNED NOT NULL,
                period_month        DATE NOT NULL COMMENT 'Primer día del mes (ej: 2026-03-01)',
                input_kg            DECIMAL(10,3) NOT NULL DEFAULT 0
                    COMMENT 'Total reposiciones del mes (kg)',
                consumed_kg         DECIMAL(10,3) NOT NULL DEFAULT 0
                    COMMENT 'Total consumo en horario de servicio (kg)',
                anomaly_kg          DECIMAL(10,3) NOT NULL DEFAULT 0
                    COMMENT 'Total consumo fuera de horario (kg) — posible sustracción',
                stock_start_kg      DECIMAL(10,3) NOT NULL DEFAULT 0
                    COMMENT 'Stock al inicio del mes',
                stock_end_kg        DECIMAL(10,3) NOT NULL DEFAULT 0
                    COMMENT 'Stock al final del mes',
                expected_waste_kg   DECIMAL(10,3) NOT NULL DEFAULT 0
                    COMMENT 'Merma operativa esperada = input × (1 - rendimiento/100)',
                actual_waste_kg     DECIMAL(10,3) NOT NULL DEFAULT 0
                    COMMENT 'Merma real = input + stock_start - consumed - stock_end',
                waste_cost_euros    DECIMAL(10,2) NOT NULL DEFAULT 0
                    COMMENT 'actual_waste_kg × precio_compra del producto',
                waste_pct           DECIMAL(5,2) NOT NULL DEFAULT 0
                    COMMENT 'actual_waste_kg / input × 100',
                saved_vs_baseline   DECIMAL(10,2) NOT NULL DEFAULT 0
                    COMMENT 'Ahorro estimado vs. merma media sector (8%)',
                generated_at        DATETIME NOT NULL,
                PRIMARY KEY (id),
                UNIQUE INDEX uq_merma_report_period (product_id, scale_id, period_month),
                INDEX idx_merma_report_product (product_id),
                INDEX idx_merma_report_scale   (scale_id),
                INDEX idx_merma_report_period  (period_month),
                CONSTRAINT fk_merma_report_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                CONSTRAINT fk_merma_report_scale   FOREIGN KEY (scale_id)   REFERENCES scales(id)   ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;