export class CatalogFilters {
  constructor() {
    this.selects = document.querySelectorAll(
      "#filterGenre, #filterYear, #filterLanguage, #filterScore",
    );

    if (!this.selects.length) return;

    this.selects.forEach((select) => {
      select.addEventListener("change", () => this.apply());
    });
  }

  apply() {
    const params = new URLSearchParams(window.location.search);

    this.selects.forEach((select) => {
      if (select.value) {
        params.set(select.name, select.value);
      } else {
        params.delete(select.name);
      }
    });

    // Mantener búsqueda activa si existe
    const q = params.get("q");
    if (!q) params.delete("q");

    window.location.href = "/titles?" + params.toString();
  }
}

