document.addEventListener("DOMContentLoaded", function () {
  highlightActiveNavLink();
  initSkillTicker();
  initSmoothScroll();
  initBadgeModal();
  initSkillFilters();
  initSkillMatcher();
  initRequestSwapModal();
  initContactForm();
  initLoginForm();
  initSwapRequestsTable();
  initBadgeProgress();
});


function highlightActiveNavLink() {
  var current = window.location.pathname.split("/").pop() || "index.html";
  document.querySelectorAll(".navbar-nav .nav-link").forEach(function (link) {
    var href = link.getAttribute("href");
    if (href === current) link.classList.add("active");
  });
}


function initSkillTicker() {
  var tickerEl = document.getElementById("tickerText");
  if (!tickerEl) return;

  var students = ["Kasun", "Nimali", "Dinuka", "Sachini", "Tharindu", "Ishara", "Ruwan", "Amaya"];
  var skillPairs = [
    ["Java", "Web Design"],
    ["Statistics", "Python"],
    ["Public Speaking", "Excel"],
    ["Networking Basics", "UI Design"],
    ["Database Design", "Photography"],
    ["English Essay Writing", "Git & GitHub"],
    ["Circuit Analysis", "Video Editing"]
  ];

  function randomFrom(arr) { return arr[Math.floor(Math.random() * arr.length)]; }

  function renderTick() {
    var name = randomFrom(students);
    var pair = randomFrom(skillPairs);
    var minutesAgo = Math.floor(Math.random() * 12) + 1;
    tickerEl.classList.remove("ticker-fade");
    // force reflow so the animation can re-trigger
    void tickerEl.offsetWidth;
    tickerEl.textContent = name + " swapped " + pair[0] + " for " + pair[1] + " \u2014 " + minutesAgo + " min" + (minutesAgo === 1 ? "" : "s") + " ago";
    tickerEl.classList.add("ticker-fade");
  }

  renderTick();
  setInterval(renderTick, 3000);
}


function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(function (link) {
    link.addEventListener("click", function (e) {
      var targetId = this.getAttribute("href");
      if (targetId.length < 2) return;
      var target = document.querySelector(targetId);
      if (!target) return;
      e.preventDefault();
      target.scrollIntoView({ behavior: "smooth", block: "start" });
    });
  });
}


function initBadgeModal() {
  var modalEl = document.getElementById("badgeInfoModal");
  if (!modalEl || typeof bootstrap === "undefined") return;

  var modal = new bootstrap.Modal(modalEl);
  var titleEl = document.getElementById("badgeInfoTitle");
  var bodyEl = document.getElementById("badgeInfoBody");

  document.querySelectorAll(".skill-badge[data-badge-reason]").forEach(function (badge) {
    badge.addEventListener("click", function () {
      titleEl.textContent = badge.getAttribute("data-badge-name") || "Badge";
      bodyEl.textContent = badge.getAttribute("data-badge-reason");
      modal.show();
    });
  });
}


function initSkillFilters() {
  var categorySel = document.getElementById("filterCategory");
  var levelSel = document.getElementById("filterLevel");
  var badgeSel = document.getElementById("filterBadge");
  var searchInput = document.getElementById("filterSearch");
  var resultCount = document.getElementById("resultCount");
  var cards = document.querySelectorAll(".skill-card-col");
  if (!cards.length) return;

  function applyFilters() {
    var category = categorySel ? categorySel.value : "all";
    var level = levelSel ? levelSel.value : "all";
    var badge = badgeSel ? badgeSel.value : "all";
    var query = searchInput ? searchInput.value.trim().toLowerCase() : "";
    var visible = 0;

    cards.forEach(function (col) {
      var matchesCategory = category === "all" || col.dataset.category === category;
      var matchesLevel = level === "all" || col.dataset.level === level;
      var matchesBadge = badge === "all" || col.dataset.badges.indexOf(badge) !== -1;
      var matchesQuery = query === "" || col.dataset.title.toLowerCase().indexOf(query) !== -1;
      var show = matchesCategory && matchesLevel && matchesBadge && matchesQuery;
      col.classList.toggle("filtered-out", !show);
      if (show) visible++;
    });

    if (resultCount) resultCount.textContent = visible + (visible === 1 ? " skill" : " skills") + " found";
  }

  [categorySel, levelSel, badgeSel].forEach(function (el) {
    if (el) el.addEventListener("change", applyFilters);
  });
  if (searchInput) searchInput.addEventListener("input", applyFilters);

  applyFilters();
}


function initSkillMatcher() {
  var findBtn = document.getElementById("findMatchBtn");
  if (!findBtn) return;

  var wantSel = document.getElementById("matchWant");
  var teachSel = document.getElementById("matchTeach");
  var resultZone = document.getElementById("matchResultZone");

  var candidatePool = [
    { name: "Kasun Perera", university: "Faculty of Applied Sciences", teaches: "Java", learns: "Web Design", badge: "Verified Tutor" },
    { name: "Nimali Rathnayake", university: "Faculty of Management", teaches: "Statistics", learns: "Python", badge: "Top Rated" },
    { name: "Dinuka Fernando", university: "Faculty of Applied Sciences", teaches: "Web Design", learns: "Public Speaking", badge: "Verified Tutor" },
    { name: "Sachini Wijesuriya", university: "Faculty of Social Sciences", teaches: "English Essay Writing", learns: "Excel", badge: "Top Rated" },
    { name: "Tharindu Gunasekara", university: "Faculty of Applied Sciences", teaches: "Python", learns: "UI Design", badge: "Verified Tutor" },
    { name: "Ishara De Silva", university: "Faculty of Management", teaches: "Excel", learns: "Java", badge: "Newcomer" }
  ];

  findBtn.addEventListener("click", function () {
    var wantValue = wantSel.value;
    var teachValue = teachSel.value;

    if (!wantValue || !teachValue) {
      resultZone.innerHTML = '<p class="text-warning mb-0 small">Please select both a skill you want to learn and a skill you can teach.</p>';
      return;
    }

    var best = candidatePool.find(function (c) { return c.teaches === wantValue; }) || candidatePool[0];

    var base = 70;
    if (best.teaches === wantValue) base += 18;
    if (best.learns === teachValue) base += 7;
    var pct = Math.min(99, base + Math.floor(Math.random() * 6));

    var initials = best.name.split(" ").map(function (n) { return n[0]; }).join("");

    var badgeClass = best.badge === "Verified Tutor" ? "badge-verified" : (best.badge === "Top Rated" ? "badge-top" : "badge-newcomer");

    resultZone.innerHTML =
      '<div class="match-result-card">' +
        '<div class="match-percent-ring" style="--pct:' + pct + '" data-pct="' + pct + '"></div>' +
        '<div class="flex-grow-1">' +
          '<div class="d-flex align-items-center gap-2 mb-1 flex-wrap">' +
            '<strong>' + best.name + '</strong>' +
            '<span class="skill-badge ' + badgeClass + '">' + best.badge + '</span>' +
          '</div>' +
          '<div class="text-muted small mb-1">' + best.university + '</div>' +
          '<div class="small">Teaches <strong>' + best.teaches + '</strong> &middot; Learning <strong>' + best.learns + '</strong></div>' +
          '<div class="fw-semibold small mt-1" style="color:var(--teal)">' + pct + '% Match Found!</div>' +
        '</div>' +
        '<button type="button" class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#swapModal">Request Swap</button>' +
      '</div>';
  });
}


function initRequestSwapModal() {
  var swapModal = document.getElementById("swapModal");
  if (!swapModal) return;

  swapModal.addEventListener("show.bs.modal", function (event) {
    var trigger = event.relatedTarget;
    var titleInput = document.getElementById("swapSkillName");
    if (trigger && titleInput) {
      var skillTitle = trigger.getAttribute("data-skill-title");
      if (skillTitle) titleInput.value = skillTitle;
    }
  });

  var swapForm = document.getElementById("swapForm");
  if (swapForm) {
    swapForm.addEventListener("submit", function (e) {
      e.preventDefault();
      var confirmEl = document.getElementById("swapConfirmMsg");
      if (confirmEl) {
        confirmEl.classList.remove("d-none");
        setTimeout(function () {
          var modalInstance = bootstrap.Modal.getInstance(swapModal);
          if (modalInstance) modalInstance.hide();
          confirmEl.classList.add("d-none");
          swapForm.reset();
        }, 1400);
      }
    });
  }
}


function initContactForm() {
  var form = document.getElementById("contactForm");
  if (!form) return;

  var nameInput = document.getElementById("contactName");
  var emailInput = document.getElementById("contactEmail");
  var messageInput = document.getElementById("contactMessage");
  var successBox = document.getElementById("contactSuccess");

  function showError(input, show) {
    var errorEl = document.getElementById(input.id + "Error");
    input.classList.toggle("is-invalid-custom", show);
    if (errorEl) errorEl.classList.toggle("show", show);
  }

  function isValidEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
  }

  [nameInput, emailInput, messageInput].forEach(function (input) {
    input.addEventListener("input", function () { validateField(input); });
  });

  function validateField(input) {
    var valid = true;
    if (input === nameInput) valid = nameInput.value.trim().length >= 2;
    if (input === emailInput) valid = isValidEmail(emailInput.value.trim());
    if (input === messageInput) valid = messageInput.value.trim().length >= 10;
    showError(input, !valid);
    return valid;
  }

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    var validName = validateField(nameInput);
    var validEmail = validateField(emailInput);
    var validMessage = validateField(messageInput);

    if (validName && validEmail && validMessage) {
      successBox.classList.remove("d-none");
      form.reset();
      [nameInput, emailInput, messageInput].forEach(function (i) { i.classList.remove("is-invalid-custom"); });
      setTimeout(function () { successBox.classList.add("d-none"); }, 4000);
    } else {
      successBox.classList.add("d-none");
    }
  });
}


function initLoginForm() {
  var loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      e.preventDefault();
      var email = document.getElementById("loginEmail");
      var password = document.getElementById("loginPassword");
      var valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim()) && password.value.length >= 6;
      email.classList.toggle("is-invalid-custom", !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim()));
      password.classList.toggle("is-invalid-custom", password.value.length < 6);
      var msg = document.getElementById("loginMsg");
      if (valid) {
        msg.className = "small mt-2 text-success";
        msg.textContent = "Signed in successfully. Redirecting to your dashboard \u2026";
        setTimeout(function () { window.location.href = "dashboard.html"; }, 900);
      } else {
        msg.className = "small mt-2 text-danger";
        msg.textContent = "Please enter a valid university email and a password of at least 6 characters.";
      }
    });
  }

  var registerForm = document.getElementById("registerForm");
  if (registerForm) {
    registerForm.addEventListener("submit", function (e) {
      e.preventDefault();
      var msg = document.getElementById("registerMsg");
      msg.className = "small mt-2 text-success";
      msg.textContent = "Account created. You can now log in above.";
      registerForm.reset();
    });
  }
}


function initSwapRequestsTable() {
  var table = document.getElementById("swapRequestsTable");
  if (!table) return;

  table.addEventListener("click", function (e) {
    var btn = e.target.closest("button[data-action]");
    if (!btn) return;
    var row = btn.closest("tr");
    var statusCell = row.querySelector(".swap-status");
    var actionCell = row.querySelector(".action-cell");

    if (btn.dataset.action === "accept") {
      statusCell.textContent = "Accepted";
      statusCell.className = "swap-status accepted";
    } else {
      statusCell.textContent = "Declined";
      statusCell.className = "swap-status declined";
    }
    actionCell.innerHTML = '<span class="text-muted small">Updated</span>';
  });
}


function initBadgeProgress() {
  var fill = document.getElementById("badgeProgressFill");
  if (!fill) return;
  var target = fill.getAttribute("data-target") || "0";
  requestAnimationFrame(function () {
    fill.style.width = target + "%";
  });
}