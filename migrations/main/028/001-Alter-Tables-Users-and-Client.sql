ALTER TABLE users
MODIFY COLUMN phone VARCHAR(255);

ALTER TABLE client
MODIFY COLUMN annual_sales_volume INT;
ALTER TABLE client
MODIFY COLUMN has_multiple_warehouses INT;