import { NavMenu } from "./modules/NavMenu.js";
import { SearchToggle } from "./modules/SearchToggle.js";
import { CatalogFilters } from "./modules/CatalogFilters.js";
import { Toast } from "./modules/Toast.js";
import { ReviewEdit } from "./modules/ReviewEdit.js";

const App = {
  init() {
    new NavMenu();
    new SearchToggle();
    new CatalogFilters();
    new Toast();
    new ReviewEdit();
  },
};

document.addEventListener('DOMContentLoaded', () => App.init());