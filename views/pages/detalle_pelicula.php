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
    // Placeholder estático — reemplazar con datos reales desde DB/API
    $pelicula = [
      'titulo'    => 'Nombre de la Película',
      'poster'    => '/assets/img/hero-bg.webp',
      'sinopsis'  => 'Una historia apasionante que sigue a un protagonista en su travesía por un mundo desconocido, enfrentando sus miedos más profundos y descubriendo verdades ocultas sobre su pasado. Un viaje visual y emocional que no dejará indiferente a nadie.',
      'valoracion'=> '4.7',
      'genero'    => 'Ciencia Ficción',
      'anio'      => '2024',
      'duracion'  => '2h 18m',
    ];
 
    $reparto = [
      ['nombre' => 'Actor Uno',    'rol' => 'Protagonista',    'foto' => '/assets/img/hero-bg.webp'],
      ['nombre' => 'Actriz Dos',   'rol' => 'Antagonista',     'foto' => '/assets/img/hero-bg.webp'],
      ['nombre' => 'Actor Tres',   'rol' => 'Secundario',      'foto' => '/assets/img/hero-bg.webp'],
      ['nombre' => 'Actriz Cuatro','rol' => 'Apoyo cómico',    'foto' => '/assets/img/hero-bg.webp'],
      ['nombre' => 'Actor Cinco',  'rol' => 'Villano',         'foto' => '/assets/img/hero-bg.webp'],
      ['nombre' => 'Actriz Seis',  'rol' => 'Mentora',         'foto' => '/assets/img/hero-bg.webp'],
    ];
 
    $resenias = [
      [
        'usuario'   => 'María G.',
        'foto'      => '/assets/img/hero-bg.webp',
        'estrellas' => 5,
        'fecha'     => '12 abr 2025',
        'texto'     => 'Una obra maestra del cine contemporáneo. La fotografía es sublime y las actuaciones están a la altura de cualquier premiación internacional. Totalmente recomendada.',
        'likes'     => 48,
      ],
      [
        'usuario'   => 'Carlos R.',
        'foto'      => '/assets/img/hero-bg.webp',
        'estrellas' => 4,
        'fecha'     => '3 mar 2025',
        'texto'     => 'Muy buena película, aunque el tercer acto me pareció algo apresurado. De todas formas, la experiencia general es increíble y la banda sonora te acompaña días después.',
        'likes'     => 21,
      ],
      [
        'usuario'   => 'Lucía M.',
        'foto'      => '/assets/img/hero-bg.webp',
        'estrellas' => 5,
        'fecha'     => '18 feb 2025',
        'texto'     => 'La vi dos veces en el cine. Hay capas y capas de simbolismo que uno descubre en cada visionado. Sin dudas una de las mejores del año.',
        'likes'     => 63,
      ],
    ];
 
    $sugeridas = [
      ['titulo' => 'Título sugerido A', 'poster' => '/assets/img/hero-bg.webp'],
      ['titulo' => 'Título sugerido B', 'poster' => '/assets/img/hero-bg.webp'],
      ['titulo' => 'Título sugerido C', 'poster' => '/assets/img/hero-bg.webp'],
      ['titulo' => 'Título sugerido D', 'poster' => '/assets/img/hero-bg.webp'],
    ];
  ?>
 
  <!-- ══════════════════════════════════
       PORTADA + TÍTULO
  ══════════════════════════════════════ -->
  <section class="detalle-hero">
    <div class="detalle-hero__poster-wrap">
      <img
        class="detalle-hero__poster"
        src="<?= htmlspecialchars($pelicula['poster']) ?>"
        alt="Portada de <?= htmlspecialchars($pelicula['titulo']) ?>"
      >
    </div>
    <div class="detalle-hero__info">
      <h1 class="detalle-titulo"><?= htmlspecialchars($pelicula['titulo']) ?></h1>
      <div class="detalle-meta">
        <span class="detalle-meta__item"><?= htmlspecialchars($pelicula['anio']) ?></span>
        <span class="detalle-meta__sep">·</span>
        <span class="detalle-meta__item"><?= htmlspecialchars($pelicula['duracion']) ?></span>
        <span class="detalle-meta__sep">·</span>
        <span class="detalle-meta__item"><?= htmlspecialchars($pelicula['genero']) ?></span>
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
        <p class="detalle-sinopsis__texto"><?= htmlspecialchars($pelicula['sinopsis']) ?></p>
      </div>
 
      <div class="detalle-datos">
 
        <div class="detalle-dato">
          <span class="detalle-dato__label">Valoración</span>
          <div class="detalle-dato__rating">
            <svg class="detalle-dato__star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
            <span class="detalle-dato__val"><?= htmlspecialchars($pelicula['valoracion']) ?></span>
            <span class="detalle-dato__max">/ 5</span>
          </div>
        </div>
 
        <div class="detalle-dato">
          <span class="detalle-dato__label">Género</span>
          <span class="detalle-dato__badge"><?= htmlspecialchars($pelicula['genero']) ?></span>
        </div>
 
        <div class="detalle-dato">
          <span class="detalle-dato__label">Año</span>
          <span class="detalle-dato__val"><?= htmlspecialchars($pelicula['anio']) ?></span>
        </div>
 
        <div class="detalle-dato">
          <span class="detalle-dato__label">Duración</span>
          <span class="detalle-dato__val"><?= htmlspecialchars($pelicula['duracion']) ?></span>
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
      <?php foreach ($reparto as $actor): ?>
        <div class="reparto-card">
          <div class="reparto-card__foto-wrap">
            <img
              class="reparto-card__foto"
              src="<?= htmlspecialchars($actor['foto']) ?>"
              alt="Foto de <?= htmlspecialchars($actor['nombre']) ?>"
            >
          </div>
          <span class="reparto-card__nombre"><?= htmlspecialchars($actor['nombre']) ?></span>
          <span class="reparto-card__rol"><?= htmlspecialchars($actor['rol']) ?></span>
        </div>
      <?php endforeach; ?>
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
        <input type="hidden" name="pelicula_id" value="1"><!-- reemplazar con ID real -->
 
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
 
      <?php foreach ($resenias as $index => $resenia): ?>
        <article class="resenia-card">
 
          <header class="resenia-card__header">
            <img
              class="resenia-card__avatar"
              src="<?= htmlspecialchars($resenia['foto']) ?>"
              alt="Avatar de <?= htmlspecialchars($resenia['usuario']) ?>"
            >
            <div class="resenia-card__meta">
              <span class="resenia-card__usuario"><?= htmlspecialchars($resenia['usuario']) ?></span>
              <div class="resenia-card__estrellas" aria-label="<?= $resenia['estrellas'] ?> estrellas">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <span class="resenia-card__star <?= $i <= $resenia['estrellas'] ? 'resenia-card__star--filled' : '' ?>">★</span>
                <?php endfor; ?>
              </div>
            </div>
            <time class="resenia-card__fecha"><?= htmlspecialchars($resenia['fecha']) ?></time>
          </header>
 
          <p class="resenia-card__texto"><?= htmlspecialchars($resenia['texto']) ?></p>
 
          <footer class="resenia-card__footer">
            <!--
              Toggle like: checkbox + label (sin JS).
              Para persistencia real se necesita AJAX/form POST.
            -->
            <input type="checkbox" id="like<?= $index ?>" class="like-input">
            <label for="like<?= $index ?>" class="like-btn" aria-label="Me gusta">
              <svg class="like-btn__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                   fill="none" stroke="currentColor" stroke-width="2"
                   stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
              </svg>
              <span class="like-btn__count"><?= (int) $resenia['likes'] ?></span>
            </label>
          </footer>
 
        </article>
      <?php endforeach; ?>
    </div>
 
    <!-- ── Tal vez te interese ── -->
    <aside class="sugeridas-col">
      <h2 class="detalle-section-label">Tal vez te interese</h2>
      <div class="sugeridas-lista">
        <?php foreach ($sugeridas as $sug): ?>
          <a href="/detalle_pelicula" class="sugerida-item">
            <img
              class="sugerida-item__poster"
              src="<?= htmlspecialchars($sug['poster']) ?>"
              alt="Portada de <?= htmlspecialchars($sug['titulo']) ?>"
            >
            <span class="sugerida-item__titulo"><?= htmlspecialchars($sug['titulo']) ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </aside>
 
  </section>
 
</main>
 
<?php require __DIR__ . '/../partials/footer.php'; ?>