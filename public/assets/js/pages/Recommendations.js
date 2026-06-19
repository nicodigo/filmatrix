import { Toast } from '../modules/Toast.js';

/**
 * Recommendations.js
 *
 * Maneja el botón ✕ en las cards de recomendaciones.
 *
 * Al hacer click:
 *   1. Deshabilita el botón para evitar doble envío.
 *   2. POST /recommendations/discard con { title_id: N } (ID interno).
 *   3. Si el servidor responde { success: true }, anima la card y la elimina.
 *   4. Muestra un toast de éxito.
 *   5. Si falla, muestra un toast de error.
 */

document.addEventListener('DOMContentLoaded', () => {
  const grid = document.querySelector('.films-grid');
  if (!grid) return;

  grid.addEventListener('click', async (e) => {
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

      if (!res.ok) {
        throw new Error(`HTTP ${res.status}`);
      }

      const data = await res.json();

      if (data.success) {
        showToast('Título descartado con éxito', 'success');

        card.classList.add('reco-card--discarding');

        card.addEventListener(
          'transitionend',
          () => {
            card.remove();
            checkEmpty(grid);
          },
          { once: true }
        );
      } else {
        btn.disabled = false;

        showToast(
          data.error ?? 'No se pudo descartar el título',
          'error'
        );

        console.warn('Discard failed:', data.error ?? 'unknown');
      }
    } catch (err) {
      btn.disabled = false;

      showToast(
        'Ocurrió un error al descartar el título',
        'error'
      );

      console.error('Error al descartar:', err);
    }
  });
});

function checkEmpty(grid) {
  if (grid.querySelectorAll('.reco-card').length === 0) {
    grid.innerHTML = `
      <p class="films-empty">
        No quedan más sugerencias por ahora.
        Explorá el <a href="/titles">catálogo</a> y marcá más títulos como vistos.
      </p>
    `;
  }
}

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