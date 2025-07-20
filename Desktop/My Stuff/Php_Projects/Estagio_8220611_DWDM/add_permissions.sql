-- Add permission fields to users table
ALTER TABLE users ADD COLUMN admin_access_granted INTEGER DEFAULT 0;
ALTER TABLE users ADD COLUMN granted_by TEXT;
ALTER TABLE users ADD COLUMN granted_at TIMESTAMP;

-- Verify the columns were added
PRAGMA table_info(users);
