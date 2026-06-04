import { NavMenu } from "./modules/NavMenu.js";
import { SearchToggle } from "./modules/SearchToggle.js";
import { Toast } from "./modules/Toast.js";

const App = {
  init() {
    new NavMenu();
    new SearchToggle();
    new Toast();
  },
};

document.addEventListener('DOMContentLoaded', () => App.init());
