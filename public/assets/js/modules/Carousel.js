export class Carousel {
  constructor(wrapper) {
    //MODO HOME (compatibilidad con IDs)
    if (!wrapper) {
      this.track = document.getElementById('movieCarousel');
      this.prevBtn = document.getElementById('carouselPrev');
      this.nextBtn = document.getElementById('carouselNext');
    } 
    //MODO REUTILIZABLE
    else {
      this.track = wrapper.querySelector('.movieCarousel');
      this.prevBtn = wrapper.querySelector('.carouselPrev');
      this.nextBtn = wrapper.querySelector('.carouselNext');
    }

    if (!this.track || !this.prevBtn || !this.nextBtn) return;

    this.currentIndex = 0;

    this.prevBtn.addEventListener('click', () => this.move(-1));
    this.nextBtn.addEventListener('click', () => this.move(1));

    window.addEventListener('resize', () => this.updateButtons());

    this.updateButtons();
  }

  get totalItems() {
    return this.track.querySelectorAll('.movie-card').length;
  }

  get itemsPerView() {
    return window.innerWidth < 768 ? 2 : 4;
  }

  get maxIndex() {
    return Math.max(0, this.totalItems - this.itemsPerView);
  }

  move(direction) {
    const step = this.itemsPerView;
    const next = this.currentIndex + direction * step;

    if (next > this.maxIndex) {
      this.currentIndex = 0;
    } else if (next < 0) {
      this.currentIndex = this.maxIndex;
    } else {
      this.currentIndex = next;
    }

    const card = this.track.querySelector('.movie-card');
    if (!card) return;

    const cardWidth = card.offsetWidth;
    const gap = 8;

    const offset = this.currentIndex * (cardWidth + gap);

    this.track.scrollTo({
      left: offset,
      behavior: 'smooth'
    });

    this.updateButtons();
  }

  updateButtons() {
    this.prevBtn.disabled = false;
    this.nextBtn.disabled = false;
  }
}