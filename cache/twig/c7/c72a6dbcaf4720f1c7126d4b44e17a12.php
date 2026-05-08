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

/* pages/register.html.twig */
class __TwigTemplate_a3ef6381e499a50cc0b4e060e7384cf5 extends Template
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
  <title>Crear cuenta — Filmatrix</title>
  <meta name=\"description\" content=\"Creá tu cuenta en Filmatrix y empezá a reseñar películas.\">

  <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
  <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
  <link href=\"https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600&display=swap\" rel=\"stylesheet\">

  <link rel=\"stylesheet\" href=\"/assets/css/base.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/header.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/footer.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/auth.css\">
</head>

<body>

";
        // line 21
        yield from $this->load("partials/header.html.twig", 21)->unwrap()->yield($context);
        // line 22
        yield "
<main class=\"auth-main\">
  <div class=\"auth-card\">

    <div class=\"auth-card__header\">
      <h1 class=\"auth-card__title\">Creá tu cuenta</h1>
      <p class=\"auth-card__subtitle\">Gratis. Sin publicidad. Solo películas.</p>
    </div>

    ";
        // line 31
        if ((($tmp = ($context["error"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 32
            yield "      <div class=\"auth-alert\" role=\"alert\">
        ";
            // line 33
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["error"] ?? null), "html", null, true);
            yield "
      </div>
    ";
        }
        // line 36
        yield "
    <form class=\"auth-form\" method=\"POST\" action=\"/register\" novalidate>

      <div class=\"auth-field\">
        <label class=\"auth-label\" for=\"username\">Nombre</label>
        <input class=\"auth-input\" type=\"text\" id=\"username\" name=\"username\"
               value=\"";
        // line 42
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["fields"] ?? null), "username", [], "any", true, true, false, 42)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["fields"] ?? null), "username", [], "any", false, false, false, 42), "")) : ("")), "html", null, true);
        yield "\"
               placeholder=\"Tu nombre\" required>
      </div>

      <div class=\"auth-field\">
        <label class=\"auth-label\" for=\"email\">Email</label>
        <input class=\"auth-input\" type=\"email\" id=\"email\" name=\"email\"
               value=\"";
        // line 49
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["fields"] ?? null), "email", [], "any", true, true, false, 49)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["fields"] ?? null), "email", [], "any", false, false, false, 49), "")) : ("")), "html", null, true);
        yield "\"
               placeholder=\"tu@email.com\" required>
      </div>

      <div class=\"auth-field\">
        <label class=\"auth-label\" for=\"password\">Contraseña</label>
        <input class=\"auth-input\" type=\"password\" id=\"password\" name=\"password\"
               placeholder=\"Mínimo 8 caracteres\" required>
      </div>

      <div class=\"auth-field\">
        <label class=\"auth-label\" for=\"confirm_password\">Repetir contraseña</label>
        <input class=\"auth-input\" type=\"password\" id=\"confirm_password\" name=\"confirm_password\"
               placeholder=\"••••••••\" required>
      </div>

      <button type=\"submit\" class=\"auth-submit\">Crear cuenta</button>
    </form>

    <p class=\"auth-switch\">
      ¿Ya tenés cuenta?
      <a class=\"auth-link\" href=\"/login\">Iniciá sesión</a>
    </p>

  </div>
</main>

";
        // line 76
        yield from $this->load("partials/footer.html.twig", 76)->unwrap()->yield($context);
        // line 77
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
        return "pages/register.html.twig";
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
        return array (  138 => 77,  136 => 76,  106 => 49,  96 => 42,  88 => 36,  82 => 33,  79 => 32,  77 => 31,  66 => 22,  64 => 21,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html lang=\"es\">
<head>
  <meta charset=\"UTF-8\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
  <title>Crear cuenta — Filmatrix</title>
  <meta name=\"description\" content=\"Creá tu cuenta en Filmatrix y empezá a reseñar películas.\">

  <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
  <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
  <link href=\"https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600&display=swap\" rel=\"stylesheet\">

  <link rel=\"stylesheet\" href=\"/assets/css/base.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/header.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/footer.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/auth.css\">
</head>

<body>

{% include 'partials/header.html.twig' %}

<main class=\"auth-main\">
  <div class=\"auth-card\">

    <div class=\"auth-card__header\">
      <h1 class=\"auth-card__title\">Creá tu cuenta</h1>
      <p class=\"auth-card__subtitle\">Gratis. Sin publicidad. Solo películas.</p>
    </div>

    {% if error %}
      <div class=\"auth-alert\" role=\"alert\">
        {{ error }}
      </div>
    {% endif %}

    <form class=\"auth-form\" method=\"POST\" action=\"/register\" novalidate>

      <div class=\"auth-field\">
        <label class=\"auth-label\" for=\"username\">Nombre</label>
        <input class=\"auth-input\" type=\"text\" id=\"username\" name=\"username\"
               value=\"{{ fields.username|default('') }}\"
               placeholder=\"Tu nombre\" required>
      </div>

      <div class=\"auth-field\">
        <label class=\"auth-label\" for=\"email\">Email</label>
        <input class=\"auth-input\" type=\"email\" id=\"email\" name=\"email\"
               value=\"{{ fields.email|default('') }}\"
               placeholder=\"tu@email.com\" required>
      </div>

      <div class=\"auth-field\">
        <label class=\"auth-label\" for=\"password\">Contraseña</label>
        <input class=\"auth-input\" type=\"password\" id=\"password\" name=\"password\"
               placeholder=\"Mínimo 8 caracteres\" required>
      </div>

      <div class=\"auth-field\">
        <label class=\"auth-label\" for=\"confirm_password\">Repetir contraseña</label>
        <input class=\"auth-input\" type=\"password\" id=\"confirm_password\" name=\"confirm_password\"
               placeholder=\"••••••••\" required>
      </div>

      <button type=\"submit\" class=\"auth-submit\">Crear cuenta</button>
    </form>

    <p class=\"auth-switch\">
      ¿Ya tenés cuenta?
      <a class=\"auth-link\" href=\"/login\">Iniciá sesión</a>
    </p>

  </div>
</main>

{% include 'partials/footer.html.twig' %}

</body>
</html>
", "pages/register.html.twig", "/var/www/html/views/pages/register.html.twig");
    }
}
