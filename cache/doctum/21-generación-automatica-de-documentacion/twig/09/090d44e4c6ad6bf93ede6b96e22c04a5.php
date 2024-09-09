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

/* traits.twig */
class __TwigTemplate_62963fcdf9048f5f706d84eeb5272e11 extends Template
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
        $macros["__internal_parse_10"] = $this->macros["__internal_parse_10"] = $this->loadTemplate("macros.twig", "traits.twig", 2)->unwrap();
        // line 1
        $this->parent = $this->loadTemplate("layout/layout.twig", "traits.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Traits");
        yield " | ";
        yield from $this->yieldParentBlock("title", $context, $blocks);
        return; yield '';
    }

    // line 4
    public function block_body_class($context, array $blocks = [])
    {
        $macros = $this->macros;
        yield "traits";
        return; yield '';
    }

    // line 6
    public function block_page_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 7
        yield "    <div class=\"page-header\">
        <h1>";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Traits");
        // line 8
        yield "</h1>
    </div>

    <div class=\"container-fluid underlined\">
        ";
        // line 12
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["classes"]) || array_key_exists("classes", $context) ? $context["classes"] : (function () { throw new RuntimeError('Variable "classes" does not exist.', 12, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["class"]) {
            // line 13
            yield "            ";
            if (CoreExtension::getAttribute($this->env, $this->source, $context["class"], "trait", [], "any", false, false, false, 13)) {
                // line 14
                yield "                <div class=\"row\">
                    <div class=\"col-md-6\">
                        ";
                // line 16
                yield CoreExtension::callMacro($macros["__internal_parse_10"], "macro_class_link", [$context["class"], true], 16, $context, $this->getSourceContext());
                yield "
                    </div>
                    <div class=\"col-md-6\">
                        ";
                // line 19
                yield $this->extensions['Doctum\Renderer\TwigExtension']->markdownToHtml($this->extensions['Doctum\Renderer\TwigExtension']->parseDesc(CoreExtension::getAttribute($this->env, $this->source, $context["class"], "shortdesc", [], "any", false, false, false, 19), $context["class"]));
                yield "
                    </div>
                </div>
            ";
            }
            // line 23
            yield "        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['class'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 24
        yield "    </div>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "traits.twig";
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
        return array (  114 => 24,  108 => 23,  101 => 19,  95 => 16,  91 => 14,  88 => 13,  84 => 12,  78 => 8,  74 => 7,  70 => 6,  62 => 4,  52 => 3,  47 => 1,  45 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"layout/layout.twig\" %}
{% from \"macros.twig\" import class_link %}
{% block title %}{% trans 'Traits' %} | {{ parent() }}{% endblock %}
{% block body_class 'traits' %}

{% block page_content %}
    <div class=\"page-header\">
        <h1>{% trans 'Traits' %}</h1>
    </div>

    <div class=\"container-fluid underlined\">
        {% for class in classes %}
            {% if class.trait %}
                <div class=\"row\">
                    <div class=\"col-md-6\">
                        {{ class_link(class, true) }}
                    </div>
                    <div class=\"col-md-6\">
                        {{ class.shortdesc|desc(class)|md_to_html }}
                    </div>
                </div>
            {% endif %}
        {% endfor %}
    </div>
{% endblock %}
", "traits.twig", "/home/santi/backapp/vendor/code-lts/doctum/src/Resources/themes/default/traits.twig");
    }
}
