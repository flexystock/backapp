ALTER TABLE `subscription_plan`
ADD COLUMN `uuid_user_creation` CHAR(36) DEFAULT NULL AFTER `max_scales`,
ADD COLUMN `datehour_creation` DATETIME DEFAULT NULL AFTER `uuid_user_creation`,
ADD COLUMN `uuid_user_modification` CHAR(36) DEFAULT NULL AFTER `datehour_creation`,
ADD COLUMN `datehour_modification` DATETIME DEFAULT NULL AFTER `uuid_user_modification`;