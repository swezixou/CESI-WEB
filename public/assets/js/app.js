/* ============================================================
   StageConnect – app.js
   ============================================================ */

'use strict';

// ── Navbar scroll effect ────────────────────────────────────
const navbar = document.getElementById('navbar');
if (navbar) {
  window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 40);
  }, { passive: true });
}

// ── Mobile menu ─────────────────────────────────────────────
function toggleMenu() {
  const menu   = document.getElementById('mobileMenu');
  const burger = document.getElementById('burger');
  if (!menu || !burger) return;

  const isOpen = menu.classList.toggle('open');
  const spans  = burger.querySelectorAll('span');

  if (isOpen) {
    spans[0].style.transform = 'rotate(45deg) translate(5px,5px)';
    spans[1].style.opacity   = '0';
    spans[2].style.transform = 'rotate(-45deg) translate(5px,-5px)';
  } else {
    spans.forEach(s => { s.style.transform = ''; s.style.opacity = ''; });
  }
}

// Fermer menu mobile en cliquant en dehors
document.addEventListener('click', e => {
  const menu = document.getElementById('mobileMenu');
  const burger = document.getElementById('burger');
  if (menu && menu.classList.contains('open') && !menu.contains(e.target) && !burger.contains(e.target)) {
    toggleMenu();
  }
});

// ── Auto-dismiss flash messages ─────────────────────────────
document.querySelectorAll('.flash').forEach(el => {
  setTimeout(() => el.style.opacity = '0', 4500);
  setTimeout(() => el.remove(), 5000);
});

// ── Confirm delete forms ────────────────────────────────────
document.querySelectorAll('form[data-confirm]').forEach(form => {
  form.addEventListener('submit', e => {
    if (!confirm(form.dataset.confirm)) e.preventDefault();
  });
});

// ── Skill checkbox toggle ───────────────────────────────────
document.querySelectorAll('.skill-check').forEach(label => {
  label.addEventListener('click', function(e) {
    e.preventDefault();
    const cb = this.querySelector('input[type="checkbox"]');
    if (cb) {
      cb.checked = !cb.checked;
      this.classList.toggle('checked', cb.checked);
    }
  });
});

// ── Character counter for textareas ────────────────────────
document.querySelectorAll('textarea[data-counter]').forEach(ta => {
  const countEl = document.getElementById(ta.dataset.counter);
  if (countEl) {
    ta.addEventListener('input', () => {
      countEl.textContent = ta.value.length + ' caractères';
    });
  }
});

// ── Wish-list AJAX (optionnel si JS activé) ─────────────────
document.querySelectorAll('.wish-ajax').forEach(btn => {
  btn.addEventListener('click', async function(e) {
    e.preventDefault();
    const form  = this.closest('form');
    const action= form.action;
    const token = form.querySelector('input[name="_csrf_token"]')?.value;

    try {
      const res  = await fetch(action, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: `_csrf_token=${encodeURIComponent(token)}`
      });
      const data = await res.json();
      if (data.status === 'added') {
        this.textContent = '❤️';
        this.classList.add('active');
      } else {
        this.textContent = '🤍';
        this.classList.remove('active');
      }
    } catch {
      form.submit(); // fallback
    }
  });
});

// ── Modal helpers ───────────────────────────────────────────
function openModal(id)  { const m = document.getElementById(id); if (m) m.style.display = 'flex'; }
function closeModal(id) { const m = document.getElementById(id); if (m) m.style.display = 'none'; }

document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
  });
});

// Fermer avec Echap
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
  }
});

// ── Front validation générique (STx 3) ──────────────────────
document.querySelectorAll('form.validate').forEach(form => {
  form.addEventListener('submit', function(e) {
    let valid = true;

    this.querySelectorAll('[required]').forEach(field => {
      const errEl = document.getElementById(field.id + 'Error');
      field.classList.remove('is-invalid');
      if (errEl) errEl.textContent = '';

      if (!field.value.trim()) {
        field.classList.add('is-invalid');
        if (errEl) errEl.textContent = 'Ce champ est obligatoire.';
        valid = false;
      } else if (field.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
        field.classList.add('is-invalid');
        if (errEl) errEl.textContent = 'Adresse email invalide.';
        valid = false;
      }
    });

    if (!valid) e.preventDefault();
  });
});

// ── Scroll fade-in animations ────────────────────────────────
const observer = new IntersectionObserver(entries => {
  entries.forEach((entry, i) => {
    if (entry.isIntersecting) {
      setTimeout(() => entry.target.classList.add('visible'), i * 70);
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));

// ── Data table row click → follow link ──────────────────────
document.querySelectorAll('.data-table tbody tr[data-href]').forEach(row => {
  row.style.cursor = 'pointer';
  row.addEventListener('click', function(e) {
    if (!e.target.closest('a, button, form')) {
      window.location.href = this.dataset.href;
    }
  });
});
