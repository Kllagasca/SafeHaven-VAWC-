-- PostgreSQL migration: create notifications table for Supabase/Postgres
-- Run in Supabase SQL editor or with psql: psql "postgresql://<user>:<pass>@<host>:<port>/<db>" -f create_notifications_postgres.sql

CREATE TABLE IF NOT EXISTS notifications (
  id SERIAL PRIMARY KEY,
  recipient_role VARCHAR(50) NOT NULL,
  recipient_id INTEGER DEFAULT NULL,
  title VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  link VARCHAR(512) DEFAULT NULL,
  is_read INTEGER DEFAULT 0,
  created_at TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Helpful indexes
CREATE INDEX IF NOT EXISTS idx_notifications_recipient_role ON notifications (recipient_role);
CREATE INDEX IF NOT EXISTS idx_notifications_is_read ON notifications (is_read);
CREATE INDEX IF NOT EXISTS idx_notifications_created_at ON notifications (created_at);
CREATE INDEX IF NOT EXISTS idx_notifications_recipient_id ON notifications (recipient_id);
