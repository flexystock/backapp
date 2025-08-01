ALTER TABLE `subscription`
ADD COLUMN `uuid_user_creation` CHAR(36) DEFAULT NULL AFTER `is_active`,
ADD COLUMN `uuid_user_modification` CHAR(36) DEFAULT NULL AFTER `created_at`;