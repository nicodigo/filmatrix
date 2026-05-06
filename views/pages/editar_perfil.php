<?php
$error   = $error ?? '';
$success = $success ?? '';

$usuario = $usuario ?? [
  'nombre' => $_SESSION['user_nombre'] ?? 'Usuario',
  'email'  => 'usuario@filmatrix.com',
  'avatar' => '/assets/img/user_avatar.png',
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar perfil — Filmatrix</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="/assets/css/base.css">
  <link rel="stylesheet" href="/assets/css/header.css">
  <link rel="stylesheet" href="/assets/css/footer.css">
  <link rel="stylesheet" href="/assets/css/editar_perfil.css">
</head>

<body>

<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="ep-main">

  <div class="ep-header">
    <a href="/perfil" class="ep-back">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="2"
           stroke-linecap="round" stroke-linejoin="round">
        <line x1="19" y1="12" x2="5" y2="12"/>
        <polyline points="12 19 5 12 12 5"/>
      </svg>
      Volver al perfil
    </a>
    <h1 class="ep-title">Editar perfil</h1>
  </div>

  <?php if ($error): ?>
    <div class="auth-alert ep-alert" role="alert">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="ep-success" role="status">
      <?= htmlspecialchars($success) ?>
    </div>
  <?php endif; ?>

  <form class="ep-form" method="POST" action="/perfil/editar" enctype="multipart/form-data">

    <!-- Avatar -->
    <section class="ep-section">
      <h2 class="ep-section__title">Foto de perfil</h2>

      <div class="ep-avatar-row">
        <div class="ep-avatar-wrap">
          <img class="ep-avatar"
               src="<?= htmlspecialchars($usuario['avatar']) ?>"
               id="avatarPreview"
               alt="Avatar">
        </div>

        <div class="ep-avatar-actions">
          <label for="avatar" class="ep-btn ep-btn--secondary">
            Subir imagen
          </label>

          <input type="file"
                 id="avatar"
                 name="avatar"
                 accept="image/*"
                 class="ep-file-input"
                 onchange="previewAvatar(this)">

          <p class="ep-avatar-hint">JPG, PNG o WebP</p>
        </div>
      </div>
    </section>

    <div class="ep-divider"></div>

    <!-- Datos -->
    <section class="ep-section">
      <h2 class="ep-section__title">Datos personales</h2>

      <div class="ep-fields">

        <div class="auth-field">
          <label class="auth-label" for="nombre">Nombre</label>
          <input class="auth-input"
                 type="text"
                 id="nombre"
                 name="nombre"
                 value="<?= htmlspecialchars($usuario['nombre']) ?>">
        </div>

        <div class="auth-field">
          <label class="auth-label" for="email">Email</label>
          <input class="auth-input"
                 type="email"
                 id="email"
                 name="email"
                 value="<?= htmlspecialchars($usuario['email']) ?>">
        </div>

      </div>
    </section>

    <div class="ep-divider"></div>

    <!-- Password -->
    <section class="ep-section">
      <h2 class="ep-section__title">Cambiar contraseña</h2>

      <div class="ep-fields">

        <div class="auth-field">
          <label class="auth-label">Contraseña actual</label>
          <input class="auth-input"
                 type="password"
                 name="password_actual">
        </div>

        <div class="auth-field">
          <label class="auth-label">Nueva contraseña</label>
          <input class="auth-input"
                 type="password"
                 name="password_nueva">
        </div>

        <div class="auth-field">
          <label class="auth-label">Repetir nueva contraseña</label>
          <input class="auth-input"
                 type="password"
                 name="password_confirm">
        </div>

      </div>
    </section>

    <div class="ep-divider"></div>

    <!-- Actions -->
    <div class="ep-actions">
      <a href="/perfil" class="ep-btn ep-btn--ghost">Cancelar</a>
      <button type="submit" class="ep-btn ep-btn--primary">Guardar cambios</button>
    </div>

  </form>

</main>

<script>
function previewAvatar(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('avatarPreview').src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>

</body>
</html>