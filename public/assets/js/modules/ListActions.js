/**
 * ListActions
 * Maneja la interacción AJAX para listas de películas.
 *
 * - Página "Mis Listas": crear / eliminar listas
 * - Página "Detalle de Lista": editar / eliminar lista
 *
 * Uso desde template:
 *   <script src="/assets/js/modules/ListActions.js" type="module"></script>
 */

/* ── Clase de acciones AJAX ── */

export class ListActions {
  #csrfToken;

  constructor(csrfToken) {
    this.#csrfToken = csrfToken;
  }

  async create(name, isPublic) {
    const res = await fetch('/my-lists', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': this.#csrfToken,
      },
      body: JSON.stringify({ name, is_public: isPublic }),
    });
    const data = await res.json();
    if (!data.success) throw new Error(data.error ?? 'create failed');
    return data;
  }

  async update(listId, name, isPublic) {
    const res = await fetch('/my-lists', {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': this.#csrfToken,
      },
      body: JSON.stringify({ list_id: listId, name, is_public: isPublic }),
    });
    const data = await res.json();
    if (!data.success) throw new Error(data.error ?? 'update failed');
    return data;
  }

  async remove(listId) {
    const res = await fetch('/my-lists', {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': this.#csrfToken,
      },
      body: JSON.stringify({ list_id: listId }),
    });
    const data = await res.json();
    if (!data.success) throw new Error(data.error ?? 'delete failed');
    return data;
  }

  async addItem(listId, titleId) {
    const res = await fetch('/my-lists/item', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': this.#csrfToken,
      },
      body: JSON.stringify({ list_id: listId, title_id: titleId }),
    });
    const data = await res.json();
    if (!data.success) throw new Error(data.error ?? 'addItem failed');
    return data;
  }

  async removeItem(listId, titleId) {
    const res = await fetch('/my-lists/item', {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': this.#csrfToken,
      },
      body: JSON.stringify({ list_id: listId, title_id: titleId }),
    });
    const data = await res.json();
    if (!data.success) throw new Error(data.error ?? 'removeItem failed');
    return data;
  }
}

/* ── Inicialización automática por página ── */

function init() {
  const csrfToken =
    document.querySelector('meta[name="csrf-token"]')?.content ?? '';

  const actions = new ListActions(csrfToken);

  /* ══════════════════════════════════════
     Página "Mis Listas" — /my-lists
  ══════════════════════════════════════ */
  const createBtn = document.getElementById('lists-create-btn');
  const createDialog = document.getElementById('list-create-dialog');

  if (createBtn && createDialog) {
    const form = document.getElementById('list-create-form');
    const closeBtn = document.getElementById('list-dialog-close');
    const cancelBtn = document.getElementById('list-dialog-cancel');
    const nameInput = document.getElementById('list-name-input');
    const publicCheckbox = document.getElementById('list-public-checkbox');

    createBtn.addEventListener('click', () => {
      nameInput.value = '';
      publicCheckbox.checked = true;
      createDialog.showModal();
    });

    const closeDialog = () => createDialog.close();
    closeBtn?.addEventListener('click', closeDialog);
    cancelBtn?.addEventListener('click', closeDialog);

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const name = nameInput.value.trim();
      if (!name) return;

      try {
        const result = await actions.create(name, publicCheckbox.checked);
        window.location.reload();
      } catch {
        /* silencioso */
      }
    });
  }

  // Delete buttons on list cards
  document.querySelectorAll('.list-card__delete-btn').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const listId = parseInt(btn.dataset.listId, 10);
      const listName = btn.dataset.listName;

      if (!confirm(`¿Eliminar la lista "${listName}"?`)) return;

      try {
        await actions.remove(listId);
        btn.closest('.list-card')?.remove();
      } catch {
        /* silencioso */
      }
    });
  });

  /* ══════════════════════════════════════
     Página "Detalle de Lista" — /my-lists/detail
  ══════════════════════════════════════ */
  const editBtn = document.getElementById('list-edit-btn');
  const editDialog = document.getElementById('list-edit-dialog');

  if (editBtn && editDialog) {
    const form = document.getElementById('list-edit-form');
    const closeBtn = document.getElementById('list-edit-dialog-close');
    const cancelBtn = document.getElementById('list-edit-cancel');
    const nameInput = document.getElementById('edit-name-input');
    const publicCheckbox = document.getElementById('edit-public-checkbox');

    editBtn.addEventListener('click', () => {
      editDialog.showModal();
    });

    const closeEditDialog = () => editDialog.close();
    closeBtn?.addEventListener('click', closeEditDialog);
    cancelBtn?.addEventListener('click', closeEditDialog);

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const listId = parseInt(form.querySelector('[name="list_id"]').value, 10);
      const name = nameInput.value.trim();
      if (!name) return;

      try {
        await actions.update(listId, name, publicCheckbox.checked);
        window.location.reload();
      } catch {
        /* silencioso */
      }
    });
  }

  // Delete button on list detail page
  const deleteBtn = document.getElementById('list-delete-btn');
  if (deleteBtn) {
    const listId = parseInt(
      document.querySelector('[name="list_id"]')?.value ?? '0',
      10,
    );

    deleteBtn.addEventListener('click', async () => {
      if (!confirm('¿Eliminar esta lista permanentemente?')) return;

      try {
        await actions.remove(listId);
        window.location.href = '/my-lists';
      } catch {
        /* silencioso */
      }
    });
  }
}

document.addEventListener('DOMContentLoaded', init);
