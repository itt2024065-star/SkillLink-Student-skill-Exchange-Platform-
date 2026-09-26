<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$requestId = (int) ($_POST['request_id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($requestId <= 0 || !in_array($action, ['accept', 'decline'], true)) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT sr.id
     FROM skill_requests sr
     JOIN skills s ON sr.skill_id = s.id
     WHERE sr.id = ? AND s.user_id = ?'
);

$stmt->execute([
    $requestId,
    current_user_id()
]);

if ($stmt->fetch()) {
    $newStatus = $action === 'accept' ? 'accepted' : 'declined';

    $update = $pdo->prepare(
        'UPDATE skill_requests SET status = ? WHERE id = ?'
    );

    $update->execute([
        $newStatus,
        $requestId
    ]);
}

header('Location: dashboard.php');
exit;