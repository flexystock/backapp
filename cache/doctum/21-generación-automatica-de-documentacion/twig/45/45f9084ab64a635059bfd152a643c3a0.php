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

/* layout/layout.twig */
class __TwigTemplate_a4e7ffdef0303a1b114f86bc61002a4a extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
            'below_menu' => [$this, 'block_below_menu'],
            'page_content' => [$this, 'block_page_content'],
            'menu' => [$this, 'block_menu'],
            'leftnav' => [$this, 'block_leftnav'],
            'control_panel' => [$this, 'block_control_panel'],
            'footer' => [$this, 'block_footer'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "layout/base.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("layout/base.twig", "layout/layout.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 4
        yield "    <div id=\"content\">
        <div id=\"left-column\">
            ";
        // line 6
        yield from         $this->unwrap()->yieldBlock("control_panel", $context, $blocks);
        yield "
            ";
        // line 7
        yield from         $this->unwrap()->yieldBlock("leftnav", $context, $blocks);
        yield "
        </div>
        <div id=\"right-column\">
            ";
        // line 10
        yield from         $this->unwrap()->yieldBlock("menu", $context, $blocks);
        yield "
            ";
        // line 11
        yield from $this->unwrap()->yieldBlock('below_menu', $context, $blocks);
        // line 12
        yield "            <div id=\"page-content\">";
        // line 13
        yield from $this->unwrap()->yieldBlock('page_content', $context, $blocks);
        // line 14
        yield "</div>";
        // line 15
        yield from         $this->unwrap()->yieldBlock("footer", $context, $blocks);
        // line 16
        yield "</div>
    </div>
";
        return; yield '';
    }

    // line 11
    public function block_below_menu($context, array $blocks = [])
    {
        $macros = $this->macros;
        yield "";
        return; yield '';
    }

    // line 13
    public function block_page_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        yield "";
        return; yield '';
    }

    // line 20
    public function block_menu($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 21
        yield "    <nav id=\"site-nav\" class=\"navbar navbar-default\" role=\"navigation\">
        <div class=\"container-fluid\">
            <div class=\"navbar-header\">
                <button type=\"button\" class=\"navbar-toggle\" data-toggle=\"collapse\" data-target=\"#navbar-elements\">
                    <span class=\"sr-only\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Toggle navigation");
        // line 25
        yield "</span>
                    <span class=\"icon-bar\"></span>
                    <span class=\"icon-bar\"></span>
                    <span class=\"icon-bar\"></span>
                </button>
                <a class=\"navbar-brand\" href=\"";
        // line 30
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Doctum\Renderer\TwigExtension']->pathForStaticFile($context, "index.html"), "html", null, true);
        yield "\">";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["project"]) || array_key_exists("project", $context) ? $context["project"] : (function () { throw new RuntimeError('Variable "project" does not exist.', 30, $this->source); })()), "config", ["title"], "method", false, false, false, 30), "html", null, true);
        yield "</a>
            </div>
            <div class=\"collapse navbar-collapse\" id=\"navbar-elements\">
                <ul class=\"nav navbar-nav\">
                    ";
        // line 34
        if ((Twig\Extension\CoreExtension::length($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, (isset($context["project"]) || array_key_exists("project", $context) ? $context["project"] : (function () { throw new RuntimeError('Variable "project" does not exist.', 34, $this->source); })()), "versions", [], "any", false, false, false, 34)) > 1)) {
            // line 35
            yield "                    <li role=\"presentation\" class=\"dropdown visible-xs-block visible-sm-block\">
                        <a class=\"dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\"
                            role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Versions");
            // line 38
            yield "&nbsp;<span class=\"caret\"></span>
                        </a>
                        <ul class=\"dropdown-menu\">
                        ";
            // line 41
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["project"]) || array_key_exists("project", $context) ? $context["project"] : (function () { throw new RuntimeError('Variable "project" does not exist.', 41, $this->source); })()), "versions", [], "any", false, false, false, 41));
            foreach ($context['_seq'] as $context["_key"] => $context["version"]) {
                // line 42
                yield "<li ";
                yield ((($context["version"] == CoreExtension::getAttribute($this->env, $this->source, (isset($context["project"]) || array_key_exists("project", $context) ? $context["project"] : (function () { throw new RuntimeError('Variable "project" does not exist.', 42, $this->source); })()), "version", [], "any", false, false, false, 42))) ? ("class=\"active\"") : (""));
                yield "><a href=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Doctum\Renderer\TwigExtension']->pathForStaticFile($context, (("../" . Twig\Extension\CoreExtension::urlencode($context["version"])) . "/index.html")), "html", null, true);
                yield "\" data-version=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["version"], "html", null, true);
                yield "\">";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["version"], "longname", [], "any", false, false, false, 42), "html", null, true);
                yield "</a></li>
                        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['version'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 44
            yield "</ul>
                    </li>
                    ";
        }
        // line 47
        yield "<li><a href=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Doctum\Renderer\TwigExtension']->pathForStaticFile($context, "classes.html"), "html", null, true);
        yield "\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Classes");
        yield "</a></li>
                    ";
        // line 48
        if ((isset($context["has_namespaces"]) || array_key_exists("has_namespaces", $context) ? $context["has_namespaces"] : (function () { throw new RuntimeError('Variable "has_namespaces" does not exist.', 48, $this->source); })())) {
            // line 49
            yield "<li><a href=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Doctum\Renderer\TwigExtension']->pathForStaticFile($context, "namespaces.html"), "html", null, true);
            yield "\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Namespaces");
            yield "</a></li>
                    ";
        }
        // line 51
        yield "<li><a href=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Doctum\Renderer\TwigExtension']->pathForStaticFile($context, "interfaces.html"), "html", null, true);
        yield "\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Interfaces");
        yield "</a></li>
                    <li><a href=\"";
        // line 52
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Doctum\Renderer\TwigExtension']->pathForStaticFile($context, "traits.html"), "html", null, true);
        yield "\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Traits");
        yield "</a></li>
                    <li><a href=\"";
        // line 53
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Doctum\Renderer\TwigExtension']->pathForStaticFile($context, "doc-index.html"), "html", null, true);
        yield "\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Index");
        yield "</a></li>
                    <li><a href=\"";
        // line 54
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Doctum\Renderer\TwigExtension']->pathForStaticFile($context, "search.html"), "html", null, true);
        yield "\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Search");
        yield "</a></li>
                </ul>
            </div>
        </div>
    </nav>
";
        return; yield '';
    }

    // line 61
    public function block_leftnav($context, array $blocks = [])
    {
        $macros = $this->macros;
        yield "    <div id=\"api-tree\"></div>
";
        return; yield '';
    }

    // line 65
    public function block_control_panel($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 66
        yield "    <div id=\"control-panel\">
        ";
        // line 67
        if ((Twig\Extension\CoreExtension::length($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, (isset($context["project"]) || array_key_exists("project", $context) ? $context["project"] : (function () { throw new RuntimeError('Variable "project" does not exist.', 67, $this->source); })()), "versions", [], "any", false, false, false, 67)) > 1)) {
            // line 68
            yield "            <form action=\"#\">
                <select class=\"form-control\" id=\"version-switcher\" name=\"version\">
                    ";
            // line 70
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["project"]) || array_key_exists("project", $context) ? $context["project"] : (function () { throw new RuntimeError('Variable "project" does not exist.', 70, $this->source); })()), "versions", [], "any", false, false, false, 70));
            foreach ($context['_seq'] as $context["_key"] => $context["version"]) {
                // line 71
                yield "                        <option
                            value=\"";
                // line 72
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Doctum\Renderer\TwigExtension']->pathForStaticFile($context, (("../" . Twig\Extension\CoreExtension::urlencode($context["version"])) . "/index.html")), "html", null, true);
                yield "\"
                            data-version=\"";
                // line 73
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["version"], "html", null, true);
                yield "\" ";
                yield ((($context["version"] == CoreExtension::getAttribute($this->env, $this->source, (isset($context["project"]) || array_key_exists("project", $context) ? $context["project"] : (function () { throw new RuntimeError('Variable "project" does not exist.', 73, $this->source); })()), "version", [], "any", false, false, false, 73))) ? ("selected") : (""));
                yield ">";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["version"], "longname", [], "any", false, false, false, 73), "html", null, true);
                yield "</option>
                    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['version'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 75
            yield "                </select>
            </form>
        ";
        }
        // line 78
        yield "        <div class=\"search-bar hidden\" id=\"search-progress-bar-container\">
            <div class=\"progress\">
                <div class=\"progress-bar\" role=\"progressbar\" id=\"search-progress-bar\"
                    aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%\"></div>
            </div>
        </div>
        <form id=\"search-form\" action=\"";
        // line 84
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Doctum\Renderer\TwigExtension']->pathForStaticFile($context, "search.html"), "html", null, true);
        yield "\">
            <span class=\"icon icon-search\"></span>
            <input name=\"search\"
                   id=\"doctum-search-auto-complete\"
                   class=\"typeahead form-control\"
                   type=\"search\"
                   placeholder=\"";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Search");
        // line 90
        yield "\"
                   spellcheck=\"false\"
                   autocorrect=\"off\"
                   autocomplete=\"off\"
                   autocapitalize=\"off\">
            <div class=\"auto-complete-results\" id=\"auto-complete-results\"></div>
        </form>
    </div>
";
        return; yield '';
    }

    // line 100
    public function block_footer($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 101
        yield "<div id=\"footer\">
        ";
        // line 102
        yield Twig\Extension\CoreExtension::sprintf(\Wdes\phpI18nL10n\Launcher::gettext("Generated by %sDoctum, a API Documentation generator and fork of Sami%s."), "<a href=\"https://github.com/code-lts/doctum\">", "</a>");
        // line 105
        if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["project"]) || array_key_exists("project", $context) ? $context["project"] : (function () { throw new RuntimeError('Variable "project" does not exist.', 105, $this->source); })()), "hasFooterLink", [], "method", false, false, false, 105)) {
            // line 106
            $context["link"] = CoreExtension::getAttribute($this->env, $this->source, (isset($context["project"]) || array_key_exists("project", $context) ? $context["project"] : (function () { throw new RuntimeError('Variable "project" does not exist.', 106, $this->source); })()), "getFooterLink", [], "method", false, false, false, 106);
            // line 107
            yield "            <br/>";
            // line 108
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["link"]) || array_key_exists("link", $context) ? $context["link"] : (function () { throw new RuntimeError('Variable "link" does not exist.', 108, $this->source); })()), "before_text", [], "any", false, false, false, 108), "html", null, true);
            // line 109
            if ( !Twig\Extension\CoreExtension::testEmpty(CoreExtension::getAttribute($this->env, $this->source, (isset($context["link"]) || array_key_exists("link", $context) ? $context["link"] : (function () { throw new RuntimeError('Variable "link" does not exist.', 109, $this->source); })()), "href", [], "any", false, false, false, 109))) {
                // line 110
                yield " ";
                yield "<a href=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["link"]) || array_key_exists("link", $context) ? $context["link"] : (function () { throw new RuntimeError('Variable "link" does not exist.', 110, $this->source); })()), "href", [], "any", false, false, false, 110), "html", null, true);
                yield "\" rel=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["link"]) || array_key_exists("link", $context) ? $context["link"] : (function () { throw new RuntimeError('Variable "link" does not exist.', 110, $this->source); })()), "rel", [], "any", false, false, false, 110), "html", null, true);
                yield "\" target=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["link"]) || array_key_exists("link", $context) ? $context["link"] : (function () { throw new RuntimeError('Variable "link" does not exist.', 110, $this->source); })()), "target", [], "any", false, false, false, 110), "html", null, true);
                yield "\">";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["link"]) || array_key_exists("link", $context) ? $context["link"] : (function () { throw new RuntimeError('Variable "link" does not exist.', 110, $this->source); })()), "link_text", [], "any", false, false, false, 110), "html", null, true);
                yield "</a>";
                yield " ";
            }
            // line 112
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["link"]) || array_key_exists("link", $context) ? $context["link"] : (function () { throw new RuntimeError('Variable "link" does not exist.', 112, $this->source); })()), "after_text", [], "any", false, false, false, 112), "html", null, true);
        }
        // line 114
        yield "</div>";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "layout/layout.twig";
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
        return array (  336 => 114,  333 => 112,  320 => 110,  318 => 109,  316 => 108,  314 => 107,  312 => 106,  310 => 105,  308 => 102,  305 => 101,  301 => 100,  288 => 90,  278 => 84,  270 => 78,  265 => 75,  253 => 73,  249 => 72,  246 => 71,  242 => 70,  238 => 68,  236 => 67,  233 => 66,  229 => 65,  220 => 61,  207 => 54,  201 => 53,  195 => 52,  188 => 51,  180 => 49,  178 => 48,  171 => 47,  166 => 44,  151 => 42,  147 => 41,  142 => 38,  137 => 35,  135 => 34,  126 => 30,  119 => 25,  112 => 21,  108 => 20,  100 => 13,  92 => 11,  85 => 16,  83 => 15,  81 => 14,  79 => 13,  77 => 12,  75 => 11,  71 => 10,  65 => 7,  61 => 6,  57 => 4,  53 => 3,  42 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"layout/base.twig\" %}

{% block content %}
    <div id=\"content\">
        <div id=\"left-column\">
            {{ block('control_panel') }}
            {{ block('leftnav') }}
        </div>
        <div id=\"right-column\">
            {{ block('menu') }}
            {% block below_menu '' %}
            <div id=\"page-content\">
                {%- block page_content '' -%}
            </div>
            {{- block('footer') -}}
        </div>
    </div>
{% endblock %}

{% block menu %}
    <nav id=\"site-nav\" class=\"navbar navbar-default\" role=\"navigation\">
        <div class=\"container-fluid\">
            <div class=\"navbar-header\">
                <button type=\"button\" class=\"navbar-toggle\" data-toggle=\"collapse\" data-target=\"#navbar-elements\">
                    <span class=\"sr-only\">{% trans 'Toggle navigation' %}</span>
                    <span class=\"icon-bar\"></span>
                    <span class=\"icon-bar\"></span>
                    <span class=\"icon-bar\"></span>
                </button>
                <a class=\"navbar-brand\" href=\"{{ path('index.html') }}\">{{ project.config('title') }}</a>
            </div>
            <div class=\"collapse navbar-collapse\" id=\"navbar-elements\">
                <ul class=\"nav navbar-nav\">
                    {% if project.versions|length > 1 %}
                    <li role=\"presentation\" class=\"dropdown visible-xs-block visible-sm-block\">
                        <a class=\"dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\"
                            role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">
                        {%- trans 'Versions' %}&nbsp;<span class=\"caret\"></span>
                        </a>
                        <ul class=\"dropdown-menu\">
                        {% for version in project.versions -%}
                            <li {{ version == project.version ? 'class=\"active\"' : ''}}><a href=\"{{ path('../' ~ version|url_encode ~ '/index.html') }}\" data-version=\"{{ version }}\">{{ version.longname }}</a></li>
                        {% endfor -%}
                        </ul>
                    </li>
                    {% endif -%}
                    <li><a href=\"{{ path('classes.html') }}\">{% trans 'Classes' %}</a></li>
                    {% if has_namespaces -%}
                    {#  #}<li><a href=\"{{ path('namespaces.html') }}\">{% trans 'Namespaces' %}</a></li>
                    {% endif -%}
                    <li><a href=\"{{ path('interfaces.html') }}\">{% trans 'Interfaces' %}</a></li>
                    <li><a href=\"{{ path('traits.html') }}\">{% trans 'Traits' %}</a></li>
                    <li><a href=\"{{ path('doc-index.html') }}\">{% trans 'Index' %}</a></li>
                    <li><a href=\"{{ path('search.html') }}\">{% trans 'Search' %}</a></li>
                </ul>
            </div>
        </div>
    </nav>
{% endblock %}

{% block leftnav %}
    <div id=\"api-tree\"></div>
{% endblock %}

{% block control_panel %}
    <div id=\"control-panel\">
        {% if project.versions|length > 1 %}
            <form action=\"#\">
                <select class=\"form-control\" id=\"version-switcher\" name=\"version\">
                    {% for version in project.versions %}
                        <option
                            value=\"{{ path('../' ~ version|url_encode ~ '/index.html') }}\"
                            data-version=\"{{ version }}\" {{ version == project.version ? 'selected' : ''}}>{{ version.longname }}</option>
                    {% endfor %}
                </select>
            </form>
        {% endif %}
        <div class=\"search-bar hidden\" id=\"search-progress-bar-container\">
            <div class=\"progress\">
                <div class=\"progress-bar\" role=\"progressbar\" id=\"search-progress-bar\"
                    aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%\"></div>
            </div>
        </div>
        <form id=\"search-form\" action=\"{{ path('search.html') }}\">
            <span class=\"icon icon-search\"></span>
            <input name=\"search\"
                   id=\"doctum-search-auto-complete\"
                   class=\"typeahead form-control\"
                   type=\"search\"
                   placeholder=\"{% trans 'Search' %}\"
                   spellcheck=\"false\"
                   autocorrect=\"off\"
                   autocomplete=\"off\"
                   autocapitalize=\"off\">
            <div class=\"auto-complete-results\" id=\"auto-complete-results\"></div>
        </form>
    </div>
{% endblock %}

{%- block footer -%}
    <div id=\"footer\">
        {{ 'Generated by %sDoctum, a API Documentation generator and fork of Sami%s.'|trans|format(
            '<a href=\"https://github.com/code-lts/doctum\">', '</a>'
        )|raw }}
        {%- if project.hasFooterLink() -%}
            {% set link = project.getFooterLink() %}
            <br/>
            {{- link.before_text }}
            {%- if link.href is not empty -%}
                {{ \" \" }}<a href=\"{{ link.href }}\" rel=\"{{ link.rel }}\" target=\"{{ link.target }}\">{{ link.link_text }}</a>{{ \" \" }}
            {%- endif -%}
            {{ link.after_text -}}
        {%- endif -%}
    </div>
{%- endblock -%}
", "layout/layout.twig", "/home/santi/backapp/vendor/code-lts/doctum/src/Resources/themes/default/layout/layout.twig");
    }
}
