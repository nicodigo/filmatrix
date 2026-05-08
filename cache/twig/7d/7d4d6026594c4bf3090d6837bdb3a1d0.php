<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* pages/profile.html.twig */
class __TwigTemplate_6312cf5f03f7576995735bc794b60ca7 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<!DOCTYPE html>
<html lang=\"es\">
<head>
  <meta charset=\"UTF-8\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
  <title>Mi perfil — Filmatrix</title>

  <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
  <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
  <link href=\"https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600&display=swap\" rel=\"stylesheet\">

  <link rel=\"stylesheet\" href=\"/assets/css/base.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/header.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/footer.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/miPerfil.css\">
</head>

<body>

";
        // line 20
        yield from $this->load("partials/header.html.twig", 20)->unwrap()->yield($context);
        // line 21
        yield "
<main class=\"perfil-main\">

  <section class=\"perfil-hero\">
    <div class=\"perfil-avatar-wrap\">
      <img class=\"perfil-avatar\" src=\"/assets/img/user_avatar.png\" alt=\"Avatar de ";
        // line 26
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "username", [], "any", false, false, false, 26), "html", null, true);
        yield "\">
    </div>

    <div class=\"perfil-hero__info\">
      <h1 class=\"perfil-nombre\">";
        // line 30
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "username", [], "any", false, false, false, 30), "html", null, true);
        yield "</h1>
      <p class=\"perfil-email\">";
        // line 31
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "email", [], "any", false, false, false, 31), "html", null, true);
        yield "</p>
      <p class=\"perfil-miembro\">Miembro desde ";
        // line 32
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "createdAt", [], "any", false, false, false, 32), "F Y"), "html", null, true);
        yield "</p>
    </div>
  </section>

  <section class=\"perfil-stats\">
    <div class=\"perfil-stat\">
      <span class=\"perfil-stat__num\">0</span>
      <span class=\"perfil-stat__label\">Reseñas</span>
    </div>

    <div class=\"perfil-stat\">
      <span class=\"perfil-stat__num\">0</span>
      <span class=\"perfil-stat__label\">Favoritas</span>
    </div>
  </section>

  <section class=\"perfil-acciones\">
    <a href=\"/profile/edit\" class=\"perfil-btn perfil-btn--primary\">Editar perfil</a>

    <form action=\"/logout\" method=\"POST\">
      <button type=\"submit\" class=\"perfil-btn perfil-btn--danger\">Cerrar sesión</button>
    </form>
  </section>

</main>

";
        // line 58
        yield from $this->load("partials/footer.html.twig", 58)->unwrap()->yield($context);
        // line 59
        yield "
</body>
</html>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "pages/profile.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  118 => 59,  116 => 58,  87 => 32,  83 => 31,  79 => 30,  72 => 26,  65 => 21,  63 => 20,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html lang=\"es\">
<head>
  <meta charset=\"UTF-8\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
  <title>Mi perfil — Filmatrix</title>

  <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
  <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
  <link href=\"https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600&display=swap\" rel=\"stylesheet\">

  <link rel=\"stylesheet\" href=\"/assets/css/base.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/header.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/footer.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/miPerfil.css\">
</head>

<body>

{% include 'partials/header.html.twig' %}

<main class=\"perfil-main\">

  <section class=\"perfil-hero\">
    <div class=\"perfil-avatar-wrap\">
      <img class=\"perfil-avatar\" src=\"/assets/img/user_avatar.png\" alt=\"Avatar de {{ user.username }}\">
    </div>

    <div class=\"perfil-hero__info\">
      <h1 class=\"perfil-nombre\">{{ user.username }}</h1>
      <p class=\"perfil-email\">{{ user.email }}</p>
      <p class=\"perfil-miembro\">Miembro desde {{ user.createdAt|date('F Y') }}</p>
    </div>
  </section>

  <section class=\"perfil-stats\">
    <div class=\"perfil-stat\">
      <span class=\"perfil-stat__num\">0</span>
      <span class=\"perfil-stat__label\">Reseñas</span>
    </div>

    <div class=\"perfil-stat\">
      <span class=\"perfil-stat__num\">0</span>
      <span class=\"perfil-stat__label\">Favoritas</span>
    </div>
  </section>

  <section class=\"perfil-acciones\">
    <a href=\"/profile/edit\" class=\"perfil-btn perfil-btn--primary\">Editar perfil</a>

    <form action=\"/logout\" method=\"POST\">
      <button type=\"submit\" class=\"perfil-btn perfil-btn--danger\">Cerrar sesión</button>
    </form>
  </section>

</main>

{% include 'partials/footer.html.twig' %}

</body>
</html>
", "pages/profile.html.twig", "/var/www/html/views/pages/profile.html.twig");
    }
}
