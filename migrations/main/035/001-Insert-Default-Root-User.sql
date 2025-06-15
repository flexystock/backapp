-- Plain password for the default root user: rootpassword
-- Hashed password stored in the database:
-- $2y$12$uKD5lsf6pGCIZ/RMcVHb0.YBLGxY0EySK/2rlPhzi.6jjy9UB7dV.
INSERT INTO users (uuid_user, name, surnames, phone, email, password, is_root, active, uuid_user_creation, date_hour_creation, timezone, language)
VALUES (
  'a674161a-4d09-4c5a-b0d0-a61224596e44',
  'Root',
  'Admin',
  123456789,
  'root@example.com',
  '$2y$12$uKD5lsf6pGCIZ/RMcVHb0.YBLGxY0EySK/2rlPhzi.6jjy9UB7dV.',
  1,
  1,
  'a674161a-4d09-4c5a-b0d0-a61224596e44',
  NOW(),
  'UTC',
  'en'
);

INSERT INTO user_role (uuid_user, role_id)
VALUES (
  'a674161a-4d09-4c5a-b0d0-a61224596e44',
  (SELECT id FROM roles WHERE name='root')
);


INSERT INTO user_profile (uuid_user, profile_id)
VALUES (
  'a674161a-4d09-4c5a-b0d0-a61224596e44',
  (SELECT id FROM profiles WHERE name='flexystock_manager')
);
