export class ReviewEdit {
  constructor() {
    this.initSingleReview();
    this.initMultipleReviews();
  }

  // Para title-detail (una sola reseña con IDs fijos)
  initSingleReview() {
    const btnEditar    = document.getElementById('btnEditarResena');
    const btnCancelar  = document.getElementById('btnCancelarEdicion');
    const resenaPropia = document.getElementById('resenaPropia');
    const editWrap     = document.getElementById('editarResenaWrap');
    const btnEliminar  = document.querySelector('.btn-eliminar-resenia[data-id]') === null
      ? document.querySelector('#resenaPropia .btn-eliminar-resenia')
      : null;
    const modal        = document.getElementById('modalEliminar');
    const modalCancelar  = document.getElementById('modalCancelar');
    const modalConfirmar = document.getElementById('modalConfirmar');
    const deleteForm   = document.querySelector('.resena-delete-form');

    if (!btnEditar || !editWrap || !resenaPropia) return;

    btnEditar.addEventListener('click', () => {
      resenaPropia.style.display = 'none';
      editWrap.classList.add('is-open');
    });

    btnCancelar?.addEventListener('click', () => {
      editWrap.classList.remove('is-open');
      resenaPropia.style.display = 'flex';
    });

    btnEliminar?.addEventListener('click', () => {
      modal?.classList.add('is-open');
    });

    modalCancelar?.addEventListener('click', () => {
      modal?.classList.remove('is-open');
    });

    modalConfirmar?.addEventListener('click', () => {
      deleteForm?.submit();
    });

    modal?.addEventListener('click', (e) => {
      if (e.target === modal) modal.classList.remove('is-open');
    });
  }

  // Para profile (múltiples reseñas con data-id)
  initMultipleReviews() {
    // Editar
    document.querySelectorAll('.btn-editar-resenia[data-id]').forEach(btn => {
      const id = btn.dataset.id;
      btn.addEventListener('click', () => {
        document.getElementById(`resenaPropia${id}`).style.display = 'none';
        document.getElementById(`editarResenaWrap${id}`)?.classList.add('is-open');
      });
    });

    // Cancelar edición
    document.querySelectorAll('.btn-cancelar[data-id]').forEach(btn => {
      const id = btn.dataset.id;
      btn.addEventListener('click', () => {
        document.getElementById(`editarResenaWrap${id}`)?.classList.remove('is-open');
        document.getElementById(`resenaPropia${id}`).style.display = 'flex';
      });
    });

    // Abrir modal eliminar
    document.querySelectorAll('.btn-eliminar-resenia[data-id]').forEach(btn => {
      const id = btn.dataset.id;
      btn.addEventListener('click', () => {
        document.getElementById(`modalEliminar${id}`)?.classList.add('is-open');
      });
    });

    // Cancelar modal
    document.querySelectorAll('[data-modal-id]').forEach(btn => {
      const id = btn.dataset.modalId;
      btn.addEventListener('click', () => {
        document.getElementById(`modalEliminar${id}`)?.classList.remove('is-open');
      });
    });

    // Confirmar eliminar
    document.querySelectorAll('[data-confirm-id]').forEach(btn => {
      const id = btn.dataset.confirmId;
      btn.addEventListener('click', () => {
        const card = document.getElementById(`resenaCard${id}`);
        card?.querySelector('.resena-delete-form')?.submit();
      });
    });

    // Cerrar modal con click fuera
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) overlay.classList.remove('is-open');
      });
    });
  }
}