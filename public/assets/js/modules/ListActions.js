import { Toast } from './Toast.js';

/**
 * ListActions
 * Maneja la interacción AJAX para listas de películas
 */

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

/* ─────────────────────────────────────
   TOAST
───────────────────────────────────── */

function showToast(message, type = 'success') {
  const existing = document.getElementById('toast');
  if (existing) existing.remove();

  const toast = document.createElement('div');
  toast.id = 'toast';
  toast.className = `toast toast--${type}`;
  toast.setAttribute('role', 'alert');

  toast.innerHTML = message;

  document.body.appendChild(toast);

  requestAnimationFrame(() => {
    new Toast();
  });
}

/* 🔥 FIX CRÍTICO: esperar render real del browser */
function nextPaint(callback) {
  requestAnimationFrame(() => {
    requestAnimationFrame(callback);
  });
}

/* ─────────────────────────────────────
   INIT
───────────────────────────────────── */

function init() {
  const csrfToken =
    document.querySelector('meta[name="csrf-token"]')?.content ?? '';

  const actions = new ListActions(csrfToken);

  /* ═════════════════════════════
     MIS LISTAS
  ═════════════════════════════ */

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
        await actions.create(name, publicCheckbox.checked);

        showToast('Lista creada con éxito');

        nextPaint(() => {
          window.location.reload();
        });

      } catch {
        showToast('No se pudo crear la lista', 'error');
      }
    });
  }

  /* DELETE LIST */
  document.querySelectorAll('.list-card__delete-btn').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const listId = parseInt(btn.dataset.listId, 10);
      const listName = btn.dataset.listName;

      if (!confirm(`¿Eliminar la lista "${listName}"?`)) return;

      try {
        await actions.remove(listId);

        showToast('Lista eliminada con éxito');

        btn.closest('.list-card')?.remove();

      } catch {
        showToast('No se pudo eliminar la lista', 'error');
      }
    });
  });

  /* ═════════════════════════════
     DETALLE LISTA
  ═════════════════════════════ */

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

      const listId = parseInt(
        form.querySelector('[name="list_id"]').value,
        10,
      );

      const name = nameInput.value.trim();
      if (!name) return;

      try {
        await actions.update(listId, name, publicCheckbox.checked);

        showToast('Lista modificada con éxito');

        nextPaint(() => {
          window.location.reload();
        });

      } catch {
        showToast('No se pudo modificar la lista', 'error');
      }
    });
  }

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

        showToast('Lista eliminada con éxito');

        nextPaint(() => {
          window.location.href = '/my-lists';
        });

      } catch {
        showToast('No se pudo eliminar la lista', 'error');
      }
    });
  }
}

document.addEventListener('DOMContentLoaded', init);