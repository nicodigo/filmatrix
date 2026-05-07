<?php
$error = $error ?? '';
$campos = $campos ?? ['nombre' => '', 'email' => ''];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crear cuenta — Filmatrix</title>
  <meta name="description" content="Creá tu cuenta en Filmatrix y empezá a reseñar películas.">

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
      <h1 class="auth-card__title">Creá tu cuenta</h1>
      <p class="auth-card__subtitle">Gratis. Sin publicidad. Solo películas.</p>
    </div>

    <?php if ($error): ?>
      <div class="auth-alert" role="alert">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form class="auth-form" method="POST" action="/register" novalidate>

      <div class="auth-field">
        <label class="auth-label" for="nombre">Nombre</label>
        <input class="auth-input" type="text" id="nombre" name="nombre"
               value="<?= htmlspecialchars($campos['nombre']) ?>"
               placeholder="Tu nombre" required>
      </div>

      <div class="auth-field">
        <label class="auth-label" for="email">Email</label>
        <input class="auth-input" type="email" id="email" name="email"
               value="<?= htmlspecialchars($campos['email']) ?>"
               placeholder="tu@email.com" required>
      </div>

      <div class="auth-field">
        <label class="auth-label" for="password">Contraseña</label>
        <input class="auth-input" type="password" id="password" name="password"
               placeholder="Mínimo 8 caracteres" required>
      </div>

      <div class="auth-field">
        <label class="auth-label" for="confirm">Repetir contraseña</label>
        <input class="auth-input" type="password" id="confirm" name="confirm"
               placeholder="••••••••" required>
      </div>

      <button type="submit" class="auth-submit">Crear cuenta</button>
    </form>

    <p class="auth-switch">
      ¿Ya tenés cuenta?
      <a class="auth-link" href="/login">Iniciá sesión</a>
    </p>

  </div>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

</body>
</html>
