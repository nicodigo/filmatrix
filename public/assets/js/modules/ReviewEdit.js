export class ReviewEdit {
    constructor() {
      this.btnEditar     = document.getElementById('btnEditarResena');
      this.btnCancelar   = document.getElementById('btnCancelarEdicion');
      this.resenaPropia  = document.getElementById('resenaPropia');
      this.editWrap      = document.getElementById('editarResenaWrap');
      this.btnEliminar   = document.querySelector('.btn-eliminar-resenia');
      this.modal         = document.getElementById('modalEliminar');
      this.modalCancelar = document.getElementById('modalCancelar');
      this.modalConfirmar = document.getElementById('modalConfirmar');
      this.deleteForm    = document.querySelector('.resena-delete-form');
  
      if (!this.btnEditar || !this.editWrap || !this.resenaPropia) return;
  
      // Editar
      this.btnEditar.addEventListener('click', () => {
        this.resenaPropia.style.display = 'none';
        this.editWrap.classList.add('is-open');
      });
  
      this.btnCancelar?.addEventListener('click', () => {
        this.editWrap.classList.remove('is-open');
        this.resenaPropia.style.display = 'flex';
      });
  
      // Eliminar — abrir modal
      this.btnEliminar?.addEventListener('click', (e) => {
        e.preventDefault();
        this.modal.classList.add('is-open');
      });
  
      // Modal — cancelar
      this.modalCancelar?.addEventListener('click', () => {
        this.modal.classList.remove('is-open');
      });
  
      // Modal — confirmar
      this.modalConfirmar?.addEventListener('click', () => {
        this.deleteForm?.submit();
      });
  
      // Cerrar con click fuera
      this.modal?.addEventListener('click', (e) => {
        if (e.target === this.modal) {
          this.modal.classList.remove('is-open');
        }
      });
    }
  }