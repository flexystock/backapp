ALTER TABLE messenger_failed_messages
    ADD COLUMN created_at DATETIME NOT NULL AFTER failed_at;