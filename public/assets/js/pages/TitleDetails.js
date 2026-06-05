import { WatchlistActions } from "../modules/WatchlistActions.js";
import { ListActions } from "../modules/ListActions.js";

const csrfToken =
  document.querySelector('meta[name="csrf-token"]')?.content ?? "";

// --- Watchlist ---
const watchlistSection = document.getElementById("watchlistSection");

if (watchlistSection) {
  const actions = new WatchlistActions(csrfToken);
  const titleId = parseInt(watchlistSection.dataset.titleId, 10);

  const addState = document.getElementById("watchlistAddState");
  const inState = document.getElementById("watchlistInState");
  const addBtn = document.getElementById("watchlistAdd");
  const statusSel = document.getElementById("watchlistStatus");
  const removeBtn = document.getElementById("watchlistRemove");

  addBtn?.addEventListener("click", async () => {
    try {
      await actions.add(titleId);
      addState.hidden = true;
      inState.hidden = false;
      statusSel.value = "pending";
    } catch {
      /* silencioso */
    }
  });

  if (statusSel) {
    statusSel.dataset.prev = statusSel.value;
    statusSel.addEventListener("change", async (e) => {
      try {
        await actions.updateStatus(titleId, e.target.value);
        statusSel.dataset.prev = e.target.value;
      } catch {
        statusSel.value = statusSel.dataset.prev;
      }
    });
  }

  removeBtn?.addEventListener("click", async () => {
    try {
      await actions.remove(titleId);
      inState.hidden = true;
      addState.hidden = false;
    } catch {
      /* silencioso */
    }
  });
}

// --- Review: editar ---
const resenaPropia = document.getElementById("resenaPropia");
const editWrap = document.getElementById("editarResenaWrap");
const btnEditar = document.getElementById("btnEditarResena");
const btnCancelar = document.getElementById("btnCancelarEdicion");

btnEditar?.addEventListener("click", () => {
  resenaPropia.hidden = true;
  editWrap.classList.add("is-open");
});

btnCancelar?.addEventListener("click", () => {
  editWrap.classList.remove("is-open");
  resenaPropia.hidden = false;
});

// --- Review: eliminar (modal) ---
const modal = document.getElementById("modalEliminar");
const btnEliminar = document.querySelector(".btn-eliminar-resenia");
const modalCancelar = document.getElementById("modalCancelar");
const modalConfirmar = document.getElementById("modalConfirmar");
const deleteForm = document.querySelector(".resena-delete-form");

btnEliminar?.addEventListener("click", () => {
  modal.classList.add("is-open");
});

modalCancelar?.addEventListener("click", () => {
  modal.classList.remove("is-open");
});

modalConfirmar?.addEventListener("click", () => {
  deleteForm?.submit();
});

// --- Lists ---
const listsSection = document.getElementById("listsSection");

if (listsSection) {
  const actions = new ListActions(csrfToken);
  const titleId = parseInt(listsSection.dataset.titleId, 10);

  const openBtn = document.getElementById("listsAddBtn");
  const dialog = document.getElementById("listsDialog");
  const closeBtn = document.getElementById("lists-dialog-close");
  const cancelBtn = document.getElementById("lists-dialog-cancel");
  const checklist = document.getElementById("lists-checklist");

  // Fetch available lists and render checkboxes
  async function loadChecklist() {
    checklist.innerHTML = '<p class="list-dialog__loading">Cargando listas…</p>';

    try {
      const res = await fetch(
        `/my-lists/available?title_id=${titleId}`,
        { headers: { "X-CSRF-Token": csrfToken } },
      );
      const data = await res.json();

      if (!data.success || data.lists.length === 0) {
        checklist.innerHTML =
          '<p class="list-dialog__loading">No tenés listas. Creá una desde Mis Listas.</p>';
        return;
      }

      checklist.innerHTML = "";
      for (const list of data.lists) {
        const label = document.createElement("label");
        label.className = "list-dialog__checklist-item";

        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.checked = list.is_in_list;
        checkbox.dataset.listId = list.id;
        checkbox.dataset.listName = list.name;

        const nameSpan = document.createElement("span");
        nameSpan.className = "list-dialog__checklist-name";
        nameSpan.textContent = list.name;

        const countSpan = document.createElement("span");
        countSpan.className = "list-dialog__checklist-count";
        countSpan.textContent = `${list.item_count} películas`;

        label.append(checkbox, nameSpan, countSpan);
        checklist.append(label);
      }
    } catch {
      checklist.innerHTML =
        '<p class="list-dialog__loading">Error al cargar listas.</p>';
    }
  }

  // Toggle add/remove when checkbox changes
  async function onCheckboxChange(e) {
    const cb = e.target;
    const listId = parseInt(cb.dataset.listId, 10);
    const wasChecked = cb.dataset.prev === "true";

    try {
      if (wasChecked) {
        await actions.removeItem(listId, titleId);
      } else {
        await actions.addItem(listId, titleId);
      }
      cb.dataset.prev = String(cb.checked);
    } catch {
      cb.checked = wasChecked;
    }
  }

  openBtn?.addEventListener("click", async () => {
    dialog.showModal();
    await loadChecklist();

    // Attach event listeners to fresh checkboxes
    document
      .querySelectorAll(".list-dialog__checklist-item input[type='checkbox']")
      .forEach((cb) => {
        cb.dataset.prev = String(cb.checked);
        cb.addEventListener("change", onCheckboxChange);
      });
  });

  closeBtn?.addEventListener("click", () => dialog.close());
  cancelBtn?.addEventListener("click", () => dialog.close());
}
