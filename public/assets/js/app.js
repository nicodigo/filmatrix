import { NavMenu } from "./modules/NavMenu.js";
import { SearchToggle } from "./modules/SearchToggle.js";
import { CatalogFilters } from "./modules/CatalogFilters.js";

const App = {
  init() {
    new NavMenu();
    new SearchToggle();
    new CatalogFilters();
  },
};

document.addEventListener('DOMContentLoaded', () => App.init());