<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- SEO básico -->
  <title>Filmatrix — Donde brillan tus reseñas</title>
  <meta name="description" content="Tu diario cinematográfico. Registrá, descubrí y compartí las películas que te definen.">

  <!-- Preconexión para optimizar la velocidad -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <!-- Importación de Inter (Sans-Serif) y DM Serif Display (Serif) -->
  <!-- <link href="https://googleapis.com" rel="stylesheet"> -->
   <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="/assets/css/base.css">
  <link rel="stylesheet" href="/assets/css/header.css">
  <link rel="stylesheet" href="/assets/css/home.css">
  <link rel="stylesheet" href="/assets/css/hero.css">
  <link rel="stylesheet" href="/assets/css/movie-card.css">
  <link rel="stylesheet" href="/assets/css/footer.css">
</head>

<?php require __DIR__ . '/../partials/header.php'; ?>

<main>
  <section class="hero">
    <h1>Filmatrix</h1>
    <h2>Donde brillan tus reseñas</h2>
  </section>

  <section class="popular-movies">
    <div class="movie-flex">
      <?php if (empty($popular)): ?>
        <p class="catalogo-empty">Sin títulos disponibles.</p>
      <?php else: ?>
        <?php foreach ($popular as $movie): require __DIR__ . '/includes/movie-card.php'; endforeach; ?>
      <?php endif; ?>
    </div>
  </section>

  <?php
    // Datos hardcodeados para la reseña del día
    $dailyReview = [
      'title'       => 'Dune: Part Two',
      'year'        => '2024',
      'author'      => 'María López',
      'avatar'      => '/assets/img/user_avatar.png',
      'texto_reseña'=> 'Una obra maestra visual que expande el universo de Frank Herbert con una narrativa épica y actuaciones memorables.',
      'likes'       => 128,
      'url_banner'  => '/assets/img/hero-bg.webp',
    ];
  ?>

  <section class="daily-review">
    <h2>Reseña del día</h2>
    <article>
      <a href="#">
        <figure>
          <img src="<?= htmlspecialchars($dailyReview['url_banner']) ?>" alt="<?= htmlspecialchars($dailyReview['title']) ?>">
        </figure>
      </a>
      <div class="daily-review__content">
        <h3 class="daily-review__title"><?= htmlspecialchars($dailyReview['title']) ?></h3>
        <span class="daily-review__year"><?= htmlspecialchars($dailyReview['year']) ?></span>
        <div class="daily-review__author">
          <img src="<?= htmlspecialchars($dailyReview['avatar']) ?>" alt="Avatar de <?= htmlspecialchars($dailyReview['author']) ?>">
          <span><?= htmlspecialchars($dailyReview['author']) ?></span>
        </div>
        <p class="daily-review__text"><?= htmlspecialchars($dailyReview['texto_reseña']) ?></p>
        <span class="daily-review__likes">&hearts; <?= (int)$dailyReview['likes'] ?></span>
      </div>
    </article>
  </section>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
