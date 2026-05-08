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

/* partials/header.html.twig */
class __TwigTemplate_44ccf31c0d59e20fb49ceb9c2ce00d3a extends Template
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
        yield "<header class=\"site-header\">
  <div class=\"header-inner\">
 
    <div class=\"header-left\">
      <a href=\"/\" class=\"header-logo\">
        <img src=\"/assets/img/filmatrix_isotipo.webp\" alt=\"Filmatrix\">
      </a>
      <a href=\"/catalog\" class=\"header-catalogo-btn\">
        <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"14\" height=\"14\" viewBox=\"0 0 24 24\"
             fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\"
             stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\">
          <rect x=\"3\" y=\"3\" width=\"7\" height=\"7\"/>
          <rect x=\"14\" y=\"3\" width=\"7\" height=\"7\"/>
          <rect x=\"3\" y=\"14\" width=\"7\" height=\"7\"/>
          <rect x=\"14\" y=\"14\" width=\"7\" height=\"7\"/>
        </svg>
        Catálogo
      </a>
    </div>
 
    <div class=\"header-actions\">
      <form role=\"search\" class=\"search-form\" action=\"/catalog\" method=\"GET\">
        <input type=\"search\" name=\"q\" placeholder=\"Buscar…\" class=\"search-input\">
        <button type=\"submit\" class=\"search-submit\" aria-label=\"Buscar\">
          <svg width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\">
            <circle cx=\"11\" cy=\"11\" r=\"8\"/>
            <line x1=\"21\" y1=\"21\" x2=\"16.65\" y2=\"16.65\"/>
          </svg>
        </button>
      </form>
      <a href=\"/profile\" class=\"header-avatar\">
        <img src=\"/assets/img/user_avatar.png\" alt=\"Avatar\" width=\"32\" height=\"32\">
      </a>
      <button class=\"header-menu\" aria-label=\"Menú\">
        <svg width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\">
          <line x1=\"3\" y1=\"6\" x2=\"21\" y2=\"6\"/>
          <line x1=\"3\" y1=\"12\" x2=\"21\" y2=\"12\"/>
          <line x1=\"3\" y1=\"18\" x2=\"21\" y2=\"18\"/>
        </svg>
      </button>
    </div>
 
  </div>
</header>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "partials/header.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<header class=\"site-header\">
  <div class=\"header-inner\">
 
    <div class=\"header-left\">
      <a href=\"/\" class=\"header-logo\">
        <img src=\"/assets/img/filmatrix_isotipo.webp\" alt=\"Filmatrix\">
      </a>
      <a href=\"/catalog\" class=\"header-catalogo-btn\">
        <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"14\" height=\"14\" viewBox=\"0 0 24 24\"
             fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\"
             stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\">
          <rect x=\"3\" y=\"3\" width=\"7\" height=\"7\"/>
          <rect x=\"14\" y=\"3\" width=\"7\" height=\"7\"/>
          <rect x=\"3\" y=\"14\" width=\"7\" height=\"7\"/>
          <rect x=\"14\" y=\"14\" width=\"7\" height=\"7\"/>
        </svg>
        Catálogo
      </a>
    </div>
 
    <div class=\"header-actions\">
      <form role=\"search\" class=\"search-form\" action=\"/catalog\" method=\"GET\">
        <input type=\"search\" name=\"q\" placeholder=\"Buscar…\" class=\"search-input\">
        <button type=\"submit\" class=\"search-submit\" aria-label=\"Buscar\">
          <svg width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\">
            <circle cx=\"11\" cy=\"11\" r=\"8\"/>
            <line x1=\"21\" y1=\"21\" x2=\"16.65\" y2=\"16.65\"/>
          </svg>
        </button>
      </form>
      <a href=\"/profile\" class=\"header-avatar\">
        <img src=\"/assets/img/user_avatar.png\" alt=\"Avatar\" width=\"32\" height=\"32\">
      </a>
      <button class=\"header-menu\" aria-label=\"Menú\">
        <svg width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\">
          <line x1=\"3\" y1=\"6\" x2=\"21\" y2=\"6\"/>
          <line x1=\"3\" y1=\"12\" x2=\"21\" y2=\"12\"/>
          <line x1=\"3\" y1=\"18\" x2=\"21\" y2=\"18\"/>
        </svg>
      </button>
    </div>
 
  </div>
</header>
", "partials/header.html.twig", "/var/www/html/views/partials/header.html.twig");
    }
}
