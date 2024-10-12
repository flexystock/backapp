ALTER TABLE messenger_failed_messages
    ADD COLUMN available_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER created_at;
