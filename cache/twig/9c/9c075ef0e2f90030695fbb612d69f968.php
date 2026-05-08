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

/* pages/home.html.twig */
class __TwigTemplate_b37eae93a77bf7780ec7582ae3e6bd77 extends Template
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

  <!-- SEO básico -->
  <title>Filmatrix — Donde brillan tus reseñas</title>
  <meta name=\"description\" content=\"Tu diario cinematográfico. Registrá, descubrí y compartí las películas que te definen.\">

  <!-- Preconexión para optimizar la velocidad -->
  <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
  <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>

  <!-- Importación de Inter (Sans-Serif) y DM Serif Display (Serif) -->
  <!-- <link href=\"https://googleapis.com\" rel=\"stylesheet\"> -->
   <link href=\"https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600&display=swap\" rel=\"stylesheet\">

  <link rel=\"stylesheet\" href=\"/assets/css/base.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/header.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/home.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/hero.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/movie-card.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/footer.css\">
</head>

";
        // line 27
        yield from $this->load("partials/header.html.twig", 27)->unwrap()->yield($context);
        // line 28
        yield "
<main>
  <section class=\"hero\">
    <h1>Filmatrix</h1>
    <h2>Donde brillan tus reseñas</h2>
  </section>

  <section class=\"popular-movies\">
    <div class=\"movie-flex\">
      ";
        // line 37
        if (Twig\Extension\CoreExtension::testEmpty(($context["popular"] ?? null))) {
            // line 38
            yield "        <p class=\"catalogo-empty\">Sin títulos disponibles.</p>
      ";
        }
        // line 40
        yield "
      ";
        // line 41
        $macros["cards"] = $this->macros["cards"] = $this->load("macros/movie-cards.html.twig", 41)->unwrap();
        // line 42
        yield "
      ";
        // line 43
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["popular"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["movie"]) {
            // line 44
            yield "        ";
            yield $macros["cards"]->getTemplateForMacro("macro_movieCard", $context, 44, $this->getSourceContext())->macro_movieCard(...[$context["movie"]]);
            yield "
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['movie'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 46
        yield "    </div>
  </section>


  <section class=\"daily-review\">
    <h2>Reseña del día</h2>
    <article>
      <a href=\"#\">
        <figure>
          <img src=\"";
        // line 55
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dailyReview"] ?? null), "url_banner", [], "any", false, false, false, 55), "html", null, true);
        yield "\" alt=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dailyReview"] ?? null), "title", [], "any", false, false, false, 55), "html", null, true);
        yield "\">
        </figure>
      </a>
      <div class=\"daily-review__content\">
        <h3 class=\"daily-review__title\">";
        // line 59
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dailyReview"] ?? null), "title", [], "any", false, false, false, 59), "html", null, true);
        yield "</h3>
        <span class=\"daily-review__year\">";
        // line 60
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dailyReview"] ?? null), "year", [], "any", false, false, false, 60), "html", null, true);
        yield "</span>
        <div class=\"daily-review__author\">
          <img src=\"";
        // line 62
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dailyReview"] ?? null), "avatar", [], "any", false, false, false, 62), "html", null, true);
        yield "\" alt=\"Avatar de ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dailyReview"] ?? null), "author", [], "any", false, false, false, 62), "html", null, true);
        yield "\">
          <span> ";
        // line 63
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dailyReview"] ?? null), "author", [], "any", false, false, false, 63), "html", null, true);
        yield "</span>
        </div>
        <p class=\"daily-review__text\"> ";
        // line 65
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dailyReview"] ?? null), "body", [], "any", false, false, false, 65), "html", null, true);
        yield "</p>
        <span class=\"daily-review__likes\">&hearts; ";
        // line 66
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["dailyReview"] ?? null), "likes", [], "any", false, false, false, 66), "html", null, true);
        yield "</span>
      </div>
    </article>
  </section>
</main>

";
        // line 72
        yield from $this->load("partials/footer.html.twig", 72)->unwrap()->yield($context);
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "pages/home.html.twig";
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
        return array (  163 => 72,  154 => 66,  150 => 65,  145 => 63,  139 => 62,  134 => 60,  130 => 59,  121 => 55,  110 => 46,  101 => 44,  97 => 43,  94 => 42,  92 => 41,  89 => 40,  85 => 38,  83 => 37,  72 => 28,  70 => 27,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html lang=\"es\">
<head>
  <meta charset=\"UTF-8\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">

  <!-- SEO básico -->
  <title>Filmatrix — Donde brillan tus reseñas</title>
  <meta name=\"description\" content=\"Tu diario cinematográfico. Registrá, descubrí y compartí las películas que te definen.\">

  <!-- Preconexión para optimizar la velocidad -->
  <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
  <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>

  <!-- Importación de Inter (Sans-Serif) y DM Serif Display (Serif) -->
  <!-- <link href=\"https://googleapis.com\" rel=\"stylesheet\"> -->
   <link href=\"https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600&display=swap\" rel=\"stylesheet\">

  <link rel=\"stylesheet\" href=\"/assets/css/base.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/header.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/home.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/hero.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/movie-card.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/footer.css\">
</head>

{% include 'partials/header.html.twig' %}

<main>
  <section class=\"hero\">
    <h1>Filmatrix</h1>
    <h2>Donde brillan tus reseñas</h2>
  </section>

  <section class=\"popular-movies\">
    <div class=\"movie-flex\">
      {% if (popular is empty) %}
        <p class=\"catalogo-empty\">Sin títulos disponibles.</p>
      {% endif %}

      {% import 'macros/movie-cards.html.twig' as cards %}

      {% for movie in popular %}
        {{ cards.movieCard(movie) }}
      {% endfor %}
    </div>
  </section>


  <section class=\"daily-review\">
    <h2>Reseña del día</h2>
    <article>
      <a href=\"#\">
        <figure>
          <img src=\"{{ dailyReview.url_banner }}\" alt=\"{{ dailyReview.title }}\">
        </figure>
      </a>
      <div class=\"daily-review__content\">
        <h3 class=\"daily-review__title\">{{ dailyReview.title }}</h3>
        <span class=\"daily-review__year\">{{ dailyReview.year }}</span>
        <div class=\"daily-review__author\">
          <img src=\"{{ dailyReview.avatar }}\" alt=\"Avatar de {{ dailyReview.author }}\">
          <span> {{ dailyReview.author }}</span>
        </div>
        <p class=\"daily-review__text\"> {{dailyReview.body }}</p>
        <span class=\"daily-review__likes\">&hearts; {{ dailyReview.likes }}</span>
      </div>
    </article>
  </section>
</main>

{% include 'partials/footer.html.twig' %}
", "pages/home.html.twig", "/var/www/html/views/pages/home.html.twig");
    }
}
