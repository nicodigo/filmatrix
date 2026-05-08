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

/* pages/includes/movie-card.html.twig */
class __TwigTemplate_c773f221f18d4b455ce9469f1b72a276 extends Template
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
        yield "<article class=\"movie-card\">
  <a href=\"/movie?tmdb_id=";
        // line 2
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["movie"] ?? null), "tmdb_id", [], "any", false, false, false, 2), "html", null, true);
        yield "\" class=\"movie-card__link\">
    <img src=\"";
        // line 3
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["movie"] ?? null), "poster_url", [], "any", false, false, false, 3), "html", null, true);
        yield "\" alt=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["movie"] ?? null), "title", [], "any", false, false, false, 3), "html", null, true);
        yield "\">
    <div class=\"movie-card__overlay\">
      <p class=\"movie-card__title\">";
        // line 5
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["movie"] ?? null), "title", [], "any", false, false, false, 5), "html", null, true);
        yield "</p>
      <p class=\"movie-card__score\">";
        // line 6
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["movie"] ?? null), "avg_score", [], "any", true, true, false, 6)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["movie"] ?? null), "avg_score", [], "any", false, false, false, 6), "S/P")) : ("S/P")), "html", null, true);
        yield "</p>
    </div>
  </a>
</article>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "pages/includes/movie-card.html.twig";
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
        return array (  60 => 6,  56 => 5,  49 => 3,  45 => 2,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<article class=\"movie-card\">
  <a href=\"/movie?tmdb_id={{ movie.tmdb_id }}\" class=\"movie-card__link\">
    <img src=\"{{ movie.poster_url }}\" alt=\"{{ movie.title }}\">
    <div class=\"movie-card__overlay\">
      <p class=\"movie-card__title\">{{movie.title}}</p>
      <p class=\"movie-card__score\">{{ movie.avg_score|default('S/P') }}</p>
    </div>
  </a>
</article>
", "pages/includes/movie-card.html.twig", "/var/www/html/views/pages/includes/movie-card.html.twig");
    }
}
