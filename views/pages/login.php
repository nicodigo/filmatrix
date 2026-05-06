<?php $error = $error ?? ''; ?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar sesión — Filmatrix</title>
  <meta name="description" content="Iniciá sesión en Filmatrix para acceder a tu perfil y reseñas.">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="/assets/css/base.css">
  <link rel="stylesheet" href="/assets/css/header.css">
  <link rel="stylesheet" href="/assets/css/footer.css">
  <link rel="stylesheet" href="/assets/css/auth.css">
</head>

<body>

<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="auth-main">
  <div class="auth-card">

    <div class="auth-card__header">
      <h1 class="auth-card__title">Bienvenido de nuevo</h1>
      <p class="auth-card__subtitle">Iniciá sesión para continuar en Filmatrix</p>
    </div>

    <?php if ($error): ?>
      <div class="auth-alert" role="alert">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="8" x2="12" y2="12"/>
          <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form class="auth-form" method="POST" action="/login" novalidate>

      <div class="auth-field">
        <label class="auth-label" for="email">Email</label>
        <input
          class="auth-input <?= $error ? 'auth-input--error' : '' ?>"
          type="email"
          id="email"
          name="email"
          value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
          placeholder="tu@email.com"
          autocomplete="email"
          required
        >
      </div>

      <div class="auth-field">
        <div class="auth-label-row">
          <label class="auth-label" for="password">Contraseña</label>
          <a class="auth-link auth-link--sm" href="/recuperar-password">¿Olvidaste tu contraseña?</a>
        </div>

        <div class="auth-input-wrap">
          <input
            class="auth-input <?= $error ? 'auth-input--error' : '' ?>"
            type="password"
            id="password"
            name="password"
            placeholder="••••••••"
            autocomplete="current-password"
            required
          >

          <input type="checkbox" id="showPass" class="show-pass-input">

          <label for="showPass" class="show-pass-btn" aria-label="Mostrar contraseña">
            <svg class="show-pass-btn__icon show-pass-btn__icon--show"
                 xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>

            <svg class="show-pass-btn__icon show-pass-btn__icon--hide"
                 xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
              <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
              <line x1="1" y1="1" x2="23" y2="23"/>
            </svg>
          </label>
        </div>
      </div>

      <button type="submit" class="auth-submit">Iniciar sesión</button>
    </form>

    <p class="auth-switch">
      ¿No tenés cuenta?
      <a class="auth-link" href="/registro">Registrate gratis</a>
    </p>

  </div>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

</body>
</html>