ALTER TABLE `log_mail`
  ADD COLUMN `error_code` INT NULL AFTER `error_message`,
  ADD COLUMN `error_type` VARCHAR(255) NULL AFTER `error_code`;