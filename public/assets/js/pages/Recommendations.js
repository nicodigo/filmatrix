import { Toast } from '../modules/Toast.js';
import { Carousel } from '../modules/Carousel.js';

document.addEventListener('DOMContentLoaded', () => {
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.reco-discard');
    if (!btn) return;

    e.preventDefault();
    e.stopPropagation();

    const card = btn.closest('.reco-card');
    const titleId = parseInt(btn.dataset.titleId, 10);

    if (!titleId || !card) return;

    btn.disabled = true;

    const csrfToken =
      document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    try {
      const res = await fetch('/recommendations/discard', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-Token': csrfToken,
        },
        body: JSON.stringify({ title_id: titleId }),
      });

      const data = await res.json();

      if (data.success) {
        showToast('Título descartado con éxito', 'success');

        card.classList.add('reco-card--discarding');

        card.addEventListener(
          'transitionend',
          () => {
            card.remove();
          },
          { once: true }
        );
      } else {
        btn.disabled = false;
        showToast(data.error ?? 'No se pudo descartar', 'error');
      }
    } catch (err) {
      btn.disabled = false;
      showToast('Ocurrió un error', 'error');
    }
  });

  document.querySelectorAll('.carousel-wrapper').forEach(wrapper => {
    new Carousel(wrapper);
  });
});

function showToast(message, type = 'success') {
  const existingToast = document.getElementById('toast');

  if (existingToast) {
    existingToast.remove();
  }

  const icon =
    type === 'success'
      ? `
        <svg xmlns="http://www.w3.org/2000/svg"
             width="18"
             height="18"
             viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="2"
             stroke-linecap="round"
             stroke-linejoin="round"
             aria-hidden="true">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
          <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
      `
      : `
        <svg xmlns="http://www.w3.org/2000/svg"
             width="18"
             height="18"
             viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="2"
             stroke-linecap="round"
             stroke-linejoin="round"
             aria-hidden="true">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="8" x2="12" y2="12"/>
          <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
      `;

  const toast = document.createElement('div');

  toast.id = 'toast';
  toast.className = `toast toast--${type}`;
  toast.setAttribute('role', 'alert');

  toast.innerHTML = `
    ${icon}
    ${message}
  `;

  document.body.appendChild(toast);

  new Toast();
}