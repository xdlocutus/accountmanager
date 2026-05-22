-- Creates an admin user if it does not already exist.
-- Default login:
--   email: admin@streambox.local
--   password: ChangeMeNow!123
-- IMPORTANT: change the password immediately after first login.

INSERT INTO users (email, password_hash, role, status, created_at)
SELECT 'admin@streambox.local', '$2y$12$/ghEEmLLHTDy8vEHb1tzr.MU.J73wg1GE4x4bjXusCgo4xyRZW1je', 'admin', 'active', NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE email = 'admin@streambox.local'
);
