import { Carousel } from "../modules/Carousel.js";

document.addEventListener('DOMContentLoaded', () => {

    document
        .querySelectorAll('.upcoming-carousel')
        .forEach(wrapper => {
            new Carousel(wrapper);
        });

});