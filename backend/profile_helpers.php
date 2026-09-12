<?php

function load_profile(mysqli $conn, string $username, string $role): array
{
    $stmt = $conn->prepare('SELECT name, username FROM users WHERE username = ? AND role = ? LIMIT 1');
    $stmt->bind_param('ss', $username, $role);
    $stmt->execute();
    $profile = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $profile ?: ['name' => '', 'username' => ''];
}

function update_profile(mysqli $conn, string $currentUsername, string $role): array
{
    $name = trim($_POST['name'] ?? '');
    $newUsername = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($name === '' || $newUsername === '') {
        return ['', 'Name and username are required.', $currentUsername];
    }
    if (strlen($newUsername) < 3) {
        return ['', 'Username must be at least 3 characters long.', $currentUsername];
    }
    if ($password !== '' && strlen($password) < 6) {
        return ['', 'A new password must be at least 6 characters long.', $currentUsername];
    }

    $check = $conn->prepare('SELECT id FROM users WHERE username = ? AND username <> ? LIMIT 1');
    $check->bind_param('ss', $newUsername, $currentUsername);
    $check->execute();
    $exists = $check->get_result()->num_rows > 0;
    $check->close();
    if ($exists) {
        return ['', 'Username is already taken. Please choose a different one.', $currentUsername];
    }

    if ($password !== '') {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $update = $conn->prepare('UPDATE users SET name = ?, username = ?, password = ? WHERE username = ? AND role = ?');
        $update->bind_param('sssss', $name, $newUsername, $passwordHash, $currentUsername, $role);
    } else {
        $update = $conn->prepare('UPDATE users SET name = ?, username = ? WHERE username = ? AND role = ?');
        $update->bind_param('ssss', $name, $newUsername, $currentUsername, $role);
    }

    $ok = $update->execute();
    $update->close();
    return $ok
        ? ['Profile updated successfully.', '', $newUsername]
        : ['', 'Profile update failed. Please try again.', $currentUsername];
}
