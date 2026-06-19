export class Carousel {
  constructor(wrapper) {
    if (!wrapper) {
      this.track = document.getElementById('movieCarousel');
      this.prevBtn = document.getElementById('carouselPrev');
      this.nextBtn = document.getElementById('carouselNext');
    } else {
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
    this.currentIndex = Math.min(this.currentIndex, this.maxIndex);

    const step = this.itemsPerView;
    let next = this.currentIndex + direction * step;

    // Clampear en vez de ciclar — si nos pasamos del final, quedamos en maxIndex.
    next = Math.max(0, Math.min(next, this.maxIndex));

    this.currentIndex = next;

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
    this.currentIndex = Math.min(this.currentIndex, this.maxIndex);

    // Si no hay nada que desplazar, ocultar ambas flechas.
    const hasOverflow = this.totalItems > this.itemsPerView;

    this.prevBtn.style.display = hasOverflow ? '' : 'none';
    this.nextBtn.style.display = hasOverflow ? '' : 'none';

    if (!hasOverflow) return;

    this.prevBtn.disabled = this.currentIndex <= 0;
    this.nextBtn.disabled = this.currentIndex >= this.maxIndex;
  }
}