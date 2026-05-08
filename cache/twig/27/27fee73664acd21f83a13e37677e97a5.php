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

/* macros/movie-cards.html.twig */
class __TwigTemplate_067ac3ffc8823f66a95e9d59c0ef8c95 extends Template
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
        yield from [];
    }

    // line 2
    public function macro_movieCard($movie = null, ...$varargs): string|Markup
    {
        $macros = $this->macros;
        $context = [
            "movie" => $movie,
            "varargs" => $varargs,
        ] + $this->env->getGlobals();

        $blocks = [];

        return ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 3
            yield "  <article class=\"movie-card\">
    <a href=\"/movie?tmdb_id=";
            // line 4
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["movie"] ?? null), "tmdb_id", [], "any", false, false, false, 4), "html", null, true);
            yield "\" class=\"movie-card__link\">
      <img src=\"";
            // line 5
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["movie"] ?? null), "poster_url", [], "any", false, false, false, 5), "html", null, true);
            yield "\" alt=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["movie"] ?? null), "title", [], "any", false, false, false, 5), "html", null, true);
            yield "\">
      <div class=\"movie-card__overlay\">
        <p class=\"movie-card__title\">";
            // line 7
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["movie"] ?? null), "title", [], "any", false, false, false, 7), "html", null, true);
            yield "</p>
        <p class=\"movie-card__score\">";
            // line 8
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["movie"] ?? null), "avg_score", [], "any", true, true, false, 8)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["movie"] ?? null), "avg_score", [], "any", false, false, false, 8), "S/P")) : ("S/P")), "html", null, true);
            yield "</p>
      </div>
    </a>
  </article>
";
            yield from [];
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "macros/movie-cards.html.twig";
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
        return array (  75 => 8,  71 => 7,  64 => 5,  60 => 4,  57 => 3,  45 => 2,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{# views/macros/movie-card.html.twig #}
{% macro movieCard(movie) %}
  <article class=\"movie-card\">
    <a href=\"/movie?tmdb_id={{ movie.tmdb_id }}\" class=\"movie-card__link\">
      <img src=\"{{ movie.poster_url }}\" alt=\"{{ movie.title }}\">
      <div class=\"movie-card__overlay\">
        <p class=\"movie-card__title\">{{ movie.title }}</p>
        <p class=\"movie-card__score\">{{ movie.avg_score|default('S/P') }}</p>
      </div>
    </a>
  </article>
{% endmacro %}
", "macros/movie-cards.html.twig", "/var/www/html/views/macros/movie-cards.html.twig");
    }
}
