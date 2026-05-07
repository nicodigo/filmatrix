<article class="movie-card">
  <a href="/pelicula?tmdb_id=<?= $movie['tmdb_id'] ?>" class="movie-card__link">
    <img src="<?= $movie['poster_url'] ?>" alt="<?= $movie['title'] ?>">
    <div class="movie-card__overlay">
      <p class="movie-card__title"><?= $movie['title'] ?></p>
      <p class="movie-card__score"><?= $movie['avg_score'] ?? 'S/P' ?></p>
    </div>
  </a>
</article>
