<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($message) < 10) {
        set_flash('contact_error', 'Please check your details: name (2+ chars), a valid email, and a message of 10+ characters.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO messages (name, email, message) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $message]);
        set_flash('contact_success', "Thanks — your message has been sent. We'll reply to your email soon.");
    }

    header('Location: contact.php');
    exit;
}

$contactError = get_flash('contact_error');
$contactSuccess = get_flash('contact_success');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact | Skill-Link</title>

<link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top navbar-custom">
  <div class="container">
    <a class="navbar-brand" href="index.html"><span class="brand-mark">SL</span>Skill<span class="brand-accent">Link</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
        <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="skills.php">Browse Skills</a></li>
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
        <li class="nav-item ms-lg-3"><a class="btn btn-outline-primary-custom btn-sm px-3" href="login.php">Login / Register</a></li>
      </ul>
    </div>
  </div>
</nav>

<header class="py-5" style="background:var(--surface); border-bottom:1px solid var(--border);">
  <div class="container">
    <span class="eyebrow-badge">Contact</span>
    <h1 class="mb-2">Get in touch</h1>
    <p class="fs-5">Questions about a swap, a badge, or the platform itself &mdash; send us a message.</p>
  </div>
</header>

<div class="container py-5">
  <div class="row g-5">
    <div class="col-lg-6">
      <div class="form-panel">
        <h4 class="mb-3">Send a message</h4>
        <?php if ($contactError): ?><div class="alert alert-danger py-2 small"><?php echo e($contactError); ?></div><?php endif; ?>
        <?php if ($contactSuccess): ?><div class="alert alert-success py-2 small"><?php echo e($contactSuccess); ?></div><?php endif; ?>
        <form id="contactForm" method="post" action="contact.php" novalidate>
          <div class="mb-3">
            <label for="contactName" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="contactName" name="name" minlength="2" required>
          </div>
          <div class="mb-3">
            <label for="contactEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="contactEmail" name="email" required>
          </div>
          <div class="mb-3">
            <label for="contactMessage" class="form-label">Message</label>
            <textarea class="form-control" id="contactMessage" name="message" rows="5" minlength="10" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary-custom w-100">Send Message</button>
        </form>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="feature-card h-auto">
        <span class="module-chip">FAQ</span>
        <h5>Before you message us</h5>
        <ul class="small">
          <li>Badge disputes are usually resolved within 48 hours.</li>
          <li>Swap requests are handled peer-to-peer via your Dashboard.</li>
          <li>Report an issue with a listing using the same form.</li>
        </ul>
      </div>
      <div class="feature-card h-auto mt-4">
        <span class="module-chip">DEPARTMENT</span>
        <h5>Department of ICT</h5>
        <p class="small mb-1">Rajarata University of Sri Lanka</p>
        <p class="small mb-0">ICT 1209 &ndash; Web Technologies, 2024 Batch</p>
      </div>
    </div>
  </div>
</div>

<footer class="site-footer">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-4">
        <h6 class="mb-2">Skill-Link</h6>
        <p class="small mb-0">A student skill exchange platform built for Rajarata University of Sri Lanka, Department of ICT.</p>
      </div>
      <div class="col-md-4">
        <h6 class="mb-2">Quick Links</h6>
        <ul class="list-unstyled small">
          <li><a href="index.html">Home</a></li>
          <li><a href="skills.php">Browse Skills</a></li>
          <li><a href="dashboard.php">Dashboard</a></li>
          <li><a href="contact.php">Contact</a></li>
        </ul>
      </div>
      <div class="col-md-4">
        <h6 class="mb-2">Connect</h6>
        <div class="footer-social">
          <a href="#" aria-label="Facebook">f</a>
          <a href="#" aria-label="Instagram">i</a>
          <a href="#" aria-label="LinkedIn">in</a>
        </div>
      </div>
    </div>
    <div class="footer-bottom text-center">
      &copy; 2026 Skill-Link &middot; Rajarata University of Sri Lanka &middot; ICT 1209 Mini Project
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>