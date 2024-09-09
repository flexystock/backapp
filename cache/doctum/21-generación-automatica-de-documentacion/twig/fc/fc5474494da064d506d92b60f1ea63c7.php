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

/* classes.twig */
class __TwigTemplate_c45a5d93e6f8fdab46d4da4f551012d8 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'body_class' => [$this, 'block_body_class'],
            'page_content' => [$this, 'block_page_content'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "layout/layout.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 2
        $macros["__internal_parse_8"] = $this->macros["__internal_parse_8"] = $this->loadTemplate("macros.twig", "classes.twig", 2)->unwrap();
        // line 1
        $this->parent = $this->loadTemplate("layout/layout.twig", "classes.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("All Classes");
        yield " | ";
        yield from $this->yieldParentBlock("title", $context, $blocks);
        return; yield '';
    }

    // line 4
    public function block_body_class($context, array $blocks = [])
    {
        $macros = $this->macros;
        yield "classes";
        return; yield '';
    }

    // line 6
    public function block_page_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 7
        yield "    <div class=\"page-header\">
        <h1>";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Classes");
        // line 8
        yield "</h1>
    </div>

    ";
        // line 11
        yield CoreExtension::callMacro($macros["__internal_parse_8"], "macro_render_classes", [(isset($context["classes"]) || array_key_exists("classes", $context) ? $context["classes"] : (function () { throw new RuntimeError('Variable "classes" does not exist.', 11, $this->source); })())], 11, $context, $this->getSourceContext());
        yield "
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "classes.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable()
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  83 => 11,  78 => 8,  74 => 7,  70 => 6,  62 => 4,  52 => 3,  47 => 1,  45 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"layout/layout.twig\" %}
{% from \"macros.twig\" import render_classes %}
{% block title %}{% trans 'All Classes' %} | {{ parent() }}{% endblock %}
{% block body_class 'classes' %}

{% block page_content %}
    <div class=\"page-header\">
        <h1>{% trans 'Classes' %}</h1>
    </div>

    {{ render_classes(classes) }}
{% endblock %}
", "classes.twig", "/home/santi/backapp/vendor/code-lts/doctum/src/Resources/themes/default/classes.twig");
    }
}
