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

/* namespace.twig */
class __TwigTemplate_19ead12e1ca9057c98980b578f13c33f extends Template
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
            'page_id' => [$this, 'block_page_id'],
            'below_menu' => [$this, 'block_below_menu'],
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
        $macros["__internal_parse_12"] = $this->macros["__internal_parse_12"] = $this->loadTemplate("macros.twig", "namespace.twig", 2)->unwrap();
        // line 1
        $this->parent = $this->loadTemplate("layout/layout.twig", "namespace.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        ((((isset($context["namespace"]) || array_key_exists("namespace", $context) ? $context["namespace"] : (function () { throw new RuntimeError('Variable "namespace" does not exist.', 3, $this->source); })()) == "")) ? (yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Doctum\Tree::getGlobalNamespaceName(), "html", null, true)) : (yield (isset($context["namespace"]) || array_key_exists("namespace", $context) ? $context["namespace"] : (function () { throw new RuntimeError('Variable "namespace" does not exist.', 3, $this->source); })())));
        yield " | ";
        yield from $this->yieldParentBlock("title", $context, $blocks);
        return; yield '';
    }

    // line 4
    public function block_body_class($context, array $blocks = [])
    {
        $macros = $this->macros;
        yield "namespace";
        return; yield '';
    }

    // line 5
    public function block_page_id($context, array $blocks = [])
    {
        $macros = $this->macros;
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(("namespace:" . Twig\Extension\CoreExtension::replace(((((isset($context["namespace"]) || array_key_exists("namespace", $context) ? $context["namespace"] : (function () { throw new RuntimeError('Variable "namespace" does not exist.', 5, $this->source); })()) == "")) ? (Doctum\Tree::getGlobalNamespacePageName()) : ((isset($context["namespace"]) || array_key_exists("namespace", $context) ? $context["namespace"] : (function () { throw new RuntimeError('Variable "namespace" does not exist.', 5, $this->source); })()))), ["\\" => "_"])), "html", null, true);
        return; yield '';
    }

    // line 7
    public function block_below_menu($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 8
        yield "    <div class=\"namespace-breadcrumbs\">
        <ol class=\"breadcrumb\">
            <li><span class=\"label label-default\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Namespace");
        // line 10
        yield "</span></li>
            ";
        // line 11
        yield CoreExtension::callMacro($macros["__internal_parse_12"], "macro_breadcrumbs", [(isset($context["namespace"]) || array_key_exists("namespace", $context) ? $context["namespace"] : (function () { throw new RuntimeError('Variable "namespace" does not exist.', 11, $this->source); })())], 11, $context, $this->getSourceContext());
        yield "
        </ol>
    </div>
";
        return; yield '';
    }

    // line 16
    public function block_page_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 17
        yield "
    <div class=\"page-header\">
        <h1>";
        // line 19
        ((((isset($context["namespace"]) || array_key_exists("namespace", $context) ? $context["namespace"] : (function () { throw new RuntimeError('Variable "namespace" does not exist.', 19, $this->source); })()) == "")) ? (yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Doctum\Tree::getGlobalNamespaceName(), "html", null, true)) : (yield (isset($context["namespace"]) || array_key_exists("namespace", $context) ? $context["namespace"] : (function () { throw new RuntimeError('Variable "namespace" does not exist.', 19, $this->source); })())));
        yield "</h1>
    </div>

    ";
        // line 22
        if ((isset($context["subnamespaces"]) || array_key_exists("subnamespaces", $context) ? $context["subnamespaces"] : (function () { throw new RuntimeError('Variable "subnamespaces" does not exist.', 22, $this->source); })())) {
            // line 23
            yield "        <h2>";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Namespaces");
            yield "</h2>
        <div class=\"namespace-list\">
            ";
            // line 25
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable((isset($context["subnamespaces"]) || array_key_exists("subnamespaces", $context) ? $context["subnamespaces"] : (function () { throw new RuntimeError('Variable "subnamespaces" does not exist.', 25, $this->source); })()));
            foreach ($context['_seq'] as $context["_key"] => $context["ns"]) {
                yield CoreExtension::callMacro($macros["__internal_parse_12"], "macro_namespace_link", [$context["ns"]], 25, $context, $this->getSourceContext());
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['ns'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 26
            yield "        </div>
    ";
        }
        // line 28
        yield "
    ";
        // line 29
        if ((isset($context["classes"]) || array_key_exists("classes", $context) ? $context["classes"] : (function () { throw new RuntimeError('Variable "classes" does not exist.', 29, $this->source); })())) {
            // line 30
            yield "        <h2>";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Classes");
            yield "</h2>";
            // line 31
            yield CoreExtension::callMacro($macros["__internal_parse_12"], "macro_render_classes", [(isset($context["classes"]) || array_key_exists("classes", $context) ? $context["classes"] : (function () { throw new RuntimeError('Variable "classes" does not exist.', 31, $this->source); })())], 31, $context, $this->getSourceContext());
        }
        // line 33
        yield "
    ";
        // line 34
        if ((isset($context["interfaces"]) || array_key_exists("interfaces", $context) ? $context["interfaces"] : (function () { throw new RuntimeError('Variable "interfaces" does not exist.', 34, $this->source); })())) {
            // line 35
            yield "        <h2>";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Interfaces");
            yield "</h2>";
            // line 36
            yield CoreExtension::callMacro($macros["__internal_parse_12"], "macro_render_classes", [(isset($context["interfaces"]) || array_key_exists("interfaces", $context) ? $context["interfaces"] : (function () { throw new RuntimeError('Variable "interfaces" does not exist.', 36, $this->source); })())], 36, $context, $this->getSourceContext());
        }
        // line 38
        yield "
    ";
        // line 39
        if ((isset($context["functions"]) || array_key_exists("functions", $context) ? $context["functions"] : (function () { throw new RuntimeError('Variable "functions" does not exist.', 39, $this->source); })())) {
            // line 40
            yield "        <h2>";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Functions");
            yield "</h2>

        <div class=\"container-fluid underlined\">
            ";
            // line 43
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable((isset($context["functions"]) || array_key_exists("functions", $context) ? $context["functions"] : (function () { throw new RuntimeError('Variable "functions" does not exist.', 43, $this->source); })()));
            foreach ($context['_seq'] as $context["_key"] => $context["function"]) {
                // line 44
                yield "                <div class=\"row\" id=\"function_";
                yield CoreExtension::getAttribute($this->env, $this->source, $context["function"], "name", [], "any", false, false, false, 44);
                yield "\">
                    <div class=\"col-md-2 type\">
                        ";
                // line 46
                if (CoreExtension::getAttribute($this->env, $this->source, $context["function"], "isStatic", [], "method", false, false, false, 46)) {
                    yield "static";
                }
                // line 47
                yield "                        ";
                if (CoreExtension::getAttribute($this->env, $this->source, $context["function"], "isProtected", [], "method", false, false, false, 47)) {
                    yield "protected";
                }
                // line 48
                yield "                        ";
                if (CoreExtension::getAttribute($this->env, $this->source, $context["function"], "isPrivate", [], "method", false, false, false, 48)) {
                    yield "private";
                }
                // line 49
                yield "                        ";
                yield CoreExtension::callMacro($macros["__internal_parse_12"], "macro_hint_link", [CoreExtension::getAttribute($this->env, $this->source, $context["function"], "hint", [], "any", false, false, false, 49)], 49, $context, $this->getSourceContext());
                yield "
                    </div>
                    <div class=\"col-md-8\">
                        ";
                // line 52
                yield CoreExtension::getAttribute($this->env, $this->source, $context["function"], "name", [], "any", false, false, false, 52);
                // line 53
                yield CoreExtension::callMacro($macros["__internal_parse_12"], "macro_function_parameters_signature", [$context["function"]], 53, $context, $this->getSourceContext());
                // line 54
                yield "<br>
                        ";
                // line 55
                if (CoreExtension::getAttribute($this->env, $this->source, $context["function"], "isInternal", [], "method", false, false, false, 55)) {
                    yield "<span class=\"label label-warning\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("internal");
                    yield "</span>";
                }
                // line 56
                yield "                        ";
                if (CoreExtension::getAttribute($this->env, $this->source, $context["function"], "isDeprecated", [], "method", false, false, false, 56)) {
                    yield "<span class=\"label label-danger\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("deprecated");
                    yield "</span>";
                }
                // line 57
                yield "                        ";
                if (CoreExtension::getAttribute($this->env, $this->source, $context["function"], "hasSince", [], "method", false, false, false, 57)) {
                    // line 58
                    yield "                            <i>";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(\Wdes\phpI18nL10n\Launcher::gettext("Since:"), "html", null, true);
                    yield " ";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["function"], "getSince", [], "method", false, false, false, 58), "html", null, true);
                    yield "</i>
                            <br>
                        ";
                }
                // line 61
                yield "                        ";
                if ( !CoreExtension::getAttribute($this->env, $this->source, $context["function"], "shortdesc", [], "any", false, false, false, 61)) {
                    // line 62
                    yield "                            <p class=\"no-description\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("No description");
                    yield "</p>
                        ";
                } else {
                    // line 64
                    yield "                            <p>";
                    yield $this->extensions['Doctum\Renderer\TwigExtension']->markdownToHtml($this->extensions['Doctum\Renderer\TwigExtension']->parseDesc(CoreExtension::getAttribute($this->env, $this->source, $context["function"], "shortdesc", [], "any", false, false, false, 64), $context["function"]));
                    yield "</p>";
                }
                // line 66
                yield "                    </div>
                    <div class=\"col-md-2\">
                        <div class=\"location\">";
                // line 69
                if ( !(CoreExtension::getAttribute($this->env, $this->source, $context["function"], "namespace", [], "any", false, false, false, 69) === (isset($context["namespace"]) || array_key_exists("namespace", $context) ? $context["namespace"] : (function () { throw new RuntimeError('Variable "namespace" does not exist.', 69, $this->source); })()))) {
                    // line 70
                    yield "<em><a href=\"";
                    yield $this->extensions['Doctum\Renderer\TwigExtension']->pathForFunction($context, $context["function"]);
                    yield "\">";
                    yield $context["function"];
                    yield "</a></em>";
                }
                // line 71
                yield CoreExtension::callMacro($macros["__internal_parse_12"], "macro_method_source_link", [$context["function"]], 71, $context, $this->getSourceContext());
                yield "
                        </div>
                    </div>
                </div>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['function'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 76
            yield "        </div>
    ";
        }
        // line 78
        yield "
    ";
        // line 79
        if ((isset($context["exceptions"]) || array_key_exists("exceptions", $context) ? $context["exceptions"] : (function () { throw new RuntimeError('Variable "exceptions" does not exist.', 79, $this->source); })())) {
            // line 80
            yield "        <h2>";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Exceptions");
            yield "</h2>";
            // line 81
            yield CoreExtension::callMacro($macros["__internal_parse_12"], "macro_render_classes", [(isset($context["exceptions"]) || array_key_exists("exceptions", $context) ? $context["exceptions"] : (function () { throw new RuntimeError('Variable "exceptions" does not exist.', 81, $this->source); })())], 81, $context, $this->getSourceContext());
        }
        // line 83
        yield "
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "namespace.twig";
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
        return array (  289 => 83,  286 => 81,  282 => 80,  280 => 79,  277 => 78,  273 => 76,  262 => 71,  255 => 70,  253 => 69,  249 => 66,  244 => 64,  238 => 62,  235 => 61,  226 => 58,  223 => 57,  216 => 56,  210 => 55,  207 => 54,  205 => 53,  203 => 52,  196 => 49,  191 => 48,  186 => 47,  182 => 46,  176 => 44,  172 => 43,  165 => 40,  163 => 39,  160 => 38,  157 => 36,  153 => 35,  151 => 34,  148 => 33,  145 => 31,  141 => 30,  139 => 29,  136 => 28,  132 => 26,  123 => 25,  117 => 23,  115 => 22,  109 => 19,  105 => 17,  101 => 16,  92 => 11,  89 => 10,  84 => 8,  80 => 7,  72 => 5,  64 => 4,  54 => 3,  49 => 1,  47 => 2,  40 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"layout/layout.twig\" %}
{% from \"macros.twig\" import breadcrumbs, render_classes, class_link, namespace_link, hint_link, deprecated, function_parameters_signature, method_source_link %}
{% block title %}{{ namespace == '' ? global_namespace_name() : namespace|raw }} | {{ parent() }}{% endblock %}
{% block body_class 'namespace' %}
{% block page_id 'namespace:' ~ ((namespace == '' ? global_namespace_page_name() : namespace)|replace({'\\\\': '_'})) %}

{% block below_menu %}
    <div class=\"namespace-breadcrumbs\">
        <ol class=\"breadcrumb\">
            <li><span class=\"label label-default\">{% trans 'Namespace' %}</span></li>
            {{ breadcrumbs(namespace) }}
        </ol>
    </div>
{% endblock %}

{% block page_content %}

    <div class=\"page-header\">
        <h1>{{ namespace == '' ? global_namespace_name() : namespace|raw }}</h1>
    </div>

    {% if subnamespaces %}
        <h2>{% trans 'Namespaces' %}</h2>
        <div class=\"namespace-list\">
            {% for ns in subnamespaces %}{{ namespace_link(ns) }}{% endfor %}
        </div>
    {% endif %}

    {% if classes %}
        <h2>{% trans 'Classes' %}</h2>
        {{- render_classes(classes) -}}
    {% endif %}

    {% if interfaces %}
        <h2>{% trans 'Interfaces' %}</h2>
        {{- render_classes(interfaces) -}}
    {% endif %}

    {% if functions %}
        <h2>{% trans 'Functions' %}</h2>

        <div class=\"container-fluid underlined\">
            {% for function in functions %}
                <div class=\"row\" id=\"function_{{ function.name|raw }}\">
                    <div class=\"col-md-2 type\">
                        {% if function.isStatic() %}static{% endif %}
                        {% if function.isProtected() %}protected{% endif %}
                        {% if function.isPrivate() %}private{% endif %}
                        {{ hint_link(function.hint) }}
                    </div>
                    <div class=\"col-md-8\">
                        {{ function.name|raw }}
                        {{- function_parameters_signature(function) -}}
                        <br>
                        {% if function.isInternal() %}<span class=\"label label-warning\">{% trans 'internal' %}</span>{% endif %}
                        {% if function.isDeprecated() %}<span class=\"label label-danger\">{% trans 'deprecated' %}</span>{% endif %}
                        {% if function.hasSince() %}
                            <i>{{ 'Since:'|trans }} {{ function.getSince() }}</i>
                            <br>
                        {% endif %}
                        {% if not function.shortdesc %}
                            <p class=\"no-description\">{% trans 'No description' %}</p>
                        {% else %}
                            <p>{{ function.shortdesc|desc(function)|md_to_html }}</p>
                        {%- endif %}
                    </div>
                    <div class=\"col-md-2\">
                        <div class=\"location\">
                        {%- if function.namespace is not same as(namespace) -%}
                            <em><a href=\"{{ function_path(function) }}\">{{ function|raw }}</a></em>
                        {%- endif -%}{{ method_source_link(function) }}
                        </div>
                    </div>
                </div>
            {% endfor %}
        </div>
    {% endif %}

    {% if exceptions %}
        <h2>{% trans 'Exceptions' %}</h2>
        {{- render_classes(exceptions) -}}
    {% endif %}

{% endblock %}
", "namespace.twig", "/home/santi/backapp/vendor/code-lts/doctum/src/Resources/themes/default/namespace.twig");
    }
}
