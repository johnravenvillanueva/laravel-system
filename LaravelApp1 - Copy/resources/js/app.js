

document.addEventListener('DOMContentLoaded', () => {
  // Mobile sidebar toggle
  const sidebarToggle = document.querySelector('[data-toggle-sidebar]');
  const sidebar = document.querySelector('.sidebar');
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
  }

  // Mobile nav toggle
  const navToggle = document.querySelector('[data-toggle-nav]');
  const navLinks = document.querySelector('.nav-links');
  if (navToggle && navLinks) {
    navToggle.addEventListener('click', () => navLinks.classList.toggle('open'));
  }

  // Quantity steppers
  document.querySelectorAll('.qty').forEach(qty => {
    const input = qty.querySelector('input');
    qty.querySelector('[data-dec]')?.addEventListener('click', () => {
      const v = Math.max(1, parseInt(input.value) - 1);
      input.value = v; input.dispatchEvent(new Event('change'));
    });
    qty.querySelector('[data-inc]')?.addEventListener('click', () => {
      input.value = parseInt(input.value) + 1;
      input.dispatchEvent(new Event('change'));
    });
  });

  // Modal openers
  document.querySelectorAll('[data-modal-open]').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-modal-open');
      document.getElementById(id)?.classList.add('open');
    });
  });
  document.querySelectorAll('[data-modal-close]').forEach(btn => {
    btn.addEventListener('click', () => {
      btn.closest('.modal-backdrop')?.classList.remove('open');
    });
  });
  document.querySelectorAll('.modal-backdrop').forEach(bd => {
    bd.addEventListener('click', e => { if (e.target === bd) bd.classList.remove('open'); });
  });

  // Auto-dismiss flash
  document.querySelectorAll('.flash').forEach(el => {
    setTimeout(() => el.style.transition = 'opacity .5s', 3000);
    setTimeout(() => el.style.opacity = '0', 3500);
    setTimeout(() => el.remove(), 4200);
  });

  // Confirm delete
  document.querySelectorAll('form[data-confirm]').forEach(f => {
    f.addEventListener('submit', e => {
      if (!confirm(f.getAttribute('data-confirm'))) e.preventDefault();
    });
  });
});