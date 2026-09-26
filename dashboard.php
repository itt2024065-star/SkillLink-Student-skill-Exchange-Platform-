<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_login();

$userId = current_user_id();

$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: logout.php');
    exit;
}

$badge = calculate_badge($user['hours_taught'], $user['rating']);
$progress = badge_progress($user['hours_taught']);
$progressPct = $progress['target'] > 0 ? min(100, round(($user['hours_taught'] / $progress['target']) * 100)) : 100;
$remainingHours = max(0, $progress['target'] - $user['hours_taught']);

$nameParts = preg_split('/\s+/', trim($user['full_name']));
$initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
$memberSince = date('M Y', strtotime($user['created_at']));

$skillsStmt = $pdo->prepare('SELECT * FROM skills WHERE user_id = ? ORDER BY created_at DESC');
$skillsStmt->execute([$userId]);
$mySkills = $skillsStmt->fetchAll();

$matchStmt = $pdo->prepare(
    'SELECT skills.id AS skill_id, skills.title, users.full_name, users.hours_taught, users.rating
     FROM skills JOIN users ON skills.user_id = users.id
     WHERE skills.user_id != ?
     ORDER BY users.rating DESC, users.hours_taught DESC
     LIMIT 3'
);
$matchStmt->execute([$userId]);
$matches = $matchStmt->fetchAll();

$incomingStmt = $pdo->prepare(
    'SELECT sr.id, sr.status, sr.offered_skill, u.full_name AS requester_name, s.title AS skill_title
     FROM skill_requests sr
     JOIN users u ON sr.requester_id = u.id
     JOIN skills s ON sr.skill_id = s.id
     WHERE s.user_id = ?
     ORDER BY sr.created_at DESC'
);
$incomingStmt->execute([$userId]);
$incoming = $incomingStmt->fetchAll();

$outgoingStmt = $pdo->prepare(
    'SELECT sr.id, sr.status, sr.offered_skill, s.title AS skill_title, owner.full_name AS owner_name
     FROM skill_requests sr
     JOIN skills s ON sr.skill_id = s.id
     JOIN users owner ON s.user_id = owner.id
     WHERE sr.requester_id = ?
     ORDER BY sr.created_at DESC'
);
$outgoingStmt->execute([$userId]);
$outgoing = $outgoingStmt->fetchAll();

$uploadError   = get_flash('upload_error');
$uploadSuccess = get_flash('upload_success');
$skillError    = get_flash('skill_error');
$skillSuccess  = get_flash('skill_success');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Dashboard | Skill-Link</title>

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
        <li class="nav-item ms-lg-3"><a class="btn btn-navy btn-sm px-3" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<header class="py-4" style="background:var(--surface); border-bottom:1px solid var(--border);">
  <div class="container">
    <span class="eyebrow-badge">My Dashboard</span>
    <h1 class="h2 mb-0">Welcome back, <?php echo e($nameParts[0]); ?></h1>
  </div>
</header>

<div class="container py-5">
  <div class="row g-4">

    <div class="col-lg-4">
      <div class="profile-card mb-4">
        <div class="d-flex align-items-center gap-3 mb-3">
          <div class="profile-photo" style="overflow:hidden;">
            <?php if (!empty($user['profile_picture']) && file_exists('uploads/profile/' . $user['profile_picture'])): ?>
              <img src="uploads/profile/<?php echo e($user['profile_picture']); ?>" alt="Profile picture" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
            <?php else: ?>
              <?php echo e($initials); ?>
            <?php endif; ?>
          </div>
          <div>
            <h5 class="mb-0"><?php echo e($user['full_name']); ?></h5>
            <div class="text-muted small"><?php echo e($user['university'] ?: 'Rajarata University of Sri Lanka'); ?></div>
          </div>
        </div>
        <div class="d-flex flex-wrap gap-2 mb-3">
          <span class="skill-badge <?php echo $badge['class']; ?>" data-bs-toggle="tooltip" title="<?php echo e($badge['reason']); ?>" data-badge-name="<?php echo e($badge['label']); ?>" data-badge-reason="<?php echo e($badge['reason']); ?>">
            <?php echo $badge['class'] !== 'badge-newcomer' ? '&#10003; ' : ''; ?><?php echo e($badge['label']); ?>
          </span>
        </div>
        <hr>
        <p class="small mb-1"><strong>Faculty:</strong> <?php echo e($user['university'] ?: 'Not set'); ?></p>
        <p class="small mb-3"><strong>Member since:</strong> <?php echo e($memberSince); ?></p>

        <?php if ($uploadError): ?><div class="alert alert-danger py-2 small"><?php echo e($uploadError); ?></div><?php endif; ?>
        <?php if ($uploadSuccess): ?><div class="alert alert-success py-2 small"><?php echo e($uploadSuccess); ?></div><?php endif; ?>
        <form action="upload_profile.php" method="post" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
          <input type="file" name="profile_picture" accept=".jpg,.jpeg,.png,.gif" class="form-control form-control-sm" required>
          <button type="submit" class="btn btn-outline-primary-custom btn-sm">Upload</button>
        </form>
      </div>

      <div class="profile-card">
        <h6 class="mb-1">Next badge: <?php echo e($progress['label']); ?></h6>
        <p class="small mb-2"><?php echo (int) $user['hours_taught']; ?>/<?php echo (int) $progress['target']; ?> Swap Hours completed to unlock this badge</p>
        <div class="progress-track">
          <div class="progress-fill" id="badgeProgressFill" data-target="<?php echo $progressPct; ?>" style="width:0%"></div>
        </div>
        <p class="small text-muted mt-2 mb-0">
          <?php if ($remainingHours > 0): ?>
            <?php echo $remainingHours; ?> more hour(s) to go &mdash; keep teaching!
          <?php else: ?>
            Badge unlocked! Keep up the great work.
          <?php endif; ?>
        </p>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="mb-4">
        <h5 class="mb-3">Recommended peers for you</h5>
        <div class="d-flex flex-column gap-3">
          <?php if (empty($matches)): ?>
            <p class="text-muted small">No suggestions yet &mdash; once more students add skills, matches will appear here.</p>
          <?php else: foreach ($matches as $m): $pct = min(99, 60 + (int) round($m['rating'] * 8)); ?>
            <div class="match-suggestion-card">
              <div class="circular-badge" style="--pct:<?php echo $pct; ?>"><span><?php echo $pct; ?>%</span></div>
              <div class="flex-grow-1">
                <strong><?php echo e($m['full_name']); ?></strong>
                <div class="small text-muted">Teaches <?php echo e($m['title']); ?></div>
              </div>
              <button class="btn btn-outline-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#swapModal" data-skill-title="<?php echo e($m['title']); ?>" data-skill-id="<?php echo (int) $m['skill_id']; ?>">Connect</button>
            </div>
          <?php endforeach; endif; ?>
        </div>
      </div>

      <div>
        <h5 class="mb-3">Swap Requests</h5>
        <div class="table-responsive">
          <table class="table swap-table align-middle" id="swapRequestsTable">
            <thead>
              <tr><th>Peer</th><th>Direction</th><th>Skill</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
              <?php if (empty($incoming) && empty($outgoing)): ?>
                <tr><td colspan="5" class="text-muted small">No swap requests yet.</td></tr>
              <?php endif; ?>
              <?php foreach ($incoming as $row): ?>
                <tr>
                  <td><?php echo e($row['requester_name']); ?></td>
                  <td><span class="badge text-bg-light border">Incoming</span></td>
                  <td><?php echo e($row['skill_title']); ?> for <?php echo e($row['offered_skill']); ?></td>
                  <td><span class="swap-status <?php echo e($row['status']); ?>"><?php echo e(ucfirst($row['status'])); ?></span></td>
                  <td class="action-cell">
                    <?php if ($row['status'] === 'pending'): ?>
                      <form method="post" action="swap_action.php" class="d-inline">
                        <input type="hidden" name="request_id" value="<?php echo (int) $row['id']; ?>">
                        <input type="hidden" name="action" value="accept">
                        <button type="submit" class="btn btn-sm btn-primary-custom me-1">Accept</button>
                      </form>
                      <form method="post" action="swap_action.php" class="d-inline">
                        <input type="hidden" name="request_id" value="<?php echo (int) $row['id']; ?>">
                        <input type="hidden" name="action" value="decline">
                        <button type="submit" class="btn btn-sm btn-outline-primary-custom">Decline</button>
                      </form>
                    <?php else: ?>
                      <span class="text-muted small">Updated</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php foreach ($outgoing as $row): ?>
                <tr>
                  <td><?php echo e($row['owner_name']); ?></td>
                  <td><span class="badge text-bg-light border">Outgoing</span></td>
                  <td><?php echo e($row['skill_title']); ?> for <?php echo e($row['offered_skill']); ?></td>
                  <td><span class="swap-status <?php echo e($row['status']); ?>"><?php echo e(ucfirst($row['status'])); ?></span></td>
                  <td class="action-cell"><span class="text-muted small">Waiting for response</span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="mt-5">
        <h5 class="mb-3">My Skills</h5>
        <?php if ($skillError): ?><div class="alert alert-danger py-2 small"><?php echo e($skillError); ?></div><?php endif; ?>
        <?php if ($skillSuccess): ?><div class="alert alert-success py-2 small"><?php echo e($skillSuccess); ?></div><?php endif; ?>
        <div class="row g-3 mb-3">
          <?php if (empty($mySkills)): ?>
            <p class="text-muted small">You haven't listed a skill yet. Add one below so others can find you.</p>
          <?php else: foreach ($mySkills as $s): ?>
            <div class="col-md-4">
              <div class="skill-card">
                <span class="subject-tag"><?php echo e(ucfirst(str_replace('-', ' ', $s['category']))); ?></span>
                <span class="level-pill ms-1"><?php echo e($s['level']); ?></span>
                <h6 class="mt-2"><?php echo e($s['title']); ?></h6>
                <p class="small mb-0"><?php echo e($s['description']); ?></p>
              </div>
            </div>
          <?php endforeach; endif; ?>
        </div>
        <div class="form-panel">
          <h6 class="mb-3">Add a skill you can teach</h6>
          <form method="post" action="add_skill.php" class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Skill Title</label>
              <input type="text" name="title" class="form-control" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Category</label>
              <select name="category" class="form-select" required>
                <option value="programming">Programming</option>
                <option value="design">Design</option>
                <option value="academic">Academic</option>
                <option value="soft-skills">Soft Skills</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Level</label>
              <select name="level" class="form-select" required>
                <option value="Beginner">Beginner</option>
                <option value="Intermediate">Intermediate</option>
                <option value="Advanced">Advanced</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-primary-custom">Add Skill</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
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
            <textarea class="form-control" id="swapNote" rows="3"></textarea>
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
<script>
  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
      new bootstrap.Tooltip(el);
    });
  });
</script>
</body>
</html>