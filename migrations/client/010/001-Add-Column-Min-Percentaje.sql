ALTER TABLE products
ADD COLUMN min_percentage TINYINT NOT NULL DEFAULT 0 COMMENT 'Minimum product percentage' AFTER ean;
