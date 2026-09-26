<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$loginError    = get_flash('login_error');
$loginSuccess  = get_flash('login_success');
$registerError = get_flash('register_error');
$showRegister  = $registerError !== null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login / Register | Skill-Link</title>

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
        <li class="nav-item ms-lg-3"><a class="btn btn-outline-primary-custom btn-sm px-3 active" href="login.php">Login / Register</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="text-center mb-5">
        <span class="eyebrow-badge">Account</span>
        <h1>Welcome to Skill-Link</h1>
        <p class="fs-5 mx-auto">Sign in to view your matches, or create an account to start swapping skills.</p>
      </div>

      <ul class="nav nav-pills justify-content-center mb-4" id="authTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link <?php echo !$showRegister ? 'active' : ''; ?>" id="login-tab" data-bs-toggle="pill" data-bs-target="#loginPane" type="button" role="tab">Login</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link <?php echo $showRegister ? 'active' : ''; ?>" id="register-tab" data-bs-toggle="pill" data-bs-target="#registerPane" type="button" role="tab">Register</button>
        </li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane fade <?php echo !$showRegister ? 'show active' : ''; ?>" id="loginPane" role="tabpanel">
          <div class="form-panel mx-auto" style="max-width:480px;">
            <?php if ($loginError): ?><div class="alert alert-danger py-2 small"><?php echo e($loginError); ?></div><?php endif; ?>
            <?php if ($loginSuccess): ?><div class="alert alert-success py-2 small"><?php echo e($loginSuccess); ?></div><?php endif; ?>
            <form id="loginForm" method="post" action="process_login.php" novalidate>
              <div class="mb-3">
                <label for="loginEmail" class="form-label">University Email</label>
                <input type="email" class="form-control" id="loginEmail" name="email" placeholder="you@stu.rjt.ac.lk" required>
              </div>
              <div class="mb-3">
                <label for="loginPassword" class="form-label">Password</label>
                <input type="password" class="form-control" id="loginPassword" name="password" required>
              </div>
              <button type="submit" class="btn btn-primary-custom w-100">Login</button>
            </form>
          </div>
        </div>

        <div class="tab-pane fade <?php echo $showRegister ? 'show active' : ''; ?>" id="registerPane" role="tabpanel">
          <div class="form-panel mx-auto" style="max-width:480px;">
            <?php if ($registerError): ?><div class="alert alert-danger py-2 small"><?php echo e($registerError); ?></div><?php endif; ?>
            <form id="registerForm" method="post" action="process_register.php" novalidate>
              <div class="mb-3">
                <label for="registerName" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="registerName" name="full_name" required>
              </div>
              <div class="mb-3">
                <label for="registerEmail" class="form-label">University Email</label>
                <input type="email" class="form-control" id="registerEmail" name="email" required>
              </div>
              <div class="mb-3">
                <label for="registerPassword" class="form-label">Password</label>
                <input type="password" class="form-control" id="registerPassword" name="password" minlength="6" required>
              </div>
              <button type="submit" class="btn btn-primary-custom w-100">Get Started</button>
            </form>
          </div>
        </div>
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