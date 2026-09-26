<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: skills.php');
    exit;
}

$skillId = (int) ($_POST['skill_id'] ?? 0);
$offeredSkill = trim($_POST['offered_skill'] ?? '');

if ($skillId <= 0 || $offeredSkill === '') {
    set_flash('swap_error', 'Please choose a skill and say what you can offer in return.');
    header('Location: skills.php');
    exit;
}

$stmt = $pdo->prepare('SELECT user_id FROM skills WHERE id = ?');
$stmt->execute([$skillId]);

$skill = $stmt->fetch();

if (!$skill) {
    set_flash('swap_error', 'That skill no longer exists.');
    header('Location: skills.php');
    exit;
}

if ((int) $skill['user_id'] === current_user_id()) {
    set_flash('swap_error', 'You cannot request a swap on your own skill.');
    header('Location: skills.php');
    exit;
}

$insert = $pdo->prepare(
    'INSERT INTO skill_requests (requester_id, skill_id, offered_skill)
     VALUES (?, ?, ?)'
);

$insert->execute([
    current_user_id(),
    $skillId,
    $offeredSkill
]);

set_flash('swap_success', 'Swap request sent!');

header('Location: skills.php');
exit;