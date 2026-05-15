import { NavMenu } from "./modules/NavMenu.js";
import { SearchToggle } from "./modules/SearchToggle.js"

const App = {
  init() {
    const navMenu = new NavMenu();
    const searchToggle = new SearchToggle();
  },
};

document.addEventListener('DOMContentLoaded', () => App.init());
