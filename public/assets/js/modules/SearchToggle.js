export class SearchToggle {
  /** @type {HTMLButtonElement} */
  toggleButton;
  /** @type {HTMLFormElement} */
  searchForm;

  constructor() {
    this.toggleButton = document.querySelector(".search-toggle");
    this.searchForm = document.querySelector(".search-form");

    if (!this.toggleButton || !this.searchForm) return;

    this.toggleButton.addEventListener("click", () => this.toggle());
    this.searchForm.addEventListener("submit", (e) => this.handleSubmit(e));
  }

  toggle() {
    const isActive = this.searchForm.classList.toggle("is-active");
    this.toggleButton.classList.toggle("is-active");
    this.toggleButton.setAttribute("aria-expanded", String(isActive));

    if (isActive) {
      this.searchForm.querySelector(".search-input")?.focus();
    }
  }

  handleSubmit(e) {
    const input = this.searchForm.querySelector(".search-input");
    if (!input?.value.trim()) {
      e.preventDefault();
      input?.focus();
    }
  }
}