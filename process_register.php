<?php

require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$fullName = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($fullName === '' || $email === '' || $password === '') {
    set_flash('register_error', 'Please fill in all fields.');
    header('Location: login.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash('register_error', 'Please enter a valid email address.');
    header('Location: login.php');
    exit;
}

if (strlen($password) < 6) {
    set_flash('register_error', 'Password must be at least 6 characters.');
    header('Location: login.php');
    exit;
}

$check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$check->execute([$email]);

if ($check->fetch()) {
    set_flash('register_error', 'An account with this email already exists.');
    header('Location: login.php');
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$insert = $pdo->prepare(
    'INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)'
);

$insert->execute([
    $fullName,
    $email,
    $hashedPassword
]);

set_flash(
    'login_success',
    'Account created successfully. You can now log in.'
);

header('Location: login.php');
exit;

