CREATE TABLE report_executions (
    id INT UNSIGNED AUTO_INCREMENT NOT NULL,
    report_id INT UNSIGNED NOT NULL,
    executed_at DATETIME NOT NULL,
    status VARCHAR(20) NOT NULL,
    sended TINYINT(1) NOT NULL DEFAULT 0,
    error_message TEXT DEFAULT NULL,
    PRIMARY KEY(id),
    INDEX IDX_report_id (report_id),
    CONSTRAINT FK_report_executions_report
        FOREIGN KEY (report_id)
        REFERENCES report (id)
        ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;