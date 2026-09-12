<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/../backend/connect.php';

if ($argc !== 5) {
    fwrite(STDERR, "Usage: php scripts/create_user.php <name> <username> <password> <member|staff|admin>\n");
    exit(1);
}

[$script, $name, $username, $password, $role] = $argv;
$allowedRoles = ['member', 'staff', 'admin'];

if (!in_array($role, $allowedRoles, true)) {
    fwrite(STDERR, "Role must be member, staff, or admin.\n");
    exit(1);
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare('INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, ?)');
$stmt->bind_param('ssss', $name, $username, $passwordHash, $role);

if (!$stmt->execute()) {
    fwrite(STDERR, "Could not create user. Check that the username is unique.\n");
    exit(1);
}

fwrite(STDOUT, "User created successfully.\n");
