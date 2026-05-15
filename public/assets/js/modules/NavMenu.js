export class NavMenu{
  /** @type {HTMLElement} */
  hamburger;
  /** @type {HTMLElement} */
  menu;
  constructor() {
    this.hamburger = document.querySelector(".menu-toggle");
    this.menu = document.querySelector(".nav-menu");

    if (!this.hamburger || !this.menu) return;

    this.hamburger.addEventListener("click", () => this.toggle(this.menu));

    document.addEventListener("click", (e) => {
      if (!this.hamburger.contains(e.target) && !this.menu.contains(e.target))
        this.close();
    });
  }

  toggle() {
    const isActive = this.menu.classList.toggle("is-active");
    this.hamburger.classList.toggle("is-active");
    this.hamburger.setAttribute("aria-expanded", String(isActive));
  }

  close() {
    this.menu.classList.remove("is-active");
    this.hamburger.classList.remove("is-active");
    this.hamburger.setAttribute("aria-expanded", "false");
  }
}
