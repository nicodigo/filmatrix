/**
 * Recommendations.js
 *
 * Maneja el botón ✕ en las cards de recomendaciones.
 *
 * Al hacer click:
 *   1. Deshabilita el botón para evitar doble envío.
 *   2. POST /recommendations/discard con { title_id: N } (ID interno).
 *   3. Si el servidor responde { success: true }, anima la card y la elimina.
 *   4. Si falla, rehabilita el botón.
 *   5. Si ya no quedan cards, muestra mensaje de estado vacío.
 */

document.addEventListener('DOMContentLoaded', () => {
  const grid = document.querySelector('.films-grid');
  if (!grid) return;

  grid.addEventListener('click', async (e) => {
    const btn = e.target.closest('.reco-discard');
    if (!btn) return;

    // Evitar que el click propague al <a> de la card
    e.preventDefault();
    e.stopPropagation();

    const card    = btn.closest('.reco-card');
    const titleId = parseInt(btn.dataset.titleId, 10);

    if (!titleId || !card) return;

    btn.disabled = true;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    try {
      const res = await fetch('/recommendations/discard', {
        method:  'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-Token': csrfToken,
        },
        body:    JSON.stringify({ title_id: titleId }),
      });

      if (!res.ok) throw new Error(`HTTP ${res.status}`);

      const data = await res.json();

      if (data.success) {
        card.classList.add('reco-card--discarding');
        card.addEventListener('transitionend', () => {
          card.remove();
          checkEmpty(grid);
        }, { once: true });
      } else {
        btn.disabled = false;
        console.warn('Discard failed:', data.error ?? 'unknown');
      }
    } catch (err) {
      btn.disabled = false;
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
