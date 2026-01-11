-- 建立 tracer 資料表用於追蹤 fmd* 資料的修改歷史
-- 執行方式: mysql -u username -p database_name < create_tracer_table.sql

CREATE TABLE IF NOT EXISTS `tracer` (
    `tracer01` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主鍵',
    `tracer02` VARCHAR(50) NOT NULL COMMENT '資料表名稱',
    `tracer03` VARCHAR(50) NOT NULL COMMENT '記錄ID',
    `tracer04` VARCHAR(20) NOT NULL COMMENT '操作類型 (INSERT/UPDATE/DELETE)',
    `tracer05` TEXT NULL COMMENT '舊資料 (JSON)',
    `tracer06` TEXT NULL COMMENT '新資料 (JSON)',
    `tracer07` INT(11) NULL COMMENT '操作人員ID',
    `tracer08` DATETIME NOT NULL COMMENT '操作時間',
    `tracer09` VARCHAR(255) NULL COMMENT '備註',
    PRIMARY KEY (`tracer01`),
    INDEX `idx_tracer02` (`tracer02`),
    INDEX `idx_tracer03` (`tracer03`),
    INDEX `idx_tracer04` (`tracer04`),
    INDEX `idx_tracer07` (`tracer07`),
    INDEX `idx_tracer08` (`tracer08`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='資料修改追蹤表';
