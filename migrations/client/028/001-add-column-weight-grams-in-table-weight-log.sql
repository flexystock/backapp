ALTER TABLE weights_log
            ADD COLUMN weight_grams INT UNSIGNED NULL
            COMMENT "Peso en gramos (precisión 1g)"
            AFTER real_weight