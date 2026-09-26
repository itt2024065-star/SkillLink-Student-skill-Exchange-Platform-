<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$want = trim($_GET['want'] ?? '');
$teach = trim($_GET['teach'] ?? '');
$matchResult = null;

if ($want !== '' && $teach !== '') {
    $stmt = $pdo->prepare(
        'SELECT skills.id AS skill_id, skills.title, users.full_name, users.hours_taught, users.rating
         FROM skills JOIN users ON skills.user_id = users.id
         WHERE skills.title = ?
         ORDER BY users.rating DESC
         LIMIT 1'
    );
    $stmt->execute([$want]);
    $matchResult = $stmt->fetch();
}

$skillTitles = $pdo->query('SELECT DISTINCT title FROM skills ORDER BY title')->fetchAll(PDO::FETCH_COLUMN);

$allSkills = $pdo->query(
    'SELECT skills.*, users.full_name, users.hours_taught, users.rating, users.id AS owner_id
     FROM skills JOIN users ON skills.user_id = users.id
     ORDER BY skills.created_at DESC'
)->fetchAll();

$swapError = get_flash('swap_error');
$swapSuccess = get_flash('swap_success');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Browse Skills | Skill-Link</title>

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
    <span class="eyebrow-badge">Browse Skills</span>
    <h1 class="mb-2">Find a peer to swap with</h1>
    <p class="fs-5">Filter by category, level, or trust badge &mdash; or let the Smart Skill Matcher find your best-fit peer below.</p>
  </div>
</header>

<div class="container py-5">

  <?php if ($swapError): ?><div class="alert alert-danger"><?php echo e($swapError); ?></div><?php endif; ?>
  <?php if ($swapSuccess): ?><div class="alert alert-success"><?php echo e($swapSuccess); ?></div><?php endif; ?>

  <section class="matcher-widget mb-5" id="matcher">
    <div class="row align-items-center g-4">
      <div class="col-lg-4">
        <h3 class="text-white mb-2">Find My Skill Match</h3>
        <p class="text-white-50 mb-0">Pick what you want to learn and what you can teach. We'll calculate your best match instantly.</p>
      </div>
      <div class="col-lg-8">
        <form method="get" action="skills.php#matcher">
          <div class="row g-3 align-items-end">
            <div class="col-sm-5">
              <label for="matchWant" class="form-label text-white-50 small mb-1">Skill I Want to Learn</label>
              <select id="matchWant" name="want" class="form-select">
                <option value="">Choose a skill</option>
                <?php foreach ($skillTitles as $t): ?>
                  <option value="<?php echo e($t); ?>" <?php echo $want === $t ? 'selected' : ''; ?>><?php echo e($t); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-sm-5">
              <label for="matchTeach" class="form-label text-white-50 small mb-1">Skill I Can Teach</label>
              <select id="matchTeach" name="teach" class="form-select">
                <option value="">Choose a skill</option>
                <?php foreach ($skillTitles as $t): ?>
                  <option value="<?php echo e($t); ?>" <?php echo $teach === $t ? 'selected' : ''; ?>><?php echo e($t); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-sm-2">
              <button type="submit" class="btn btn-primary-custom w-100">Find Match</button>
            </div>
          </div>
        </form>
        <div id="matchResultZone" class="mt-4">
          <?php if ($want !== '' && $teach !== ''): ?>
            <?php if ($matchResult): $pct = min(99, 60 + (int) round($matchResult['rating'] * 8)); ?>
              <div class="match-result-card">
                <div class="match-percent-ring" style="--pct:<?php echo $pct; ?>" data-pct="<?php echo $pct; ?>"></div>
                <div class="flex-grow-1">
                  <strong><?php echo e($matchResult['full_name']); ?></strong>
                  <div class="small">Teaches <strong><?php echo e($matchResult['title']); ?></strong></div>
                  <div class="fw-semibold small mt-1" style="color:var(--teal)"><?php echo $pct; ?>% Match Found!</div>
                </div>
                <button type="button" class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#swapModal" data-skill-title="<?php echo e($matchResult['title']); ?>" data-skill-id="<?php echo (int) $matchResult['skill_id']; ?>">Request Swap</button>
              </div>
            <?php else: ?>
              <p class="text-white-50 small mb-0">No one currently teaches "<?php echo e($want); ?>" yet.</p>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <section class="filter-bar mb-4">
    <div class="row g-3 align-items-end">
      <div class="col-md-3">
        <label for="filterCategory">Category</label>
        <select id="filterCategory" class="form-select">
          <option value="all">All categories</option>
          <option value="programming">Programming</option>
          <option value="design">Design</option>
          <option value="academic">Academic</option>
          <option value="soft-skills">Soft Skills</option>
        </select>
      </div>
      <div class="col-md-3">
        <label for="filterLevel">Skill Level</label>
        <select id="filterLevel" class="form-select">
          <option value="all">All levels</option>
          <option value="Beginner">Beginner</option>
          <option value="Intermediate">Intermediate</option>
          <option value="Advanced">Advanced</option>
        </select>
      </div>
      <div class="col-md-3">
        <label for="filterBadge">Badge</label>
        <select id="filterBadge" class="form-select">
          <option value="all">All badges</option>
          <option value="verified">Verified Tutor</option>
          <option value="top">Top Rated</option>
          <option value="newcomer">Newcomer</option>
        </select>
      </div>
      <div class="col-md-3">
        <label for="filterSearch">Search</label>
        <input type="text" id="filterSearch" class="form-control" placeholder="e.g. Python, Java...">
      </div>
    </div>
    <div class="mt-3 small text-muted" id="resultCount"></div>
  </section>

  <section class="row g-4" id="skillGrid">
    <?php if (empty($allSkills)): ?>
      <p class="text-muted">No skills have been listed yet. Log in and add one from your dashboard!</p>
    <?php endif; ?>
    <?php foreach ($allSkills as $row):
      $badge = calculate_badge($row['hours_taught'], $row['rating']);
      $badgeKey = str_replace('badge-', '', $badge['class']);
      $initials = strtoupper(substr($row['full_name'], 0, 1));
      $isOwnSkill = is_logged_in() && (int) $row['owner_id'] === current_user_id();
    ?>
      <div class="col-md-6 col-lg-4 skill-card-col" data-category="<?php echo e($row['category']); ?>" data-level="<?php echo e($row['level']); ?>" data-badges="<?php echo e($badgeKey); ?>" data-title="<?php echo e($row['title']); ?>">
        <div class="skill-card">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <span class="subject-tag"><?php echo e(ucfirst(str_replace('-', ' ', $row['category']))); ?></span>
            <span class="level-pill"><?php echo e($row['level']); ?></span>
          </div>
          <h5><?php echo e($row['title']); ?></h5>
          <p class="small"><?php echo e($row['description']); ?></p>
          <div class="d-flex align-items-center gap-2 mb-3">
            <div class="tutor-avatar"><?php echo e($initials); ?></div>
            <div>
              <div class="fw-semibold small"><?php echo e($row['full_name']); ?></div>
              <span class="skill-badge <?php echo $badge['class']; ?>" data-badge-name="<?php echo e($badge['label']); ?>" data-badge-reason="<?php echo e($badge['reason']); ?>">
                <?php echo $badge['class'] !== 'badge-newcomer' ? '&#10003; ' : ''; ?><?php echo e($badge['label']); ?>
              </span>
            </div>
          </div>
          <?php if ($isOwnSkill): ?>
            <span class="btn btn-outline-primary-custom w-100 disabled">This is your skill</span>
          <?php else: ?>
            <button type="button" class="btn btn-primary-custom w-100" data-bs-toggle="modal" data-bs-target="#swapModal" data-skill-title="<?php echo e($row['title']); ?>" data-skill-id="<?php echo (int) $row['id']; ?>">Request Swap</button>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </section>
</div>

<div class="modal fade" id="badgeInfoModal" tabindex="-1" aria-labelledby="badgeInfoTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="badgeInfoTitle">Badge</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="badgeInfoBody">Reason will appear here.</div>
    </div>
  </div>
</div>

<div class="modal fade" id="swapModal" tabindex="-1" aria-labelledby="swapModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="swapForm" method="post" action="create_swap.php">
        <input type="hidden" id="swapSkillId" name="skill_id" value="">
        <div class="modal-header">
          <h5 class="modal-title" id="swapModalLabel">Request a Skill Swap</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="swapSkillName" class="form-label">Skill</label>
            <input type="text" class="form-control" id="swapSkillName" readonly>
          </div>
          <div class="mb-3">
            <label for="swapOffer" class="form-label">What will you teach in return?</label>
            <input type="text" class="form-control" id="swapOffer" name="offered_skill" placeholder="e.g. Excel, Photography" required>
          </div>
          <div class="mb-3">
            <label for="swapNote" class="form-label">Message (optional)</label>
            <textarea class="form-control" id="swapNote" rows="3" placeholder="Introduce yourself and suggest a time"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-primary-custom" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary-custom">Send Request</button>
        </div>
      </form>
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