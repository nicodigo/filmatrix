export class Toast {
    constructor() {
      this.toast = document.getElementById('toast');
      if (!this.toast) return;
  
      // Mostrar con animación
      requestAnimationFrame(() => {
        this.toast.classList.add('toast--visible');
      });
  
      // Auto-ocultar después de 4s
      setTimeout(() => this.dismiss(), 4000);
  
      this.toast.addEventListener('click', () => this.dismiss());
    }
  
    dismiss() {
      this.toast.classList.remove('toast--visible');
      this.toast.classList.add('toast--hiding');
      this.toast.addEventListener('transitionend', () => this.toast.remove(), { once: true });
    }
  }