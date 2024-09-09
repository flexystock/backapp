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

/* layout/base.twig */
class __TwigTemplate_c055d46d3f8d36b8d391f69c91640610 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'head' => [$this, 'block_head'],
            'html' => [$this, 'block_html'],
            'body_class' => [$this, 'block_body_class'],
            'page_id' => [$this, 'block_page_id'],
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        yield "<!DOCTYPE html>
<html lang=\"";
        // line 2
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["project"]) || array_key_exists("project", $context) ? $context["project"] : (function () { throw new RuntimeError('Variable "project" does not exist.', 2, $this->source); })()), "config", ["language"], "method", false, false, false, 2), "html", null, true);
        yield "\">
<head>
    <meta charset=\"UTF-8\" />
    <meta name=\"robots\" content=\"index, follow, all\" />
    <title>";
        // line 6
        yield from $this->unwrap()->yieldBlock('title', $context, $blocks);
        yield "</title>

    ";
        // line 8
        yield from $this->unwrap()->yieldBlock('head', $context, $blocks);
        // line 21
        yield "
";
        // line 22
        if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["project"]) || array_key_exists("project", $context) ? $context["project"] : (function () { throw new RuntimeError('Variable "project" does not exist.', 22, $this->source); })()), "config", ["favicon"], "method", false, false, false, 22)) {
            // line 23
            yield "        <link rel=\"shortcut icon\" href=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["project"]) || array_key_exists("project", $context) ? $context["project"] : (function () { throw new RuntimeError('Variable "project" does not exist.', 23, $this->source); })()), "config", ["favicon"], "method", false, false, false, 23), "html", null, true);
            yield "\" />";
        }
        // line 25
        yield "
    ";
        // line 26
        if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["project"]) || array_key_exists("project", $context) ? $context["project"] : (function () { throw new RuntimeError('Variable "project" does not exist.', 26, $this->source); })()), "getBaseUrl", [], "method", false, false, false, 26)) {
            // line 27
            yield "    ";
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["project"]) || array_key_exists("project", $context) ? $context["project"] : (function () { throw new RuntimeError('Variable "project" does not exist.', 27, $this->source); })()), "versions", [], "any", false, false, false, 27));
            foreach ($context['_seq'] as $context["_key"] => $context["version"]) {
                // line 28
                yield "<link rel=\"search\"
    ";
                // line 29
                yield "      type=\"application/opensearchdescription+xml\"
    ";
                // line 30
                yield "      href=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::replace(CoreExtension::getAttribute($this->env, $this->source, (isset($context["project"]) || array_key_exists("project", $context) ? $context["project"] : (function () { throw new RuntimeError('Variable "project" does not exist.', 30, $this->source); })()), "getBaseUrl", [], "method", false, false, false, 30), ["%version%" => $context["version"]]), "html", null, true);
                yield "/opensearch.xml\"
    ";
                // line 31
                yield "      title=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["project"]) || array_key_exists("project", $context) ? $context["project"] : (function () { throw new RuntimeError('Variable "project" does not exist.', 31, $this->source); })()), "config", ["title"], "method", false, false, false, 31), "html", null, true);
                yield " (";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["version"], "html", null, true);
                yield ")\" />
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['version'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        }
        // line 34
        yield "</head>

";
        // line 36
        yield from $this->unwrap()->yieldBlock('html', $context, $blocks);
        // line 41
        yield "
</html>
";
        return; yield '';
    }

    // line 6
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["project"]) || array_key_exists("project", $context) ? $context["project"] : (function () { throw new RuntimeError('Variable "project" does not exist.', 6, $this->source); })()), "config", ["title"], "method", false, false, false, 6), "html", null, true);
        return; yield '';
    }

    // line 8
    public function block_head($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 9
        yield "        <link rel=\"stylesheet\" type=\"text/css\" href=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Doctum\Renderer\TwigExtension']->pathForStaticFile($context, "css/bootstrap.min.css"), "html", null, true);
        yield "\">
        <link rel=\"stylesheet\" type=\"text/css\" href=\"";
        // line 10
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Doctum\Renderer\TwigExtension']->pathForStaticFile($context, "css/bootstrap-theme.min.css"), "html", null, true);
        yield "\">
        <link rel=\"stylesheet\" type=\"text/css\" href=\"";
        // line 11
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Doctum\Renderer\TwigExtension']->pathForStaticFile($context, "css/doctum.css"), "html", null, true);
        yield "\">
        <link rel=\"stylesheet\" type=\"text/css\" href=\"";
        // line 12
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Doctum\Renderer\TwigExtension']->pathForStaticFile($context, "fonts/doctum-font.css"), "html", null, true);
        yield "\">
        <script src=\"";
        // line 13
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Doctum\Renderer\TwigExtension']->pathForStaticFile($context, "js/jquery-3.5.1.slim.min.js"), "html", null, true);
        yield "\"></script>
        <script async defer src=\"";
        // line 14
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Doctum\Renderer\TwigExtension']->pathForStaticFile($context, "doctum.js"), "html", null, true);
        yield "\"></script>
        <script async defer src=\"";
        // line 15
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Doctum\Renderer\TwigExtension']->pathForStaticFile($context, "js/bootstrap.min.js"), "html", null, true);
        yield "\"></script>
        <script async defer src=\"";
        // line 16
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Doctum\Renderer\TwigExtension']->pathForStaticFile($context, "js/autocomplete.min.js"), "html", null, true);
        yield "\"></script>
        <meta name=\"MobileOptimized\" content=\"width\">
        <meta name=\"HandheldFriendly\" content=\"true\">
        <meta name=\"viewport\" content=\"width=device-width,initial-scale=1,maximum-scale=1\">";
        return; yield '';
    }

    // line 36
    public function block_html($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 37
        yield "    <body id=\"";
        yield from $this->unwrap()->yieldBlock('body_class', $context, $blocks);
        yield "\" data-name=\"";
        yield from $this->unwrap()->yieldBlock('page_id', $context, $blocks);
        yield "\" data-root-path=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["root_path"]) || array_key_exists("root_path", $context) ? $context["root_path"] : (function () { throw new RuntimeError('Variable "root_path" does not exist.', 37, $this->source); })()), "html", null, true);
        yield "\" data-search-index-url=\"";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Doctum\Renderer\TwigExtension']->pathForStaticFile($context, "doctum-search.json"), "html", null, true);
        yield "\">
        ";
        // line 38
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        // line 39
        yield "    </body>
";
        return; yield '';
    }

    // line 37
    public function block_body_class($context, array $blocks = [])
    {
        $macros = $this->macros;
        yield "";
        return; yield '';
    }

    public function block_page_id($context, array $blocks = [])
    {
        $macros = $this->macros;
        yield "";
        return; yield '';
    }

    // line 38
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        yield "";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "layout/base.twig";
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
        return array (  205 => 38,  190 => 37,  184 => 39,  182 => 38,  171 => 37,  167 => 36,  158 => 16,  154 => 15,  150 => 14,  146 => 13,  142 => 12,  138 => 11,  134 => 10,  129 => 9,  125 => 8,  117 => 6,  110 => 41,  108 => 36,  104 => 34,  92 => 31,  87 => 30,  84 => 29,  81 => 28,  76 => 27,  74 => 26,  71 => 25,  66 => 23,  64 => 22,  61 => 21,  59 => 8,  54 => 6,  47 => 2,  44 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<!DOCTYPE html>
<html lang=\"{{ project.config('language') }}\">
<head>
    <meta charset=\"UTF-8\" />
    <meta name=\"robots\" content=\"index, follow, all\" />
    <title>{% block title project.config('title') %}</title>

    {% block head %}
        <link rel=\"stylesheet\" type=\"text/css\" href=\"{{ path('css/bootstrap.min.css') }}\">
        <link rel=\"stylesheet\" type=\"text/css\" href=\"{{ path('css/bootstrap-theme.min.css') }}\">
        <link rel=\"stylesheet\" type=\"text/css\" href=\"{{ path('css/doctum.css') }}\">
        <link rel=\"stylesheet\" type=\"text/css\" href=\"{{ path('fonts/doctum-font.css') }}\">
        <script src=\"{{ path('js/jquery-3.5.1.slim.min.js') }}\"></script>
        <script async defer src=\"{{ path('doctum.js') }}\"></script>
        <script async defer src=\"{{ path('js/bootstrap.min.js') }}\"></script>
        <script async defer src=\"{{ path('js/autocomplete.min.js') }}\"></script>
        <meta name=\"MobileOptimized\" content=\"width\">
        <meta name=\"HandheldFriendly\" content=\"true\">
        <meta name=\"viewport\" content=\"width=device-width,initial-scale=1,maximum-scale=1\">
    {%- endblock %}

{##}{% if project.config('favicon') %}
        <link rel=\"shortcut icon\" href=\"{{ project.config('favicon') }}\" />
    {%- endif %}

    {% if project.getBaseUrl() %}
    {#  #}{%- for version in project.versions -%}
    {#  #}<link rel=\"search\"
    {#  #}      type=\"application/opensearchdescription+xml\"
    {#  #}      href=\"{{ project.getBaseUrl()|replace({'%version%': version}) }}/opensearch.xml\"
    {#  #}      title=\"{{ project.config('title') }} ({{ version }})\" />
    {#  #}{% endfor -%}
    {% endif %}
</head>

{% block html %}
    <body id=\"{% block body_class '' %}\" data-name=\"{% block page_id '' %}\" data-root-path=\"{{ root_path }}\" data-search-index-url=\"{{ path('doctum-search.json') }}\">
        {% block content '' %}
    </body>
{% endblock %}

</html>
", "layout/base.twig", "/home/santi/backapp/vendor/code-lts/doctum/src/Resources/themes/default/layout/base.twig");
    }
}
