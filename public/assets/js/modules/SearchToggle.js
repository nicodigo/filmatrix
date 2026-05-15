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
  }

  toggle() {
    const isActive = this.searchForm.classList.toggle("is-active");
    this.toggleButton.classList.toggle("is-active");
    this.toggleButton.setAttribute("aria-expanded", String(isActive));
  }
}
