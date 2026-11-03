<?php
/**
 * One-time CLI helper to create/reset a test login after attaching the database.
 * Run from a terminal:   php MealsonWheels\tools\create-test-user.php
 * Uses the same DB connection as the app (classes/db.class.php), so configure that first.
 * SAFE: refuses to run over the web. Delete this file before production.
 */
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('This script can only be run from the command line.');
}

require_once(__DIR__ . '/../classes/db.class.php');
$db = new database();

$email = 'staff@mow.com';
$password = 'Mow12345';
$hash = password_hash($password, PASSWORD_DEFAULT);

$existing = $db->select('SELECT id FROM users WHERE userEmail = ?', [$email]);
if ($existing) {
    $db->update('UPDATE users SET userPassword = ?, isAdmin = 1, firstName = ?, lastName = ? WHERE userEmail = ?',
        [$hash, 'Staff', 'User', $email]);
    echo "Updated existing user.\n";
} else {
    $db->insert('INSERT INTO users (userEmail, userPassword, firstName, lastName, isAdmin) VALUES (?,?,?,?,1)',
        [$email, $hash, 'Staff', 'User']);
    echo "Created new user.\n";
}

echo "You can now log in with:\n  Email:    $email\n  Password: $password\n";
