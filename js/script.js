// INDEX JS


const tickerEvents = [
  "Kasun swapped Java for Web Design — 2 mins ago",
  "Ishara taught Photography to 3 students — 5 mins ago",
  "Ruwan matched with a Guitar tutor — 9 mins ago",
  "Dilani earned the Verified Tutor badge — 12 mins ago",
  "Chamara swapped Python for French lessons — 15 mins ago",
  "Sanduni booked a Public Speaking session — 18 mins ago",
  "Hasitha reached 20+ teaching hours — 21 mins ago",
  "Yashodha requested an HTML swap — 24 mins ago",
  "Tharindu taught a Cooking workshop — 27 mins ago",
  "Nimasha hit a 4.9 average rating — 30 mins ago"
];

let tickerIndex = 0;
const tickerEl = document.getElementById('tickerText');

function showTickerItem(){
  tickerEl.textContent = tickerEvents[tickerIndex];
  tickerEl.classList.add('show');
}

function rotateTicker(){
  tickerEl.classList.remove('show'); 
  setTimeout(() => {
    tickerIndex = (tickerIndex + 1) % tickerEvents.length;
    showTickerItem(); // fade in with new text
  }, 400);
}

document.addEventListener('DOMContentLoaded', function(){
  showTickerItem();
  setInterval(rotateTicker, 3000);
});


const testimonialCarouselEl = document.getElementById('testimonialCarousel');
const testimonialCarousel = new bootstrap.Carousel(testimonialCarouselEl, {
  interval: 4500,
  ride: 'carousel',
  pause: 'hover',
  wrap: true
});


testimonialCarouselEl.addEventListener('mouseenter', () => testimonialCarousel.pause());
testimonialCarouselEl.addEventListener('mouseleave', () => testimonialCarousel.cycle());




// skill js
    
const skillsData = [
  { id:1, name:'Kasun Silva', avatar:'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=150&q=80', skill:'Java', category:'Programming', level:'Advanced', rating:4.9, hours:40, badges:['verified','top-rated'] },
  { id:2, name:'Ishara Fernando', avatar:'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=150&q=80', skill:'Photography', category:'Design', level:'Intermediate', rating:4.7, hours:22, badges:['verified'] },
  { id:3, name:'Ruwan Jayasuriya', avatar:'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&q=80', skill:'Public Speaking', category:'Language', level:'Advanced', rating:4.8, hours:30, badges:['top-rated'] },
  { id:4, name:'Dilani Perera', avatar:'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=150&q=80', skill:'Guitar', category:'Music', level:'Beginner', rating:4.5, hours:8, badges:[] },
  { id:5, name:'Chamara Wickramasinghe', avatar:'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=150&q=80', skill:'Python', category:'Programming', level:'Intermediate', rating:4.9, hours:55, badges:['verified','top-rated'] },
  { id:6, name:'Sanduni Rajapaksha', avatar:'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=150&q=80&flip=h', skill:'French', category:'Language', level:'Beginner', rating:4.6, hours:12, badges:['verified'] },
  { id:7, name:'Hasitha Bandara', avatar:'https://images.unsplash.com/photo-1531891437562-4301cf35b7e4?auto=format&fit=crop&w=150&q=80', skill:'Graphic Design', category:'Design', level:'Advanced', rating:4.4, hours:18, badges:[] },
  { id:8, name:'Yashodha Gunasekara', avatar:'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=150&q=80&flip=h', skill:'HTML & CSS', category:'Programming', level:'Beginner', rating:4.3, hours:6, badges:[] },
  { id:9, name:'Tharindu Wijesinghe', avatar:'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=150&q=80&flip=h', skill:'Cooking', category:'Lifestyle', level:'Intermediate', rating:4.7, hours:25, badges:['verified'] },
  { id:10, name:'Nimasha Kodithuwakku', avatar:'https://images.unsplash.com/photo-1531891437562-4301cf35b7e4?auto=format&fit=crop&w=150&q=80&flip=h', skill:'Yoga', category:'Lifestyle', level:'Advanced', rating:4.9, hours:60, badges:['verified','top-rated'] }
];

const badgeInfo = {
  'verified':  { icon:'bi-patch-check-fill', cls:'verified',   title:'Verified Tutor', descTemplate: h => `${h}+ Hours Taught, verified by the Skill-Link community.` },
  'top-rated': { icon:'bi-star-fill',        cls:'top-rated',  title:'Top Rated',      descTemplate: r => `${r} Average Rating from students they've taught.` }
};


let currentPage = 1;
const perPage = 6;

function badgeChipHTML(card){
  return card.badges.map(b => {
    const info = badgeInfo[b];
    const chipClass = b === 'verified' ? 'mint' : '';
    return `<span class="badge-chip-btn ${chipClass}"
              data-badge="${b}" data-hours="${card.hours}" data-rating="${card.rating}" data-name="${card.name}"
              onclick="openBadgeModal(this)">
              <i class="bi ${info.icon}"></i> ${info.title}
            </span>`;
  }).join('');
}

function cardHTML(card){
  return `
  <div class="col-md-6 col-xl-4">
    <div class="skill-card">
      <div class="top-row">
        <img src="${card.avatar}" alt="${card.name}">
        <div>
          <p class="student-name mb-0">${card.name}</p>
          <p class="student-skill mb-0">Teaches <strong>${card.skill}</strong></p>
        </div>
      </div>
      <div class="badges-row">${badgeChipHTML(card) || '<span class="text-muted small">No badges yet</span>'}</div>
      <div class="tag-row">
        <span class="tag-pill">${card.category}</span>
        <span class="tag-pill level">${card.level}</span>
      </div>
      <div class="meta-row">
        <span class="rating"><i class="bi bi-star-fill me-1"></i>${card.rating}</span>
        <span>${card.hours}+ hrs taught</span>
      </div>
      <button class="btn-request-swap" onclick="openSwapModal('${card.name.replace(/'/g,"\\'")}', '${card.skill.replace(/'/g,"\\'")}')">
        <i class="bi bi-arrow-left-right me-1"></i>Request Swap
      </button>
    </div>
  </div>`;
}

function getFilteredData(){
  const search = document.getElementById('searchInput').value.trim().toLowerCase();
  const checkedCats = [...document.querySelectorAll('.filter-category:checked')].map(c => c.value);
  const level = document.querySelector('input[name="levelFilter"]:checked').value;
  const checkedBadges = [...document.querySelectorAll('.filter-badge:checked')].map(c => c.value);

  return skillsData.filter(card => {
    const matchesSearch = !search || card.skill.toLowerCase().includes(search) || card.name.toLowerCase().includes(search);
    const matchesCat = checkedCats.length === 0 || checkedCats.includes(card.category);
    const matchesLevel = level === 'All' || card.level === level;
    const matchesBadge = checkedBadges.length === 0 || checkedBadges.every(b => card.badges.includes(b));
    return matchesSearch && matchesCat && matchesLevel && matchesBadge;
  });
}

function renderGrid(){
  const filtered = getFilteredData();
  const grid = document.getElementById('skillsGrid');
  const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
  if (currentPage > totalPages) currentPage = totalPages;

  const start = (currentPage - 1) * perPage;
  const pageItems = filtered.slice(start, start + perPage);

  document.getElementById('resultsCount').textContent =
    filtered.length === 0 ? 'No tutors match your filters' : `Showing ${pageItems.length} of ${filtered.length} tutor${filtered.length !== 1 ? 's' : ''}`;

  grid.innerHTML = pageItems.length
    ? pageItems.map(cardHTML).join('')
    : `<div class="col-12"><div class="empty-state"><i class="bi bi-emoji-frown"></i>Try adjusting your search or filters.</div></div>`;

  renderPagination(totalPages);
}

function renderPagination(totalPages){
  const list = document.getElementById('paginationList');
  if (totalPages <= 1){ list.innerHTML = ''; return; }
  let html = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="goToPage(${currentPage - 1});return false;"><i class="bi bi-chevron-left"></i></a>
              </li>`;
  for (let p = 1; p <= totalPages; p++){
    html += `<li class="page-item ${p === currentPage ? 'active' : ''}">
               <a class="page-link" href="#" onclick="goToPage(${p});return false;">${p}</a>
             </li>`;
  }
  html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
             <a class="page-link" href="#" onclick="goToPage(${currentPage + 1});return false;"><i class="bi bi-chevron-right"></i></a>
           </li>`;
  list.innerHTML = html;
}

function goToPage(p){ currentPage = p; renderGrid(); window.scrollTo({top: document.getElementById('skillsGrid').offsetTop - 100, behavior:'smooth'}); }
function applyFilters(){ currentPage = 1; renderGrid(); }

document.getElementById('searchInput').addEventListener('input', applyFilters);
document.querySelectorAll('.filter-category, .filter-badge').forEach(el => el.addEventListener('change', applyFilters));
document.querySelectorAll('input[name="levelFilter"]').forEach(el => el.addEventListener('change', applyFilters));


function openBadgeModal(chip){
  const key = chip.dataset.badge;
  const info = badgeInfo[key];
  const hours = chip.dataset.hours;
  const rating = chip.dataset.rating;
  const student = chip.dataset.name;

  document.getElementById('badgeModalIcon').className = `badge-modal-icon mx-auto ${info.cls}`;
  document.getElementById('badgeModalIcon').innerHTML = `<i class="bi ${info.icon}"></i>`;
  document.getElementById('badgeModalTitle').textContent = info.title;
  document.getElementById('badgeModalStudent').textContent = `Earned by ${student}`;
  document.getElementById('badgeModalDesc').textContent = key === 'verified'
    ? info.descTemplate(hours)
    : info.descTemplate(rating);

  new bootstrap.Modal(document.getElementById('badgeModal')).show();
}


function openSwapModal(studentName, skillName){
  document.getElementById('swapStudentName').value = studentName;
  document.getElementById('swapSkillName').value = skillName;
  document.getElementById('swapForm').reset();
  document.getElementById('swapStudentName').value = studentName;
  document.getElementById('swapSkillName').value = skillName;
  new bootstrap.Modal(document.getElementById('swapModal')).show();
}

document.getElementById('swapForm').addEventListener('submit', function(e){
  e.preventDefault();
  const student = document.getElementById('swapStudentName').value;
  bootstrap.Modal.getInstance(document.getElementById('swapModal')).hide();
  showToast(`Swap request sent to ${student}!`);
});

function showToast(message){
  const id = 'toast-' + Date.now();
  const html = `
    <div id="${id}" class="toast align-items-center text-white border-0 mb-2" role="alert" style="background:var(--sl-mint);">
      <div class="d-flex">
        <div class="toast-body"><i class="bi bi-check-circle me-2"></i>${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>`;
  document.getElementById('toastArea').insertAdjacentHTML('beforeend', html);
  const toastEl = document.getElementById(id);
  const toast = new bootstrap.Toast(toastEl, { delay: 3500 });
  toast.show();
  toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}


const allSkillNames = [...new Set(skillsData.map(c => c.skill))].sort();

function populateMatcherDropdowns(){
  const wantSel = document.getElementById('wantSkill');
  const teachSel = document.getElementById('teachSkill');
  allSkillNames.forEach(skill => {
    wantSel.insertAdjacentHTML('beforeend', `<option value="${skill}">${skill}</option>`);
    teachSel.insertAdjacentHTML('beforeend', `<option value="${skill}">${skill}</option>`);
  });
}

function findMatch(){
  const want = document.getElementById('wantSkill').value;
  const teach = document.getElementById('teachSkill').value;
  const resultWrap = document.getElementById('matchResult');
  const content = document.getElementById('matchContent');

  if (!want || !teach){
    content.innerHTML = `<p class="no-match-msg mb-0"><i class="bi bi-exclamation-circle me-1"></i>Please select both a skill to learn and a skill to teach.</p>`;
    resultWrap.classList.add('show');
    return;
  }

  
  const candidates = skillsData.filter(c => c.skill === want).sort((a,b) => b.rating - a.rating);

  if (candidates.length === 0){
    content.innerHTML = `<p class="no-match-msg mb-0"><i class="bi bi-emoji-neutral me-1"></i>No tutors currently teach "${want}". Try another skill.</p>`;
    resultWrap.classList.add('show');
    return;
  }

  const match = candidates[0];
  const basePct = Math.round(70 + (match.rating - 4) * 20 + Math.random() * 6);
  const pct = Math.min(99, Math.max(65, basePct));

  content.innerHTML = `
    <div class="match-pct-line fade-slide" id="pctLine">
      <span class="match-pct-num" id="pctNum">0%</span>
      <span class="match-pct-label">Match Found!</span>
    </div>
    <div class="matched-student-card fade-slide" id="matchedCard">
      <img src="${match.avatar}" alt="${match.name}">
      <div class="flex-grow-1">
        <p class="name mb-0">${match.name}</p>
        <p class="swap-line mb-0">Teaches <strong>${want}</strong><span class="swap-arrow">⇄</span>You teach <strong>${teach}</strong></p>
      </div>
      <button class="btn btn-find-match" onclick="openSwapModal('${match.name.replace(/'/g,"\\'")}', '${want.replace(/'/g,"\\'")}')">Request Swap</button>
    </div>
  `;

  resultWrap.classList.add('show');

  requestAnimationFrame(() => {
    document.getElementById('pctLine').classList.add('in');
    setTimeout(() => document.getElementById('matchedCard').classList.add('in'), 150);
    animateCount(document.getElementById('pctNum'), pct);
  });
}

function animateCount(el, target){
  let current = 0;
  const step = Math.max(1, Math.round(target / 24));
  const timer = setInterval(() => {
    current += step;
    if (current >= target){ current = target; clearInterval(timer); }
    el.textContent = current + '%';
  }, 20);
}

function handleLogout(){
  window.location.href = 'index.html';
}


document.addEventListener('DOMContentLoaded', function(){
  populateMatcherDropdowns();
  renderGrid();
});
// dashboard js

document.addEventListener('DOMContentLoaded', function () {
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.forEach(function (el) {
    new bootstrap.Tooltip(el);
  });
});

 function setStatus(btn, newStatus) {
  var row = btn.closest('[data-row]');
  var statusCell = row.querySelector('[data-status]');
  var actionsCell = row.querySelector('[data-actions]');

  if (newStatus === 'accepted') {
    statusCell.innerHTML = '<span class="status-badge status-accepted">Accepted</span>';
    actionsCell.innerHTML = '<span class="text-muted small"><i class="bi bi-check-circle me-1"></i>Request confirmed</span>';
  } else if (newStatus === 'declined') {
    statusCell.innerHTML = '<span class="status-badge status-declined">Declined</span>';
    actionsCell.innerHTML = '<span class="text-muted small"><i class="bi bi-x-circle me-1"></i>Request closed</span>';
  }
}


function connectPeer(btn, name) {
  btn.textContent = 'Request Sent';
  btn.classList.add('is-sent');
}

// Placeholder logout handler
function handleLogout() {
  // In Phase 3 this will call auth/logout.php to destroy the session
  window.location.href = 'index.html';
}