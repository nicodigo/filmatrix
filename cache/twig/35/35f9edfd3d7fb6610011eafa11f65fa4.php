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

/* pages/catalog.html.twig */
class __TwigTemplate_3d10ed2af7cffc1d715ecf0687cb1fd2 extends Template
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

  <title>Catálogo — Filmatrix</title>
  <meta name=\"description\" content=\"Explorá el catálogo completo de películas en Filmatrix. Filtrá por género, año y más.\">

  <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
  <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
  <link href=\"https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600&display=swap\" rel=\"stylesheet\">

  <link rel=\"stylesheet\" href=\"/assets/css/base.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/header.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/movie-card.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/footer.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/catalogo.css\">
</head>

";
        // line 21
        yield from $this->load("partials/header.html.twig", 21)->unwrap()->yield($context);
        // line 22
        yield "
<main>

  <!-- ── Encabezado del catálogo ── -->
  <input type=\"checkbox\" id=\"filterToggle\" class=\"filter-toggle-input\">
  <section class=\"catalogo-header\">
    <h1 class=\"catalogo-title\">Catálogo</h1>

    <!--
      Toggle de filtros sin JS:
      checkbox oculto + label actúa como botón.
      El panel se muestra con el selector CSS:
      .filter-toggle-input:checked ~ .filter-panel
    -->
    <label for=\"filterToggle\" class=\"catalogo-filter-btn\">
      <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" viewBox=\"0 0 24 24\"
           fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\"
           stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\">
        <line x1=\"4\" y1=\"6\" x2=\"20\" y2=\"6\"/>
        <line x1=\"8\" y1=\"12\" x2=\"16\" y2=\"12\"/>
        <line x1=\"11\" y1=\"18\" x2=\"13\" y2=\"18\"/>
      </svg>
      Filtros
    </label>
  </section>

  <!-- ── Panel de filtros (CSS-only) ── -->
  <aside class=\"filter-panel\">
    <div class=\"filter-panel__inner\">

      <div class=\"filter-group\">
        <label class=\"filter-label\" for=\"filterGenre\">Género</label>
        <select class=\"filter-select\" id=\"filterGenre\" name=\"genre\">
          <option value=\"\">Todos</option>
          <option value=\"accion\">Acción</option>
          <option value=\"drama\">Drama</option>
          <option value=\"comedia\">Comedia</option>
          <option value=\"terror\">Terror</option>
          <option value=\"ciencia-ficcion\">Ciencia Ficción</option>
          <option value=\"animacion\">Animación</option>
          <option value=\"documental\">Documental</option>
        </select>
      </div>

      <div class=\"filter-group\">
        <label class=\"filter-label\" for=\"filterYear\">Año</label>
        <select class=\"filter-select\" id=\"filterYear\" name=\"year\">
          <option value=\"\">Todos</option>
          ";
        // line 70
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(range($this->extensions['Twig\Extension\CoreExtension']->formatDate("now", "Y"), 1970));
        foreach ($context['_seq'] as $context["_key"] => $context["y"]) {
            // line 71
            yield "            <option value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["y"], "html", null, true);
            yield "\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["y"], "html", null, true);
            yield "</option>
          ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['y'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 73
        yield "        </select>
      </div>

      <div class=\"filter-group\">
        <label class=\"filter-label\" for=\"filterScore\">Puntaje mínimo</label>
        <select class=\"filter-select\" id=\"filterScore\" name=\"score\">
          <option value=\"\">Cualquiera</option>
          <option value=\"4.5\">★ 4.5+</option>
          <option value=\"4\">★ 4.0+</option>
          <option value=\"3\">★ 3.0+</option>
        </select>
      </div>

    </div>
  </aside>


  <section class=\"catalogo-section\">
    <div class=\"catalogo-grid\">
    ";
        // line 92
        if (Twig\Extension\CoreExtension::testEmpty(($context["titles"] ?? null))) {
            // line 93
            yield "        <p class=\"catalogo-empty\">Sin títulos disponibles.</p>
    ";
        }
        // line 95
        yield "
    ";
        // line 96
        $macros["cards"] = $this->macros["cards"] = $this->load("macros/movie-cards.html.twig", 96)->unwrap();
        // line 97
        yield "
    ";
        // line 98
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["titles"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["movie"]) {
            // line 99
            yield "      ";
            yield $macros["cards"]->getTemplateForMacro("macro_movieCard", $context, 99, $this->getSourceContext())->macro_movieCard(...[$context["movie"]]);
            yield "
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['movie'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 101
        yield "
    </div>
  </section>

</main>

";
        // line 107
        yield from $this->load("partials/footer.html.twig", 107)->unwrap()->yield($context);
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "pages/catalog.html.twig";
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
        return array (  187 => 107,  179 => 101,  170 => 99,  166 => 98,  163 => 97,  161 => 96,  158 => 95,  154 => 93,  152 => 92,  131 => 73,  120 => 71,  116 => 70,  66 => 22,  64 => 21,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html lang=\"es\">
<head>
  <meta charset=\"UTF-8\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">

  <title>Catálogo — Filmatrix</title>
  <meta name=\"description\" content=\"Explorá el catálogo completo de películas en Filmatrix. Filtrá por género, año y más.\">

  <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
  <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
  <link href=\"https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600&display=swap\" rel=\"stylesheet\">

  <link rel=\"stylesheet\" href=\"/assets/css/base.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/header.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/movie-card.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/footer.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/catalogo.css\">
</head>

{% include 'partials/header.html.twig' %}

<main>

  <!-- ── Encabezado del catálogo ── -->
  <input type=\"checkbox\" id=\"filterToggle\" class=\"filter-toggle-input\">
  <section class=\"catalogo-header\">
    <h1 class=\"catalogo-title\">Catálogo</h1>

    <!--
      Toggle de filtros sin JS:
      checkbox oculto + label actúa como botón.
      El panel se muestra con el selector CSS:
      .filter-toggle-input:checked ~ .filter-panel
    -->
    <label for=\"filterToggle\" class=\"catalogo-filter-btn\">
      <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" viewBox=\"0 0 24 24\"
           fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\"
           stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\">
        <line x1=\"4\" y1=\"6\" x2=\"20\" y2=\"6\"/>
        <line x1=\"8\" y1=\"12\" x2=\"16\" y2=\"12\"/>
        <line x1=\"11\" y1=\"18\" x2=\"13\" y2=\"18\"/>
      </svg>
      Filtros
    </label>
  </section>

  <!-- ── Panel de filtros (CSS-only) ── -->
  <aside class=\"filter-panel\">
    <div class=\"filter-panel__inner\">

      <div class=\"filter-group\">
        <label class=\"filter-label\" for=\"filterGenre\">Género</label>
        <select class=\"filter-select\" id=\"filterGenre\" name=\"genre\">
          <option value=\"\">Todos</option>
          <option value=\"accion\">Acción</option>
          <option value=\"drama\">Drama</option>
          <option value=\"comedia\">Comedia</option>
          <option value=\"terror\">Terror</option>
          <option value=\"ciencia-ficcion\">Ciencia Ficción</option>
          <option value=\"animacion\">Animación</option>
          <option value=\"documental\">Documental</option>
        </select>
      </div>

      <div class=\"filter-group\">
        <label class=\"filter-label\" for=\"filterYear\">Año</label>
        <select class=\"filter-select\" id=\"filterYear\" name=\"year\">
          <option value=\"\">Todos</option>
          {% for y in range(\"now\"|date(\"Y\"), 1970) %}
            <option value=\"{{ y }}\">{{ y }}</option>
          {% endfor %}
        </select>
      </div>

      <div class=\"filter-group\">
        <label class=\"filter-label\" for=\"filterScore\">Puntaje mínimo</label>
        <select class=\"filter-select\" id=\"filterScore\" name=\"score\">
          <option value=\"\">Cualquiera</option>
          <option value=\"4.5\">★ 4.5+</option>
          <option value=\"4\">★ 4.0+</option>
          <option value=\"3\">★ 3.0+</option>
        </select>
      </div>

    </div>
  </aside>


  <section class=\"catalogo-section\">
    <div class=\"catalogo-grid\">
    {% if titles is empty %}
        <p class=\"catalogo-empty\">Sin títulos disponibles.</p>
    {% endif %}

    {% import 'macros/movie-cards.html.twig' as cards %}

    {% for movie in titles %}
      {{ cards.movieCard(movie) }}
    {% endfor %}

    </div>
  </section>

</main>

{% include 'partials/footer.html.twig' %}
", "pages/catalog.html.twig", "/var/www/html/views/pages/catalog.html.twig");
    }
}
