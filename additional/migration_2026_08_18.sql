-- ==========================================
-- MIGRATION: Add security features and new columns
-- Date: 2026-08-18
-- ==========================================

-- ==========================================
-- 1. Add new columns to users table (skip existing ones)
-- ==========================================

-- Add computer_number column (if not exists)
SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS 
               WHERE table_schema = DATABASE() 
               AND table_name = 'users' 
               AND column_name = 'computer_number');
SET @query := IF(@exist = 0, 
                 'ALTER TABLE `users` ADD COLUMN `computer_number` VARCHAR(50) NULL AFTER `password`', 
                 'SELECT "computer_number column already exists"');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add mobile column (if not exists)
SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS 
               WHERE table_schema = DATABASE() 
               AND table_name = 'users' 
               AND column_name = 'mobile');
SET @query := IF(@exist = 0, 
                 'ALTER TABLE `users` ADD COLUMN `mobile` VARCHAR(15) NULL AFTER `computer_number`', 
                 'SELECT "mobile column already exists"');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add is_active column (if not exists)
SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS 
               WHERE table_schema = DATABASE() 
               AND table_name = 'users' 
               AND column_name = 'is_active');
SET @query := IF(@exist = 0, 
                 'ALTER TABLE `users` ADD COLUMN `is_active` TINYINT(1) DEFAULT 1 AFTER `mobile`', 
                 'SELECT "is_active column already exists"');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add updated_at column (if not exists)
SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS 
               WHERE table_schema = DATABASE() 
               AND table_name = 'users' 
               AND column_name = 'updated_at');
SET @query := IF(@exist = 0, 
                 'ALTER TABLE `users` ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`', 
                 'SELECT "updated_at column already exists"');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ==========================================
-- 2. Create login_attempts table for security
-- ==========================================

CREATE TABLE IF NOT EXISTS `login_attempts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nic` VARCHAR(20) NOT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `success` TINYINT(1) DEFAULT 0,
    `attempt_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nic_time (`nic`, `attempt_time`),
    INDEX idx_ip_time (`ip_address`, `attempt_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ==========================================
-- 3. Update existing passwords to bcrypt
-- ==========================================

-- Update all users with MD5 hash to bcrypt hash for 'password123'
UPDATE `users` 
SET `password` = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE `password` = '482c811da5d5b4bc6d497ffa98491e38';

-- ==========================================
-- 4. Verify the changes
-- ==========================================

-- Show the updated users table structure
DESCRIBE `users`;

-- Show updated users with bcrypt passwords
SELECT `nic`, `name`, `email`, 
       CASE 
           WHEN `password` LIKE '$2y$%' THEN 'bcrypt'
           ELSE 'other'
       END as `hash_type`,
       `is_active` 
FROM `users`;