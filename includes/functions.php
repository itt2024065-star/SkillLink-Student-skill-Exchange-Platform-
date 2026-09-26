<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function set_flash($type, $message) {
    $_SESSION['flash_' . $type] = $message;
}

function get_flash($type) {
    if (!empty($_SESSION['flash_' . $type])) {
        $message = $_SESSION['flash_' . $type];
        unset($_SESSION['flash_' . $type]);
        return $message;
    }

    return null;
}

function calculate_badge($hoursTaught, $rating) {
    $hoursTaught = (int) $hoursTaught;
    $rating = (float) $rating;

    if ($hoursTaught >= 10 && $rating >= 4.8) {
        return [
            'class' => 'badge-top',
            'label' => 'Top Rated',
            'reason' => $hoursTaught . '+ Hours Taught, ' . number_format($rating, 1) . ' Rating'
        ];
    }

    if ($hoursTaught >= 5) {
        return [
            'class' => 'badge-verified',
            'label' => 'Verified Tutor',
            'reason' => $hoursTaught . '+ Hours Taught, ' . number_format($rating, 1) . ' Rating'
        ];
    }

    return [
        'class' => 'badge-newcomer',
        'label' => 'Newcomer',
        'reason' => 'Newly joined — ' . $hoursTaught . ' hour(s) logged so far'
    ];
}

function badge_progress($hoursTaught) {
    $hoursTaught = (int) $hoursTaught;

    if ($hoursTaught < 5) {
        return [
            'target' => 5,
            'label' => 'Verified Tutor'
        ];
    }

    if ($hoursTaught < 10) {
        return [
            'target' => 10,
            'label' => 'Top Rated'
        ];
    }

    return [
        'target' => $hoursTaught,
        'label' => 'Top Rated'
    ];
}