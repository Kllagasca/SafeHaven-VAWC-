-- Migration: add supabase_id column to local MySQL `cases` table
-- Purpose: store the Supabase/Postgres case `id` (external case id) to avoid relying only on the local caseno string.
-- Notes:
--  - Supabase `id` may be an integer or a UUID; this script uses VARCHAR(255) to be safe.
--  - MySQL allows multiple NULLs for a UNIQUE index; this migration makes the column UNIQUE so once you populate it duplicate external ids are prevented.
--  - Run this on your local MySQL server (e.g., via phpMyAdmin, MySQL Workbench, or mysql CLI).

ALTER TABLE `cases`
  ADD COLUMN `supabase_id` VARCHAR(255) NULL AFTER `caseno`,
  ADD COLUMN `supabase_id_created_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'timestamp when supabase_id was synced' AFTER `supabase_id`;

-- Create an index and unique constraint on supabase_id to prevent duplicates for populated rows
ALTER TABLE `cases`
  ADD UNIQUE INDEX `uniq_cases_supabase_id` (`supabase_id`),
  ADD INDEX `idx_cases_supabase_id` (`supabase_id`);

-- Optional: if you want to copy a value from an existing `id` column (uncommon on local MySQL)
-- UPDATE `cases` SET `supabase_id` = `id` WHERE `supabase_id` IS NULL AND `id` IS NOT NULL;

-- Optional cleanup: set a default value or mark rows as synced after populating
-- UPDATE `cases` SET `supabase_id_created_at` = NOW() WHERE `supabase_id` IS NOT NULL AND `supabase_id_created_at` IS NULL;

-- End of migration
