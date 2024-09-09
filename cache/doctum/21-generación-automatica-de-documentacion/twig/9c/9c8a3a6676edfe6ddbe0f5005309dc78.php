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

/* doc-index.twig */
class __TwigTemplate_39cc4e53a83059e2e219379f048173f0 extends Template
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
        $macros["__internal_parse_7"] = $this->macros["__internal_parse_7"] = $this->loadTemplate("macros.twig", "doc-index.twig", 2)->unwrap();
        // line 1
        $this->parent = $this->loadTemplate("layout/layout.twig", "doc-index.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Index");
        yield " | ";
        yield from $this->yieldParentBlock("title", $context, $blocks);
        return; yield '';
    }

    // line 4
    public function block_body_class($context, array $blocks = [])
    {
        $macros = $this->macros;
        yield "doc-index";
        return; yield '';
    }

    // line 6
    public function block_page_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 7
        yield "
    <div class=\"page-header\">
        <h1>";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Index");
        // line 9
        yield "</h1>
    </div>

    <ul class=\"pagination\">
        ";
        // line 13
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(range("A", "Z"));
        foreach ($context['_seq'] as $context["_key"] => $context["letter"]) {
            // line 14
            yield "            ";
            if ((CoreExtension::getAttribute($this->env, $this->source, ($context["items"] ?? null), $context["letter"], [], "array", true, true, false, 14) && (Twig\Extension\CoreExtension::length($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, (isset($context["items"]) || array_key_exists("items", $context) ? $context["items"] : (function () { throw new RuntimeError('Variable "items" does not exist.', 14, $this->source); })()), $context["letter"], [], "array", false, false, false, 14)) > 1))) {
                // line 15
                yield "                <li><a href=\"#letter";
                yield $context["letter"];
                yield "\">";
                yield $context["letter"];
                yield "</a></li>
            ";
            } else {
                // line 17
                yield "                <li class=\"disabled\"><a href=\"#letter";
                yield $context["letter"];
                yield "\">";
                yield $context["letter"];
                yield "</a></li>
            ";
            }
            // line 19
            yield "        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['letter'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 20
        yield "    </ul>

    ";
        // line 22
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["items"]) || array_key_exists("items", $context) ? $context["items"] : (function () { throw new RuntimeError('Variable "items" does not exist.', 22, $this->source); })()));
        foreach ($context['_seq'] as $context["letter"] => $context["elements"]) {
            // line 23
            yield "<h2 id=\"letter";
            yield $context["letter"];
            yield "\">";
            yield $context["letter"];
            yield "</h2>
        <dl id=\"index";
            // line 24
            yield $context["letter"];
            yield "\">";
            // line 25
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable($context["elements"]);
            foreach ($context['_seq'] as $context["_key"] => $context["element"]) {
                // line 26
                $context["type"] = CoreExtension::getAttribute($this->env, $this->source, $context["element"], 0, [], "array", false, false, false, 26);
                // line 27
                $context["value"] = CoreExtension::getAttribute($this->env, $this->source, $context["element"], 1, [], "array", false, false, false, 27);
                // line 28
                if (("class" == (isset($context["type"]) || array_key_exists("type", $context) ? $context["type"] : (function () { throw new RuntimeError('Variable "type" does not exist.', 28, $this->source); })()))) {
                    // line 29
                    yield "<dt>";
                    yield CoreExtension::callMacro($macros["__internal_parse_7"], "macro_class_link", [(isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 29, $this->source); })())], 29, $context, $this->getSourceContext());
                    if ((isset($context["has_namespaces"]) || array_key_exists("has_namespaces", $context) ? $context["has_namespaces"] : (function () { throw new RuntimeError('Variable "has_namespaces" does not exist.', 29, $this->source); })())) {
                        yield " &mdash; <em>";
                        yield Twig\Extension\CoreExtension::sprintf(\Wdes\phpI18nL10n\Launcher::gettext("Class in namespace %s"), CoreExtension::callMacro($macros["__internal_parse_7"], "macro_namespace_link", [CoreExtension::getAttribute($this->env, $this->source,                         // line 30
(isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 30, $this->source); })()), "namespace", [], "any", false, false, false, 30)], 30, $context, $this->getSourceContext()));
                    }
                    // line 31
                    yield "</em></dt>
                    <dd>";
                    // line 32
                    yield $this->extensions['Doctum\Renderer\TwigExtension']->markdownToHtml($this->extensions['Doctum\Renderer\TwigExtension']->parseDesc(CoreExtension::getAttribute($this->env, $this->source, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 32, $this->source); })()), "shortdesc", [], "any", false, false, false, 32), (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 32, $this->source); })())));
                    yield "</dd>";
                } elseif (("method" ==                 // line 33
(isset($context["type"]) || array_key_exists("type", $context) ? $context["type"] : (function () { throw new RuntimeError('Variable "type" does not exist.', 33, $this->source); })()))) {
                    // line 34
                    yield "<dt>";
                    yield CoreExtension::callMacro($macros["__internal_parse_7"], "macro_method_link", [(isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 34, $this->source); })())], 34, $context, $this->getSourceContext());
                    yield "() &mdash; <em>";
                    yield Twig\Extension\CoreExtension::sprintf(\Wdes\phpI18nL10n\Launcher::gettext("Method in class %s"), CoreExtension::callMacro($macros["__internal_parse_7"], "macro_class_link", [CoreExtension::getAttribute($this->env, $this->source,                     // line 35
(isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 35, $this->source); })()), "class", [], "any", false, false, false, 35)], 35, $context, $this->getSourceContext()));
                    // line 36
                    yield "</em></dt>
                    <dd>";
                    // line 37
                    yield $this->extensions['Doctum\Renderer\TwigExtension']->markdownToHtml($this->extensions['Doctum\Renderer\TwigExtension']->parseDesc(CoreExtension::getAttribute($this->env, $this->source, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 37, $this->source); })()), "shortdesc", [], "any", false, false, false, 37), CoreExtension::getAttribute($this->env, $this->source, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 37, $this->source); })()), "class", [], "any", false, false, false, 37)));
                    yield "</dd>";
                } elseif (("property" ==                 // line 38
(isset($context["type"]) || array_key_exists("type", $context) ? $context["type"] : (function () { throw new RuntimeError('Variable "type" does not exist.', 38, $this->source); })()))) {
                    // line 39
                    yield "<dt>\$";
                    yield CoreExtension::callMacro($macros["__internal_parse_7"], "macro_property_link", [(isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 39, $this->source); })())], 39, $context, $this->getSourceContext());
                    yield " &mdash; <em>";
                    yield Twig\Extension\CoreExtension::sprintf(\Wdes\phpI18nL10n\Launcher::gettext("Property in class %s"), CoreExtension::callMacro($macros["__internal_parse_7"], "macro_class_link", [CoreExtension::getAttribute($this->env, $this->source,                     // line 40
(isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 40, $this->source); })()), "class", [], "any", false, false, false, 40)], 40, $context, $this->getSourceContext()));
                    // line 41
                    yield "</em></dt>
                    <dd>";
                    // line 42
                    yield $this->extensions['Doctum\Renderer\TwigExtension']->markdownToHtml($this->extensions['Doctum\Renderer\TwigExtension']->parseDesc(CoreExtension::getAttribute($this->env, $this->source, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 42, $this->source); })()), "shortdesc", [], "any", false, false, false, 42), CoreExtension::getAttribute($this->env, $this->source, (isset($context["value"]) || array_key_exists("value", $context) ? $context["value"] : (function () { throw new RuntimeError('Variable "value" does not exist.', 42, $this->source); })()), "class", [], "any", false, false, false, 42)));
                    yield "</dd>";
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['element'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 45
            yield "        </dl>";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['letter'], $context['elements'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "doc-index.twig";
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
        return array (  189 => 45,  181 => 42,  178 => 41,  176 => 40,  172 => 39,  170 => 38,  167 => 37,  164 => 36,  162 => 35,  158 => 34,  156 => 33,  153 => 32,  150 => 31,  147 => 30,  142 => 29,  140 => 28,  138 => 27,  136 => 26,  132 => 25,  129 => 24,  122 => 23,  118 => 22,  114 => 20,  108 => 19,  100 => 17,  92 => 15,  89 => 14,  85 => 13,  79 => 9,  74 => 7,  70 => 6,  62 => 4,  52 => 3,  47 => 1,  45 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"layout/layout.twig\" %}
{% from \"macros.twig\" import class_link, namespace_link, method_link, property_link %}
{% block title %}{% trans 'Index' %} | {{ parent() }}{% endblock %}
{% block body_class 'doc-index' %}

{% block page_content %}

    <div class=\"page-header\">
        <h1>{% trans 'Index' %}</h1>
    </div>

    <ul class=\"pagination\">
        {% for letter in 'A'..'Z' %}
            {% if items[letter] is defined and items[letter]|length > 1 %}
                <li><a href=\"#letter{{ letter|raw }}\">{{ letter|raw }}</a></li>
            {% else %}
                <li class=\"disabled\"><a href=\"#letter{{ letter|raw }}\">{{ letter|raw }}</a></li>
            {% endif %}
        {% endfor %}
    </ul>

    {% for letter, elements in items -%}
        <h2 id=\"letter{{ letter|raw }}\">{{ letter|raw }}</h2>
        <dl id=\"index{{ letter|raw }}\">
            {%- for element in elements %}
                {%- set type = element[0] %}
                {%- set value = element[1] %}
                {%- if 'class' == type -%}
                    <dt>{{ class_link(value) }}{% if has_namespaces %} &mdash; <em>{{'Class in namespace %s'|trans|format(
                        namespace_link(value.namespace)
                    )|raw}}{% endif %}</em></dt>
                    <dd>{{ value.shortdesc|desc(value)|md_to_html }}</dd>
                {%- elseif 'method' == type -%}
                    <dt>{{ method_link(value) }}() &mdash; <em>{{ 'Method in class %s'|trans|format(
                        class_link(value.class)
                    )|raw }}</em></dt>
                    <dd>{{ value.shortdesc|desc(value.class)|md_to_html }}</dd>
                {%- elseif 'property' == type -%}
                    <dt>\${{ property_link(value) }} &mdash; <em>{{ 'Property in class %s'|trans|format(
                        class_link(value.class)
                    )|raw}}</em></dt>
                    <dd>{{ value.shortdesc|desc(value.class)|md_to_html }}</dd>
                {%- endif %}
            {%- endfor %}
        </dl>
    {%- endfor %}
{% endblock %}
", "doc-index.twig", "/home/santi/backapp/vendor/code-lts/doctum/src/Resources/themes/default/doc-index.twig");
    }
}
