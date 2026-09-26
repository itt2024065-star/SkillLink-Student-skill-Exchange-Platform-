<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$title = trim($_POST['title'] ?? '');
$category = $_POST['category'] ?? '';
$level = $_POST['level'] ?? '';
$description = trim($_POST['description'] ?? '');

$allowedCategories = ['programming', 'design', 'academic', 'soft-skills'];
$allowedLevels = ['Beginner', 'Intermediate', 'Advanced'];

if ($title === '' || !in_array($category, $allowedCategories, true) || !in_array($level, $allowedLevels, true)) {
    set_flash('skill_error', 'Please fill in all fields correctly.');
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare(
    'INSERT INTO skills (user_id, title, category, level, description)
     VALUES (?, ?, ?, ?, ?)'
);

$stmt->execute([
    current_user_id(),
    $title,
    $category,
    $level,
    $description
]);

set_flash('skill_success', 'Skill added successfully.');

header('Location: dashboard.php');
exit;