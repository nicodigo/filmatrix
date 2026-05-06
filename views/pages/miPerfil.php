<?php
$usuario = [
    'nombre'        => $_SESSION['user_nombre'] ?? 'Usuario',
    'email'         => 'usuario@filmatrix.com',
    'avatar'        => '/assets/img/user_avatar.png',
    'miembro_desde' => 'Mayo 2025',
    'resenias'      => 12,
    'favoritas'     => 34,
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi perfil — Filmatrix</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="/assets/css/base.css">
  <link rel="stylesheet" href="/assets/css/header.css">
  <link rel="stylesheet" href="/assets/css/footer.css">
  <link rel="stylesheet" href="/assets/css/miPerfil.css">
</head>

<body>

<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="perfil-main">

  <section class="perfil-hero">
    <div class="perfil-avatar-wrap">
      <img class="perfil-avatar" src="<?= htmlspecialchars($usuario['avatar']) ?>" alt="Avatar de <?= htmlspecialchars($usuario['nombre']) ?>">
    </div>

    <div class="perfil-hero__info">
      <h1 class="perfil-nombre"><?= htmlspecialchars($usuario['nombre']) ?></h1>
      <p class="perfil-email"><?= htmlspecialchars($usuario['email']) ?></p>
      <p class="perfil-miembro">Miembro desde <?= htmlspecialchars($usuario['miembro_desde']) ?></p>
    </div>
  </section>

  <section class="perfil-stats">
    <div class="perfil-stat">
      <span class="perfil-stat__num"><?= $usuario['resenias'] ?></span>
      <span class="perfil-stat__label">Reseñas</span>
    </div>

    <div class="perfil-stat">
      <span class="perfil-stat__num"><?= $usuario['favoritas'] ?></span>
      <span class="perfil-stat__label">Favoritas</span>
    </div>
  </section>

  <section class="perfil-acciones">
    <a href="/perfil/editar" class="perfil-btn perfil-btn--primary">Editar perfil</a>

    <form action="/logout" method="POST">
      <button type="submit" class="perfil-btn perfil-btn--danger">Cerrar sesión</button>
    </form>
  </section>

</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

</body>
</html>