-- Migration: Create messenger_failed_messages table

CREATE TABLE messenger_failed_messages (
                                           id BIGINT AUTO_INCREMENT PRIMARY KEY,
                                           body LONGTEXT NOT NULL,
                                           headers LONGTEXT NOT NULL,
                                           queue_name VARCHAR(255) NOT NULL,
                                           error TEXT DEFAULT NULL,
                                           failed_at DATETIME NOT NULL,
                                           UNIQUE INDEX UNIQ_FAILED_MESSAGES_ID (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
