ALTER TABLE scales
    ADD CONSTRAINT fk_scales_pool_scales
    FOREIGN KEY (uuid) REFERENCES pool_scales(uuid)
    ON DELETE CASCADE ON UPDATE CASCADE;
