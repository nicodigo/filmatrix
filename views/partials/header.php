<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Filmatrix — Header</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="/assets/css/base.css" rel="stylesheet">
  <link href="/assets/css/header.css" rel="stylesheet">
</head>
<body>
  <!-- Skip to main content: accesibilidad para usuarios de teclado -->
  <a class="skip-link" href="#main-content">Saltar al contenido</a>

  <!-- ── Overlay del drawer ─────────────────────────────────── -->
  <div
    class="drawer-overlay"
    id="drawer-overlay"
    aria-hidden="true"
    role="presentation"
  ></div>

  <!-- ── Drawer de navegación ───────────────────────────────── -->
  <nav
    class="drawer"
    id="main-drawer"
    role="dialog"
    aria-modal="true"
    aria-label="Menú de navegación"
    hidden
  >
    <div class="drawer-header">
      <span class="drawer-title">Filmatrix</span>
      <button
        class="icon-btn"
        id="drawer-close"
        aria-label="Cerrar menú"
      >
        <!-- Icon: X -->
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>

    <div class="drawer-nav">

      <!-- SECCIÓN: Descubrir — placeholder, reemplazar con links reales -->
      <span class="drawer-section-label">Descubrir</span>

      <a href="#" class="drawer-link active" aria-current="page">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
          <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
        Inicio
      </a>

      <a href="#" class="drawer-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
          <polygon points="23 7 16 12 23 17 23 7"/>
          <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
        </svg>
        Películas
      </a>

      <a href="#" class="drawer-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        Actividad
      </a>

      <a href="#" class="drawer-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
          <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
        </svg>
        Mejor valoradas
      </a>

      <!-- SECCIÓN: Tu cuenta — placeholder -->
      <span class="drawer-section-label">Tu cuenta</span>

      <a href="#" class="drawer-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
        Perfil
      </a>

      <a href="#" class="drawer-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
          <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
          <polyline points="17 21 17 13 7 13 7 21"/>
          <polyline points="7 3 7 8 15 8"/>
        </svg>
        Mi lista
      </a>

      <a href="#" class="drawer-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
          <circle cx="12" cy="12" r="3"/>
          <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/>
        </svg>
        Configuración
      </a>

    </div>

    <div class="drawer-footer">
      <p>Filmatrix &copy; 2026</p>
    </div>
  </nav>

  <!-- ── Header principal ───────────────────────────────────── -->
  <header class="site-header" role="banner">
    <div class="header-inner">

      <!-- Logo -->
      <a href="/" class="site-logo" aria-label="Filmatrix — ir al inicio">
        <!--
          Reemplazar el <span> por <img> cuando el asset esté disponible:
          <img src="/assets/filmatrix-logo.webp" alt="Filmatrix" height="32">
        -->
        <span class="logo-text" aria-hidden="true">Filmatrix</span>
      </a>

      <!-- Acciones del header -->
      <div class="header-actions" role="group" aria-label="Acciones principales">

        <!-- Búsqueda expandible -->
        <div class="search-wrapper" id="search-wrapper">
          <!-- El formulario se revela al activar el botón de búsqueda -->
          <form
            class="search-form"
            id="search-form"
            role="search"
            aria-label="Buscar películas"
            action="/buscar"
            method="GET"
          >
            <label for="search-input" class="sr-only">Buscar películas</label>
            <input
              class="search-input"
              type="search"
              id="search-input"
              name="q"
              placeholder="Buscar película..."
              autocomplete="off"
              aria-label="Buscar películas"
            >
          </form>

          <button
            class="icon-btn search-toggle"
            id="search-toggle"
            aria-label="Abrir búsqueda"
            aria-expanded="false"
            aria-controls="search-form"
          >
            <!-- Icon: Search -->
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
              <circle cx="11" cy="11" r="8"/>
              <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
          </button>
        </div>

        <!-- Avatar / Acceso a cuenta
             data-logged-in="true"  → usuario logueado → lleva al perfil
             data-logged-in="false" → no logueado → lleva a login/registro
        -->
        <a
          href="/perfil"
          class="avatar-btn"
          aria-label="Ver mi perfil"
          data-logged-in="false"
          id="avatar-link"
        >
          <!--
            Cuando el usuario esté logueado, reemplazar el SVG por:
            <img src="/usuarios/avatar.jpg" alt="Avatar de [nombre de usuario]">
          -->
          <!-- Icon: User (fallback) -->
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </a>

        <!-- Botón hamburguesa / Abrir drawer -->
        <button
          class="icon-btn"
          id="menu-toggle"
          aria-label="Abrir menú de navegación"
          aria-expanded="false"
          aria-controls="main-drawer"
          aria-haspopup="dialog"
        >
          <!-- Icon: Menu -->
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
            <line x1="3" y1="6" x2="21" y2="6"/>
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
          </svg>
        </button>

      </div>
    </div>
  </header>

  <!-- ── Contenido principal (demo) ────────────────────────── -->
  <main id="main-content" tabindex="-1">
    <p>Contenido de la página.</p>
  </main>

  <!-- ── Visually hidden utility (accesibilidad) ────────────── -->
  <style>
    .sr-only {
      position: absolute;
      width: 1px; height: 1px;
      padding: 0; margin: -1px;
      overflow: hidden;
      clip: rect(0,0,0,0);
      white-space: nowrap;
      border: 0;
    }
  </style>

  <script>
    /* ── Search toggle ─────────────────────────────────────── */
    const searchWrapper = document.getElementById('search-wrapper');
    const searchToggle  = document.getElementById('search-toggle');
    const searchInput   = document.getElementById('search-input');

    searchToggle.addEventListener('click', () => {
      const isOpen = searchWrapper.classList.toggle('is-open');
      searchToggle.setAttribute('aria-expanded', isOpen);
      searchToggle.setAttribute('aria-label', isOpen ? 'Cerrar búsqueda' : 'Abrir búsqueda');
      if (isOpen) {
        // Pequeño delay para esperar la animación de expansión
        setTimeout(() => searchInput.focus(), 50);
      }
    });

    // Cerrar búsqueda al presionar Escape
    searchInput.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        searchWrapper.classList.remove('is-open');
        searchToggle.setAttribute('aria-expanded', 'false');
        searchToggle.setAttribute('aria-label', 'Abrir búsqueda');
        searchToggle.focus();
      }
    });

    /* ── Drawer ────────────────────────────────────────────── */
    const drawer        = document.getElementById('main-drawer');
    const drawerOverlay = document.getElementById('drawer-overlay');
    const menuToggle    = document.getElementById('menu-toggle');
    const drawerClose   = document.getElementById('drawer-close');

    // Todos los elementos focusables dentro del drawer (para focus trap)
    const getFocusable = () =>
      [...drawer.querySelectorAll('a, button, input, [tabindex]:not([tabindex="-1"])')];

    function openDrawer() {
      drawer.hidden = false;
      // rAF para que la transición CSS se active después de remover hidden
      requestAnimationFrame(() => {
        drawer.classList.add('is-open');
        drawerOverlay.classList.add('is-open');
        drawerOverlay.removeAttribute('aria-hidden');
      });
      menuToggle.setAttribute('aria-expanded', 'true');
      drawer.setAttribute('aria-hidden', 'false');
      // Mover foco al botón de cierre
      drawerClose.focus();
      document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
      drawer.classList.remove('is-open');
      drawerOverlay.classList.remove('is-open');
      drawerOverlay.setAttribute('aria-hidden', 'true');
      menuToggle.setAttribute('aria-expanded', 'false');
      drawer.setAttribute('aria-hidden', 'true');
      // Restaurar scroll y ocultar drawer luego de la transición
      setTimeout(() => { drawer.hidden = true; }, 250);
      document.body.style.overflow = '';
      menuToggle.focus();
    }

    menuToggle.addEventListener('click', openDrawer);
    drawerClose.addEventListener('click', closeDrawer);
    drawerOverlay.addEventListener('click', closeDrawer);

    // Cerrar con Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && drawer.classList.contains('is-open')) {
        closeDrawer();
      }
    });

    // Focus trap dentro del drawer
    drawer.addEventListener('keydown', (e) => {
      if (e.key !== 'Tab') return;
      const focusable = getFocusable();
      const first = focusable[0];
      const last  = focusable[focusable.length - 1];
      if (e.shiftKey) {
        if (document.activeElement === first) { e.preventDefault(); last.focus(); }
      } else {
        if (document.activeElement === last)  { e.preventDefault(); first.focus(); }
      }
    });

    /* ── Avatar: ajuste dinámico según estado de sesión ─────── */
    // En producción: reemplazar con la lógica de autenticación real.
    // Ejemplo: leer una cookie, un token, o el estado del store.
    const avatarLink = document.getElementById('avatar-link');
    const isLoggedIn = false; // <- reemplazar con verificación real

    if (!isLoggedIn) {
      avatarLink.href = '/ingresar';
      avatarLink.setAttribute('aria-label', 'Ingresar o registrarse');
    }
  </script>

</body>
</html>
