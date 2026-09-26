<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['profile_picture'])) {
    header('Location: dashboard.php');
    exit;
}

$file = $_FILES['profile_picture'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    set_flash('upload_error', 'Upload failed. Please try again.');
    header('Location: dashboard.php');
    exit;
}

$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif'
];

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!isset($allowedTypes[$mimeType])) {
    set_flash('upload_error', 'Only JPG, PNG or GIF images are allowed.');
    header('Location: dashboard.php');
    exit;
}

$maxSize = 2 * 1024 * 1024;

if ($file['size'] > $maxSize) {
    set_flash('upload_error', 'Image must be smaller than 2MB.');
    header('Location: dashboard.php');
    exit;
}

$extension = $allowedTypes[$mimeType];

$safeFileName = 'user_' . current_user_id() . '_' . time() . '.' . $extension;

$uploadDir = __DIR__ . '/uploads/profile/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$destination = $uploadDir . $safeFileName;

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    set_flash('upload_error', 'Could not save the uploaded image. Please try again.');
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT profile_picture FROM users WHERE id = ?'
);

$stmt->execute([current_user_id()]);

$old = $stmt->fetchColumn();

if ($old && file_exists($uploadDir . $old)) {
    unlink($uploadDir . $old);
}

$update = $pdo->prepare(
    'UPDATE users SET profile_picture = ? WHERE id = ?'
);

$update->execute([
    $safeFileName,
    current_user_id()
]);

set_flash('upload_success', 'Profile picture updated.');

header('Location: dashboard.php');
exit;