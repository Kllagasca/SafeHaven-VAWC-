-- Migration: create survey_verifications table and add verification_id to responses
-- BACKUP your DB before running.

-- Create survey_verifications table
CREATE TABLE IF NOT EXISTS survey_verifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  survey_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  gender VARCHAR(50) DEFAULT NULL,
  address TEXT DEFAULT NULL,
  image VARCHAR(512) DEFAULT NULL,
  status ENUM('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add verification_id column to responses to link responses to a verification (nullable)
ALTER TABLE `responses` 
  ADD COLUMN IF NOT EXISTS `verification_id` INT NULL, 
  ADD INDEX(`verification_id`);

-- (Optional) You may want to add a foreign key to survey_verifications.id if your DB schema enforces FKs.
-- ALTER TABLE `responses` ADD CONSTRAINT fk_responses_verification FOREIGN KEY (verification_id) REFERENCES survey_verifications(id) ON DELETE SET NULL;
