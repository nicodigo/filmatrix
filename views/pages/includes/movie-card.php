<article class="movie-card">
  <a href="#" class="movie-card__link">
    <img src="<?= $movie['poster'] ?>" alt="<?= $movie['title'] ?>">
    <div class="movie-card__overlay">
      <p class="movie-card__title"><?= $movie['title'] ?></p>
      <p class="movie-card__score"><?= $movie['score'] ?></p>
    </div>
  </a>
</article>
