<?php

require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    set_flash('login_error', 'Please enter both email and password.');
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT id, full_name, password FROM users WHERE email = ?'
);

$stmt->execute([$email]);

$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    set_flash('login_error', 'Incorrect email or password.');
    header('Location: login.php');
    exit;
}

session_regenerate_id(true);

$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['full_name'];

header('Location: dashboard.php');
exit;