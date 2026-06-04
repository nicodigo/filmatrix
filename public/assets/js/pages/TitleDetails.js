import { WatchlistActions } from "../modules/WatchlistActions.js";

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
