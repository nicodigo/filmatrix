import { Header } from "./modules/header.js";

const App = {
  init() {
    const header = new Header();
  },
};

document.addEventListener('DOMContentLoaded', () => App.init());
