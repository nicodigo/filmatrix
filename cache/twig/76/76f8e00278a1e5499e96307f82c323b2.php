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

/* pages/movieDetails.html.twig */
class __TwigTemplate_ac18d2e31375cb5bf3028f86a00b0e05 extends Template
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

  <title>Detalle — Filmatrix</title>
  <meta name=\"description\" content=\"Información, reparto y reseñas de la película en Filmatrix.\">

  <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
  <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
  <link href=\"https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600&display=swap\" rel=\"stylesheet\">

  <link rel=\"stylesheet\" href=\"/assets/css/base.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/header.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/movie-card.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/footer.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/detalle_pelicula.css\">
</head>

";
        // line 22
        yield from $this->load("partials/header.html.twig", 22)->unwrap()->yield($context);
        // line 23
        yield "
<main class=\"detalle-main\">
  <!-- ══════════════════════════════════
       PORTADA + TÍTULO
  ══════════════════════════════════════ -->
  <section class=\"detalle-hero\">
    <div class=\"detalle-hero__poster-wrap\">
      <img
        class=\"detalle-hero__poster\"
        src=\"";
        // line 32
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["title"] ?? null), "posterUrl", [], "any", true, true, false, 32)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["title"] ?? null), "posterUrl", [], "any", false, false, false, 32), "/assets/img/hero-bg.webp")) : ("/assets/img/hero-bg.webp")), "html", null, true);
        yield "\"
        alt=\"Portada de ";
        // line 33
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["title"] ?? null), "title", [], "any", false, false, false, 33), "html", null, true);
        yield "\">
    </div>
    <div class=\"detalle-hero__info\">
      <h1 class=\"detalle-titulo\">";
        // line 36
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["title"] ?? null), "title", [], "any", false, false, false, 36), "html", null, true);
        yield "</h1>
      <div class=\"detalle-meta\">
        <span class=\"detalle-meta__item\">";
        // line 38
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["title"] ?? null), "releaseYear", [], "any", true, true, false, 38)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["title"] ?? null), "releaseYear", [], "any", false, false, false, 38), "S/D")) : ("S/D")), "html", null, true);
        yield "</span>
        <span class=\"detalle-meta__sep\">·</span>
        <span class=\"detalle-meta__item\">";
        // line 40
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("duration", $context)) ? (Twig\Extension\CoreExtension::default(($context["duration"] ?? null), "S/D")) : ("S/D")), "html", null, true);
        yield "</span>
        <span class=\"detalle-meta__sep\">·</span>
        <span class=\"detalle-meta__item\">";
        // line 42
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("genreLabel", $context)) ? (Twig\Extension\CoreExtension::default(($context["genreLabel"] ?? null), "S/D")) : ("S/D")), "html", null, true);
        yield "</span>
      </div>
    </div>
  </section>

  <!-- ══════════════════════════════════
       SINOPSIS / VALORACIÓN / GÉNERO
  ══════════════════════════════════════ -->
  <section class=\"detalle-info-section\">
    <div class=\"detalle-info-grid\">
      <div class=\"detalle-sinopsis\">
        <h2 class=\"detalle-section-label\">Sinopsis</h2>
        <p class=\"detalle-sinopsis__texto\">";
        // line 54
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["title"] ?? null), "synopsis", [], "any", true, true, false, 54)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["title"] ?? null), "synopsis", [], "any", false, false, false, 54), "Sin sinopsis disponible.")) : ("Sin sinopsis disponible.")), "html", null, true);
        yield "</p>
      </div>
      <div class=\"detalle-datos\">
        <div class=\"detalle-dato\">
          <span class=\"detalle-dato__label\">Valoración</span>
          <div class=\"detalle-dato__rating\">
            <svg class=\"detalle-dato__star\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\" fill=\"currentColor\" aria-hidden=\"true\">
              <path d=\"M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z\" />
            </svg>
            <span class=\"detalle-dato__val\">";
        // line 63
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["title"] ?? null), "avgScore", [], "any", true, true, false, 63)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["title"] ?? null), "avgScore", [], "any", false, false, false, 63), "S/P")) : ("S/P")), "html", null, true);
        yield "</span>
            <span class=\"detalle-dato__max\">/ 5</span>
          </div>
        </div>
        <div class=\"detalle-dato\">
          <span class=\"detalle-dato__label\">Género</span>
          <span class=\"detalle-dato__badge\">";
        // line 69
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("genreLabel", $context)) ? (Twig\Extension\CoreExtension::default(($context["genreLabel"] ?? null), "S/D")) : ("S/D")), "html", null, true);
        yield "</span>
        </div>
        <div class=\"detalle-dato\">
          <span class=\"detalle-dato__label\">Año</span>
          <span class=\"detalle-dato__val\">";
        // line 73
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["title"] ?? null), "releaseYear", [], "any", true, true, false, 73)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["title"] ?? null), "releaseYear", [], "any", false, false, false, 73), "S/D")) : ("S/D")), "html", null, true);
        yield "</span>
        </div>
        <div class=\"detalle-dato\">
          <span class=\"detalle-dato__label\">Duración</span>
          <span class=\"detalle-dato__val\">";
        // line 77
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("duration", $context)) ? (Twig\Extension\CoreExtension::default(($context["duration"] ?? null), "S/D")) : ("S/D")), "html", null, true);
        yield "</span>
        </div>
        <div class=\"detalle-dato\">
          <span class=\"detalle-dato__label\">Idioma original</span>
          <span class=\"detalle-dato__val\">";
        // line 81
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::default(Twig\Extension\CoreExtension::upper($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, ($context["title"] ?? null), "language", [], "any", false, false, false, 81)), "S/D"), "html", null, true);
        yield "</span>
        </div>
        ";
        // line 83
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["title"] ?? null), "tmdbRating", [], "any", false, false, false, 83)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 84
            yield "          <div class=\"detalle-dato\">
            <span class=\"detalle-dato__label\">Rating TMDB</span>
            <span class=\"detalle-dato__val\">";
            // line 86
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatNumber(CoreExtension::getAttribute($this->env, $this->source, ($context["title"] ?? null), "tmdbRating", [], "any", false, false, false, 86), 1), "html", null, true);
            yield " / 10</span>
          </div>
        ";
        }
        // line 89
        yield "      </div>
    </div>
  </section>
  <section class=\"detalle-reparto-section\">
    <h2 class=\"detalle-section-label\">Reparto</h2>
    <div class=\"reparto-grid\">
      ";
        // line 95
        if (Twig\Extension\CoreExtension::testEmpty(($context["cast"] ?? null))) {
            // line 96
            yield "        <p class=\"detalle-empty\">Sin información de reparto.</p>
      ";
        } else {
            // line 98
            yield "        ";
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(($context["cast"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["actor"]) {
                // line 99
                yield "          <div class=\"reparto-card\">
            <div class=\"reparto-card__foto-wrap\">
              <img
                class=\"reparto-card__foto\"
                src=\"";
                // line 103
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, $context["actor"], "profile_url", [], "any", true, true, false, 103)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, $context["actor"], "profile_url", [], "any", false, false, false, 103), "/assets/img/hero-bg.webp")) : ("/assets/img/hero-bg.webp")), "html", null, true);
                yield "\"
                alt=\"Foto de ";
                // line 104
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["actor"], "name", [], "any", false, false, false, 104), "html", null, true);
                yield "\">
            </div>
            <span class=\"reparto-card__nombre\">";
                // line 106
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["actor"], "name", [], "any", false, false, false, 106), "html", null, true);
                yield "</span>
            <span class=\"reparto-card__rol\">";
                // line 107
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, $context["actor"], "character_name", [], "any", true, true, false, 107)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, $context["actor"], "character_name", [], "any", false, false, false, 107), CoreExtension::getAttribute($this->env, $this->source, $context["actor"], "role", [], "any", false, false, false, 107))) : (CoreExtension::getAttribute($this->env, $this->source, $context["actor"], "role", [], "any", false, false, false, 107))), "html", null, true);
                yield "</span>
          </div>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['actor'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 110
            yield "      ";
        }
        // line 111
        yield "    </div>
  </section>

  <h2>
    ";
        // line 115
        if (array_key_exists("flashError", $context)) {
            // line 116
            yield "      <p>";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["flashError"] ?? null), "html", null, true);
            yield "</p>
    ";
        }
        // line 118
        yield "  </h2>

  <section class=\"detalle-nueva-resenia-section\">
    <input type=\"checkbox\" id=\"resenaToggle\" class=\"resena-toggle-input\">
    <label for=\"resenaToggle\" class=\"btn-crear-resenia\">
      <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" viewBox=\"0 0 24 24\"
        fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\"
        stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\">
        <path d=\"M12 20h9\" />
        <path d=\"M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z\" />
      </svg>
      Escribir reseña
    </label>
    <div class=\"resena-form-wrap\">
      <form class=\"resena-form\" action=\"/review/post\" method=\"POST\">
        <input type=\"hidden\" name=\"title-id\" value=\"";
        // line 133
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["title"] ?? null), "id", [], "any", false, false, false, 133), "html", null, true);
        yield "\">
        <input type=\"hidden\" name=\"tmdb-id\" value=\"";
        // line 134
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["tmdbId"] ?? null), "html", null, true);
        yield "\">
        <div class=\"resena-form__stars\" role=\"group\" aria-label=\"Puntuación\">
          ";
        // line 136
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(range(5, 1));
        foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
            // line 137
            yield "            <input type=\"radio\" id=\"star";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["i"], "html", null, true);
            yield "\" name=\"score\" value=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["i"], "html", null, true);
            yield "\" class=\"star-input\">
            <label for=\"star";
            // line 138
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["i"], "html", null, true);
            yield "\" class=\"star-label\" aria-label=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["i"], "html", null, true);
            yield " estrellas\">★</label>
          ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['i'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 140
        yield "        </div>
        <textarea
          class=\"resena-form__textarea\"
          name=\"review-text\"
          placeholder=\"Contá qué te pareció la película…\"
          rows=\"5\"
          maxlength=\"1000\"></textarea>
        <div class=\"resena-form__actions\">
          <label for=\"resenaToggle\" class=\"btn-cancelar\">Cancelar</label>
          <button type=\"submit\" class=\"btn-publicar\">Publicar reseña</button>
        </div>
      </form>
    </div>
  </section>

  <section class=\"detalle-bottom-section\">
    <div class=\"resenias-col\">
      <h2 class=\"detalle-section-label\">Reseñas</h2>
      ";
        // line 158
        if (Twig\Extension\CoreExtension::testEmpty(($context["reviews"] ?? null))) {
            // line 159
            yield "        <p class=\"detalle-empty\">Todavía no hay reseñas.</p>
      ";
        } else {
            // line 161
            yield "        ";
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(($context["reviews"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["review"]) {
                // line 162
                yield "          <article class=\"resenia-card\">
            <header class=\"resenia-card__header\">
              <img
                class=\"resenia-card__avatar\"
                src=\"/assets/img/hero-bg.webp\"
                alt=\"Avatar de ";
                // line 167
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["review"], "username", [], "any", false, false, false, 167), "html", null, true);
                yield "\">
              <div class=\"resenia-card__meta\">
                <span class=\"resenia-card__usuario\">";
                // line 169
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["review"], "username", [], "any", false, false, false, 169), "html", null, true);
                yield "</span>
                <div class=\"resenia-card__estrellas\" aria-label=\"";
                // line 170
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::round(CoreExtension::getAttribute($this->env, $this->source, $context["review"], "score", [], "any", false, false, false, 170)), "html", null, true);
                yield " estrellas\">
                  ";
                // line 171
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable(range(1, 5));
                foreach ($context['_seq'] as $context["_key"] => $context["i"]) {
                    // line 172
                    yield "                    <span class=\"resenia-card__star ";
                    yield ((($context["i"] <= Twig\Extension\CoreExtension::round(CoreExtension::getAttribute($this->env, $this->source, $context["review"], "score", [], "any", false, false, false, 172), 0))) ? ("resenia-card__star--filled") : (""));
                    yield "\">★</span>
                  ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_key'], $context['i'], $context['_parent']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 174
                yield "                </div>
              </div>
              <time class=\"resenia-card__fecha\">";
                // line 176
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["review"], "created_at", [], "any", false, false, false, 176), "d M Y"), "html", null, true);
                yield "</time>
            </header>
            <p class=\"resenia-card__texto\">";
                // line 178
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, $context["review"], "body", [], "any", true, true, false, 178)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, $context["review"], "body", [], "any", false, false, false, 178), "")) : ("")), "html", null, true);
                yield "</p>
          </article>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['review'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 181
            yield "      ";
        }
        // line 182
        yield "    </div>

    <aside class=\"sugeridas-col\">
      <h2 class=\"detalle-section-label\">Tal vez te interese</h2>
      <div class=\"sugeridas-lista\">
        ";
        // line 187
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["suggested"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["sug"]) {
            // line 188
            yield "          <a href=\"/movie?tmdb_id=";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["sug"], "tmdb_id", [], "any", false, false, false, 188), "html", null, true);
            yield "\" class=\"sugerida-item\">
            <img
              class=\"sugerida-item__poster\"
              src=\"";
            // line 191
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, $context["sug"], "poster_url", [], "any", true, true, false, 191)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, $context["sug"], "poster_url", [], "any", false, false, false, 191), "/assets/img/hero-bg.webp")) : ("/assets/img/hero-bg.webp")), "html", null, true);
            yield "\"
              alt=\"Portada de ";
            // line 192
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["sug"], "title", [], "any", false, false, false, 192), "html", null, true);
            yield "\">
            <span class=\"sugerida-item__titulo\">";
            // line 193
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["sug"], "title", [], "any", false, false, false, 193), "html", null, true);
            yield "</span>
          </a>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['sug'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 196
        yield "      </div>
    </aside>
  </section>

</main>

";
        // line 202
        yield from $this->load("partials/footer.html.twig", 202)->unwrap()->yield($context);
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "pages/movieDetails.html.twig";
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
        return array (  416 => 202,  408 => 196,  399 => 193,  395 => 192,  391 => 191,  384 => 188,  380 => 187,  373 => 182,  370 => 181,  361 => 178,  356 => 176,  352 => 174,  343 => 172,  339 => 171,  335 => 170,  331 => 169,  326 => 167,  319 => 162,  314 => 161,  310 => 159,  308 => 158,  288 => 140,  278 => 138,  271 => 137,  267 => 136,  262 => 134,  258 => 133,  241 => 118,  235 => 116,  233 => 115,  227 => 111,  224 => 110,  215 => 107,  211 => 106,  206 => 104,  202 => 103,  196 => 99,  191 => 98,  187 => 96,  185 => 95,  177 => 89,  171 => 86,  167 => 84,  165 => 83,  160 => 81,  153 => 77,  146 => 73,  139 => 69,  130 => 63,  118 => 54,  103 => 42,  98 => 40,  93 => 38,  88 => 36,  82 => 33,  78 => 32,  67 => 23,  65 => 22,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html lang=\"es\">

<head>
  <meta charset=\"UTF-8\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">

  <title>Detalle — Filmatrix</title>
  <meta name=\"description\" content=\"Información, reparto y reseñas de la película en Filmatrix.\">

  <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
  <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
  <link href=\"https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600&display=swap\" rel=\"stylesheet\">

  <link rel=\"stylesheet\" href=\"/assets/css/base.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/header.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/movie-card.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/footer.css\">
  <link rel=\"stylesheet\" href=\"/assets/css/detalle_pelicula.css\">
</head>

{% include 'partials/header.html.twig' %}

<main class=\"detalle-main\">
  <!-- ══════════════════════════════════
       PORTADA + TÍTULO
  ══════════════════════════════════════ -->
  <section class=\"detalle-hero\">
    <div class=\"detalle-hero__poster-wrap\">
      <img
        class=\"detalle-hero__poster\"
        src=\"{{ title.posterUrl|default('/assets/img/hero-bg.webp') }}\"
        alt=\"Portada de {{ title.title }}\">
    </div>
    <div class=\"detalle-hero__info\">
      <h1 class=\"detalle-titulo\">{{ title.title }}</h1>
      <div class=\"detalle-meta\">
        <span class=\"detalle-meta__item\">{{ title.releaseYear|default('S/D') }}</span>
        <span class=\"detalle-meta__sep\">·</span>
        <span class=\"detalle-meta__item\">{{ duration|default('S/D') }}</span>
        <span class=\"detalle-meta__sep\">·</span>
        <span class=\"detalle-meta__item\">{{ genreLabel|default('S/D') }}</span>
      </div>
    </div>
  </section>

  <!-- ══════════════════════════════════
       SINOPSIS / VALORACIÓN / GÉNERO
  ══════════════════════════════════════ -->
  <section class=\"detalle-info-section\">
    <div class=\"detalle-info-grid\">
      <div class=\"detalle-sinopsis\">
        <h2 class=\"detalle-section-label\">Sinopsis</h2>
        <p class=\"detalle-sinopsis__texto\">{{ title.synopsis|default('Sin sinopsis disponible.') }}</p>
      </div>
      <div class=\"detalle-datos\">
        <div class=\"detalle-dato\">
          <span class=\"detalle-dato__label\">Valoración</span>
          <div class=\"detalle-dato__rating\">
            <svg class=\"detalle-dato__star\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\" fill=\"currentColor\" aria-hidden=\"true\">
              <path d=\"M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z\" />
            </svg>
            <span class=\"detalle-dato__val\">{{ title.avgScore|default('S/P') }}</span>
            <span class=\"detalle-dato__max\">/ 5</span>
          </div>
        </div>
        <div class=\"detalle-dato\">
          <span class=\"detalle-dato__label\">Género</span>
          <span class=\"detalle-dato__badge\">{{ genreLabel|default('S/D') }}</span>
        </div>
        <div class=\"detalle-dato\">
          <span class=\"detalle-dato__label\">Año</span>
          <span class=\"detalle-dato__val\">{{ title.releaseYear|default('S/D') }}</span>
        </div>
        <div class=\"detalle-dato\">
          <span class=\"detalle-dato__label\">Duración</span>
          <span class=\"detalle-dato__val\">{{ duration|default('S/D') }}</span>
        </div>
        <div class=\"detalle-dato\">
          <span class=\"detalle-dato__label\">Idioma original</span>
          <span class=\"detalle-dato__val\">{{ title.language|upper|default('S/D') }}</span>
        </div>
        {% if title.tmdbRating %}
          <div class=\"detalle-dato\">
            <span class=\"detalle-dato__label\">Rating TMDB</span>
            <span class=\"detalle-dato__val\">{{ title.tmdbRating|number_format(1) }} / 10</span>
          </div>
        {% endif %}
      </div>
    </div>
  </section>
  <section class=\"detalle-reparto-section\">
    <h2 class=\"detalle-section-label\">Reparto</h2>
    <div class=\"reparto-grid\">
      {% if cast is empty %}
        <p class=\"detalle-empty\">Sin información de reparto.</p>
      {% else %}
        {% for actor in cast %}
          <div class=\"reparto-card\">
            <div class=\"reparto-card__foto-wrap\">
              <img
                class=\"reparto-card__foto\"
                src=\"{{ actor.profile_url|default('/assets/img/hero-bg.webp') }}\"
                alt=\"Foto de {{ actor.name }}\">
            </div>
            <span class=\"reparto-card__nombre\">{{ actor.name }}</span>
            <span class=\"reparto-card__rol\">{{ actor.character_name|default(actor.role) }}</span>
          </div>
        {% endfor %}
      {% endif %}
    </div>
  </section>

  <h2>
    {% if flashError is defined %}
      <p>{{ flashError }}</p>
    {% endif %}
  </h2>

  <section class=\"detalle-nueva-resenia-section\">
    <input type=\"checkbox\" id=\"resenaToggle\" class=\"resena-toggle-input\">
    <label for=\"resenaToggle\" class=\"btn-crear-resenia\">
      <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" viewBox=\"0 0 24 24\"
        fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\"
        stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\">
        <path d=\"M12 20h9\" />
        <path d=\"M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z\" />
      </svg>
      Escribir reseña
    </label>
    <div class=\"resena-form-wrap\">
      <form class=\"resena-form\" action=\"/review/post\" method=\"POST\">
        <input type=\"hidden\" name=\"title-id\" value=\"{{ title.id }}\">
        <input type=\"hidden\" name=\"tmdb-id\" value=\"{{ tmdbId }}\">
        <div class=\"resena-form__stars\" role=\"group\" aria-label=\"Puntuación\">
          {% for i in range(5, 1) %}
            <input type=\"radio\" id=\"star{{ i }}\" name=\"score\" value=\"{{ i }}\" class=\"star-input\">
            <label for=\"star{{ i }}\" class=\"star-label\" aria-label=\"{{ i }} estrellas\">★</label>
          {% endfor %}
        </div>
        <textarea
          class=\"resena-form__textarea\"
          name=\"review-text\"
          placeholder=\"Contá qué te pareció la película…\"
          rows=\"5\"
          maxlength=\"1000\"></textarea>
        <div class=\"resena-form__actions\">
          <label for=\"resenaToggle\" class=\"btn-cancelar\">Cancelar</label>
          <button type=\"submit\" class=\"btn-publicar\">Publicar reseña</button>
        </div>
      </form>
    </div>
  </section>

  <section class=\"detalle-bottom-section\">
    <div class=\"resenias-col\">
      <h2 class=\"detalle-section-label\">Reseñas</h2>
      {% if reviews is empty %}
        <p class=\"detalle-empty\">Todavía no hay reseñas.</p>
      {% else %}
        {% for review in reviews %}
          <article class=\"resenia-card\">
            <header class=\"resenia-card__header\">
              <img
                class=\"resenia-card__avatar\"
                src=\"/assets/img/hero-bg.webp\"
                alt=\"Avatar de {{ review.username }}\">
              <div class=\"resenia-card__meta\">
                <span class=\"resenia-card__usuario\">{{ review.username }}</span>
                <div class=\"resenia-card__estrellas\" aria-label=\"{{ review.score|round }} estrellas\">
                  {% for i in 1..5 %}
                    <span class=\"resenia-card__star {{ i <= review.score|round(0) ? 'resenia-card__star--filled' : '' }}\">★</span>
                  {% endfor %}
                </div>
              </div>
              <time class=\"resenia-card__fecha\">{{ review.created_at|date('d M Y') }}</time>
            </header>
            <p class=\"resenia-card__texto\">{{ review.body|default('') }}</p>
          </article>
        {% endfor %}
      {% endif %}
    </div>

    <aside class=\"sugeridas-col\">
      <h2 class=\"detalle-section-label\">Tal vez te interese</h2>
      <div class=\"sugeridas-lista\">
        {% for sug in suggested %}
          <a href=\"/movie?tmdb_id={{ sug.tmdb_id }}\" class=\"sugerida-item\">
            <img
              class=\"sugerida-item__poster\"
              src=\"{{ sug.poster_url|default('/assets/img/hero-bg.webp') }}\"
              alt=\"Portada de {{ sug.title }}\">
            <span class=\"sugerida-item__titulo\">{{ sug.title }}</span>
          </a>
        {% endfor %}
      </div>
    </aside>
  </section>

</main>

{% include 'partials/footer.html.twig' %}
", "pages/movieDetails.html.twig", "/var/www/html/views/pages/movieDetails.html.twig");
    }
}
