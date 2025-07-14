ALTER TABLE `subscription`
    ADD COLUMN `payment_status` VARCHAR(20) NOT NULL DEFAULT 'pending'
        AFTER `uuid_user_modification`;