<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
 
  <title>Detalle — Filmatrix</title>
  <meta name="description" content="Información, reparto y reseñas de la película en Filmatrix.">
 
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
 
  <link rel="stylesheet" href="/assets/css/base.css">
  <link rel="stylesheet" href="/assets/css/header.css">
  <link rel="stylesheet" href="/assets/css/movie-card.css">
  <link rel="stylesheet" href="/assets/css/footer.css">
  <link rel="stylesheet" href="/assets/css/detalle_pelicula.css">
</head>
 
<?php require __DIR__ . '/../partials/header.php'; ?>
 
<main class="detalle-main">
 
  <?php
    // Data provided by DetalleController
  ?>
 
  <!-- ══════════════════════════════════
       PORTADA + TÍTULO
  ══════════════════════════════════════ -->
  <section class="detalle-hero">
    <div class="detalle-hero__poster-wrap">
      <img
        class="detalle-hero__poster"
        src="<?= htmlspecialchars($title['poster_url'] ?? '/assets/img/hero-bg.webp') ?>"
        alt="Portada de <?= htmlspecialchars($title['title']) ?>"
      >
    </div>
    <div class="detalle-hero__info">
      <h1 class="detalle-titulo"><?= htmlspecialchars($title['title']) ?></h1>
      <div class="detalle-meta">
        <span class="detalle-meta__item"><?= htmlspecialchars($title['release_year'] ?? 'S/D') ?></span>
        <span class="detalle-meta__sep">·</span>
        <span class="detalle-meta__item"><?= htmlspecialchars($duracion ?? 'S/D') ?></span>
        <span class="detalle-meta__sep">·</span>
        <span class="detalle-meta__item"><?= htmlspecialchars($generoLabel ?: 'S/D') ?></span>
      </div>
    </div>
  </section>
 
  <!-- ══════════════════════════════════
       SINOPSIS / VALORACIÓN / GÉNERO
  ══════════════════════════════════════ -->
  <section class="detalle-info-section">
 
    <div class="detalle-info-grid">
 
      <div class="detalle-sinopsis">
        <h2 class="detalle-section-label">Sinopsis</h2>
        <p class="detalle-sinopsis__texto"><?= htmlspecialchars($title['synopsis'] ?? 'Sin sinopsis disponible.') ?></p>
      </div>
 
      <div class="detalle-datos">
 
        <div class="detalle-dato">
          <span class="detalle-dato__label">Valoración</span>
          <div class="detalle-dato__rating">
            <svg class="detalle-dato__star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
            <span class="detalle-dato__val"><?= htmlspecialchars($title['avg_score'] ?? 'S/P') ?></span>
            <span class="detalle-dato__max">/ 5</span>
          </div>
        </div>
 
        <div class="detalle-dato">
          <span class="detalle-dato__label">Género</span>
          <span class="detalle-dato__badge"><?= htmlspecialchars($generoLabel ?: 'S/D') ?></span>
        </div>
 
        <div class="detalle-dato">
          <span class="detalle-dato__label">Año</span>
          <span class="detalle-dato__val"><?= htmlspecialchars($title['release_year'] ?? 'S/D') ?></span>
        </div>
 
        <div class="detalle-dato">
          <span class="detalle-dato__label">Duración</span>
          <span class="detalle-dato__val"><?= htmlspecialchars($duracion ?? 'S/D') ?></span>
        </div>
 
      </div>
 
    </div>
  </section>
 
  <!-- ══════════════════════════════════
       REPARTO
  ══════════════════════════════════════ -->
  <section class="detalle-reparto-section">
    <h2 class="detalle-section-label">Reparto</h2>
    <div class="reparto-grid">
      <?php if (empty($cast)): ?>
        <p class="detalle-empty">Sin información de reparto.</p>
      <?php else: ?>
        <?php foreach ($cast as $actor): ?>
          <div class="reparto-card">
            <div class="reparto-card__foto-wrap">
              <img
                class="reparto-card__foto"
                src="<?= htmlspecialchars($actor['profile_url'] ?? '/assets/img/hero-bg.webp') ?>"
                alt="Foto de <?= htmlspecialchars($actor['name']) ?>"
              >
            </div>
            <span class="reparto-card__nombre"><?= htmlspecialchars($actor['name']) ?></span>
            <span class="reparto-card__rol"><?= htmlspecialchars($actor['character_name'] ?? $actor['role']) ?></span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
 
  <!-- ══════════════════════════════════
       BOTÓN CREAR RESEÑA
  ══════════════════════════════════════ -->
  <section class="detalle-nueva-resenia-section">
    <input type="checkbox" id="resenaToggle" class="resena-toggle-input">
 
    <label for="resenaToggle" class="btn-crear-resenia">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="2"
           stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M12 20h9"/>
        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
      </svg>
      Escribir reseña
    </label>
 
    <!-- Formulario CSS-only, visible cuando el checkbox está marcado -->
    <div class="resena-form-wrap">
      <form class="resena-form" action="/resenas/crear" method="POST">
        <input type="hidden" name="pelicula_id" value="<?= $title['id'] ?>">
 
        <div class="resena-form__stars" role="group" aria-label="Puntuación">
          <?php for ($i = 5; $i >= 1; $i--): ?>
            <input type="radio" id="star<?= $i ?>" name="estrellas" value="<?= $i ?>" class="star-input">
            <label for="star<?= $i ?>" class="star-label" aria-label="<?= $i ?> estrellas">★</label>
          <?php endfor; ?>
        </div>
 
        <textarea
          class="resena-form__textarea"
          name="texto"
          placeholder="Contá qué te pareció la película…"
          rows="5"
          maxlength="1000"
          required
        ></textarea>
 
        <div class="resena-form__actions">
          <label for="resenaToggle" class="btn-cancelar">Cancelar</label>
          <button type="submit" class="btn-publicar">Publicar reseña</button>
        </div>
      </form>
    </div>
  </section>
 
  <!-- ══════════════════════════════════
       RESEÑAS + TAL VEZ TE INTERESE
  ══════════════════════════════════════ -->
  <section class="detalle-bottom-section">
 
    <!-- ── Reseñas ── -->
    <div class="resenias-col">
      <h2 class="detalle-section-label">Reseñas</h2>
 
      <?php if (empty($reviews)): ?>
        <p class="detalle-empty">Todavía no hay reseñas.</p>
      <?php else: ?>
        <?php foreach ($reviews as $index => $resenia): ?>
          <article class="resenia-card">
 
            <header class="resenia-card__header">
              <img
                class="resenia-card__avatar"
                src="/assets/img/hero-bg.webp"
                alt="Avatar de <?= htmlspecialchars($resenia['username']) ?>"
              >
              <div class="resenia-card__meta">
                <span class="resenia-card__usuario"><?= htmlspecialchars($resenia['username']) ?></span>
                <div class="resenia-card__estrellas" aria-label="<?= (int) round($resenia['score']) ?> estrellas">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <span class="resenia-card__star <?= $i <= (int) round($resenia['score']) ? 'resenia-card__star--filled' : '' ?>">★</span>
                  <?php endfor; ?>
                </div>
              </div>
              <time class="resenia-card__fecha"><?= htmlspecialchars(date('d M Y', strtotime($resenia['created_at']))) ?></time>
            </header>
 
            <p class="resenia-card__texto"><?= htmlspecialchars($resenia['body'] ?? '') ?></p>
 
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
 
    <!-- ── Tal vez te interese ── -->
    <aside class="sugeridas-col">
      <h2 class="detalle-section-label">Tal vez te interese</h2>
      <div class="sugeridas-lista">
        <?php if (!empty($suggested)): ?>
          <?php foreach ($suggested as $sug): ?>
            <a href="/pelicula?tmdb_id=<?= $sug['tmdb_id'] ?>" class="sugerida-item">
              <img
                class="sugerida-item__poster"
                src="<?= htmlspecialchars($sug['poster_url'] ?? '/assets/img/hero-bg.webp') ?>"
                alt="Portada de <?= htmlspecialchars($sug['title']) ?>"
              >
              <span class="sugerida-item__titulo"><?= htmlspecialchars($sug['title']) ?></span>
            </a>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </aside>
 
  </section>
 
</main>
 
<?php require __DIR__ . '/../partials/footer.php'; ?>
