-- Clear all accounts from both tables
DELETE FROM users;
DELETE FROM clients;

-- Reset auto-increment counters (if needed)
DELETE FROM sqlite_sequence WHERE name='users';
DELETE FROM sqlite_sequence WHERE name='clients';

-- Verify tables are empty
SELECT COUNT(*) as users_count FROM users;
SELECT COUNT(*) as clients_count FROM clients;