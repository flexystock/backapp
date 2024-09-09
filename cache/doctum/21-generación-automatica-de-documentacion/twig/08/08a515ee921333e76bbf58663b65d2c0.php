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

/* namespaces.twig */
class __TwigTemplate_7fefae151358622cdf274c4e74e48b28 extends Template
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
        $macros["__internal_parse_0"] = $this->macros["__internal_parse_0"] = $this->loadTemplate("macros.twig", "namespaces.twig", 2)->unwrap();
        // line 1
        $this->parent = $this->loadTemplate("layout/layout.twig", "namespaces.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Namespaces");
        yield " | ";
        yield from $this->yieldParentBlock("title", $context, $blocks);
        return; yield '';
    }

    // line 4
    public function block_body_class($context, array $blocks = [])
    {
        $macros = $this->macros;
        yield "namespaces";
        return; yield '';
    }

    // line 6
    public function block_page_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 7
        yield "    <div class=\"page-header\">
        <h1>";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Namespaces");
        // line 8
        yield "</h1>
    </div>

    ";
        // line 11
        if ((isset($context["namespaces"]) || array_key_exists("namespaces", $context) ? $context["namespaces"] : (function () { throw new RuntimeError('Variable "namespaces" does not exist.', 11, $this->source); })())) {
            // line 12
            yield "        <div class=\"namespaces clearfix\">
            ";
            // line 13
            $context["last_name"] = "";
            // line 14
            yield "            ";
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable((isset($context["namespaces"]) || array_key_exists("namespaces", $context) ? $context["namespaces"] : (function () { throw new RuntimeError('Variable "namespaces" does not exist.', 14, $this->source); })()));
            foreach ($context['_seq'] as $context["_key"] => $context["namespace"]) {
                // line 15
                yield "                ";
                $context["top_level"] = Twig\Extension\CoreExtension::first($this->env->getCharset(), Twig\Extension\CoreExtension::split($this->env->getCharset(), $context["namespace"], "\\"));
                // line 16
                yield "                ";
                if (((isset($context["top_level"]) || array_key_exists("top_level", $context) ? $context["top_level"] : (function () { throw new RuntimeError('Variable "top_level" does not exist.', 16, $this->source); })()) != (isset($context["last_name"]) || array_key_exists("last_name", $context) ? $context["last_name"] : (function () { throw new RuntimeError('Variable "last_name" does not exist.', 16, $this->source); })()))) {
                    // line 17
                    yield "                    ";
                    if ((isset($context["last_name"]) || array_key_exists("last_name", $context) ? $context["last_name"] : (function () { throw new RuntimeError('Variable "last_name" does not exist.', 17, $this->source); })())) {
                        yield "</ul></div>";
                    }
                    // line 18
                    yield "                    <div class=\"namespace-container\">
                        <h2>";
                    // line 19
                    yield (isset($context["top_level"]) || array_key_exists("top_level", $context) ? $context["top_level"] : (function () { throw new RuntimeError('Variable "top_level" does not exist.', 19, $this->source); })());
                    yield "</h2>
                        <ul>
                    ";
                    // line 21
                    $context["last_name"] = (isset($context["top_level"]) || array_key_exists("top_level", $context) ? $context["top_level"] : (function () { throw new RuntimeError('Variable "top_level" does not exist.', 21, $this->source); })());
                    // line 22
                    yield "                ";
                }
                // line 23
                yield "                <li>";
                yield CoreExtension::callMacro($macros["__internal_parse_0"], "macro_namespace_link", [$context["namespace"]], 23, $context, $this->getSourceContext());
                yield "</li>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['namespace'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 25
            yield "                </ul>
            </div>
        </div>
    ";
        }
        // line 29
        yield "
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "namespaces.twig";
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
        return array (  134 => 29,  128 => 25,  119 => 23,  116 => 22,  114 => 21,  109 => 19,  106 => 18,  101 => 17,  98 => 16,  95 => 15,  90 => 14,  88 => 13,  85 => 12,  83 => 11,  78 => 8,  74 => 7,  70 => 6,  62 => 4,  52 => 3,  47 => 1,  45 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"layout/layout.twig\" %}
{% from \"macros.twig\" import namespace_link %}
{% block title %}{% trans 'Namespaces' %} | {{ parent() }}{% endblock %}
{% block body_class 'namespaces' %}

{% block page_content %}
    <div class=\"page-header\">
        <h1>{% trans 'Namespaces' %}</h1>
    </div>

    {% if namespaces %}
        <div class=\"namespaces clearfix\">
            {% set last_name = '' %}
            {% for namespace in namespaces %}
                {% set top_level = namespace|split('\\\\')|first %}
                {% if top_level != last_name %}
                    {% if last_name %}</ul></div>{% endif %}
                    <div class=\"namespace-container\">
                        <h2>{{ top_level|raw }}</h2>
                        <ul>
                    {% set last_name = top_level %}
                {% endif %}
                <li>{{ namespace_link(namespace) }}</li>
            {% endfor %}
                </ul>
            </div>
        </div>
    {% endif %}

{% endblock %}
", "namespaces.twig", "/home/santi/backapp/vendor/code-lts/doctum/src/Resources/themes/default/namespaces.twig");
    }
}
