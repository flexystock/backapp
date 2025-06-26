INSERT INTO profile_permission (profile_id, permission_id)
SELECT p.id, perm.id
FROM profiles p, permissions perm
WHERE p.name = 'flexy_manager';
