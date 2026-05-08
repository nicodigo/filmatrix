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

/* partials/footer.html.twig */
class __TwigTemplate_8bea62a5d0803d6498901cddef14ef41 extends Template
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
        yield "<footer class=\"site-footer\">
  <nav class=\"footer-nav\">
    <a href=\"/acerca-de-nosotros\">Acerca de nosotros</a>
    <a href=\"/contacto\">Contacto</a>
  </nav>
  <ul class=\"footer-social\">
    <li><a href=\"#\" aria-label=\"Twitter\"><img src=\"\" alt=\"Twitter\" class=\"social-icon twitter\"></a></li>
    <li><a href=\"#\" aria-label=\"Instagram\"><img src=\"\" alt=\"Instagram\" class=\"social-icon instagram\"></a></li>
    <li><a href=\"#\" aria-label=\"YouTube\"><img src=\"\" alt=\"YouTube\" class=\"social-icon youtube\"></a></li>
  </ul>
  <p class=\"footer-copy\">© 2026 Filmatrix. Todos los derechos reservados.</p>
</footer>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "partials/footer.html.twig";
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
        return new Source("<footer class=\"site-footer\">
  <nav class=\"footer-nav\">
    <a href=\"/acerca-de-nosotros\">Acerca de nosotros</a>
    <a href=\"/contacto\">Contacto</a>
  </nav>
  <ul class=\"footer-social\">
    <li><a href=\"#\" aria-label=\"Twitter\"><img src=\"\" alt=\"Twitter\" class=\"social-icon twitter\"></a></li>
    <li><a href=\"#\" aria-label=\"Instagram\"><img src=\"\" alt=\"Instagram\" class=\"social-icon instagram\"></a></li>
    <li><a href=\"#\" aria-label=\"YouTube\"><img src=\"\" alt=\"YouTube\" class=\"social-icon youtube\"></a></li>
  </ul>
  <p class=\"footer-copy\">© 2026 Filmatrix. Todos los derechos reservados.</p>
</footer>
", "partials/footer.html.twig", "/var/www/html/views/partials/footer.html.twig");
    }
}
