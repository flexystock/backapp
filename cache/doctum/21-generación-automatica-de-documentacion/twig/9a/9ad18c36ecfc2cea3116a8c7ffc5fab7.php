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

/* class.twig */
class __TwigTemplate_9131cc44e378c03640a108c46778261d extends Template
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
            'class_signature' => [$this, 'block_class_signature'],
            'method_signature' => [$this, 'block_method_signature'],
            'method_parameters_signature' => [$this, 'block_method_parameters_signature'],
            'parameters' => [$this, 'block_parameters'],
            'return' => [$this, 'block_return'],
            'exceptions' => [$this, 'block_exceptions'],
            'examples' => [$this, 'block_examples'],
            'see' => [$this, 'block_see'],
            'constants' => [$this, 'block_constants'],
            'properties' => [$this, 'block_properties'],
            'methods' => [$this, 'block_methods'],
            'methods_details' => [$this, 'block_methods_details'],
            'method' => [$this, 'block_method'],
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
        $macros["__internal_parse_13"] = $this->macros["__internal_parse_13"] = $this->loadTemplate("macros.twig", "class.twig", 2)->unwrap();
        // line 1
        $this->parent = $this->loadTemplate("layout/layout.twig", "class.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        yield (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 3, $this->source); })());
        yield " | ";
        yield from $this->yieldParentBlock("title", $context, $blocks);
        return; yield '';
    }

    // line 4
    public function block_body_class($context, array $blocks = [])
    {
        $macros = $this->macros;
        yield "class";
        return; yield '';
    }

    // line 5
    public function block_page_id($context, array $blocks = [])
    {
        $macros = $this->macros;
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(("class:" . Twig\Extension\CoreExtension::replace(CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 5, $this->source); })()), "name", [], "any", false, false, false, 5), ["\\" => "_"])), "html", null, true);
        return; yield '';
    }

    // line 7
    public function block_below_menu($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 8
        yield "    ";
        if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 8, $this->source); })()), "namespace", [], "any", false, false, false, 8)) {
            // line 9
            yield "        <div class=\"namespace-breadcrumbs\">
            <ol class=\"breadcrumb\">
                <li><span class=\"label label-default\">";
            // line 11
            yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_class_category_name", [CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 11, $this->source); })()), "getCategoryId", [], "method", false, false, false, 11)], 11, $context, $this->getSourceContext());
            yield "</span></li>
                ";
            // line 12
            yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_breadcrumbs", [CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 12, $this->source); })()), "namespace", [], "any", false, false, false, 12)], 12, $context, $this->getSourceContext());
            // line 13
            yield "<li>";
            yield CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 13, $this->source); })()), "shortname", [], "any", false, false, false, 13);
            yield "</li>
            </ol>
        </div>
    ";
        }
        return; yield '';
    }

    // line 19
    public function block_page_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 20
        yield "
    <div class=\"page-header\">
        <h1>";
        // line 23
        yield Twig\Extension\CoreExtension::last($this->env->getCharset(), Twig\Extension\CoreExtension::split($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 23, $this->source); })()), "name", [], "any", false, false, false, 23), "\\"));
        // line 24
        yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_deprecated", [(isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 24, $this->source); })())], 24, $context, $this->getSourceContext());
        yield "
            ";
        // line 25
        if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 25, $this->source); })()), "isReadOnly", [], "method", false, false, false, 25)) {
            yield "<small><span class=\"label label-primary\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("read-only");
            yield "</span></small>";
        }
        // line 26
        yield "</h1>
    </div>

    ";
        // line 29
        if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 29, $this->source); })()), "hasSince", [], "method", false, false, false, 29)) {
            // line 30
            yield "        <i>";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(\Wdes\phpI18nL10n\Launcher::gettext("Since:"), "html", null, true);
            yield " ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 30, $this->source); })()), "getSince", [], "method", false, false, false, 30), "html", null, true);
            yield "</i>
        <br>
    ";
        }
        // line 33
        yield "
    <p>";
        // line 34
        yield from         $this->unwrap()->yieldBlock("class_signature", $context, $blocks);
        yield "</p>

    ";
        // line 36
        yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_deprecations", [(isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 36, $this->source); })())], 36, $context, $this->getSourceContext());
        yield "
    ";
        // line 37
        yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_internals", [(isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 37, $this->source); })())], 37, $context, $this->getSourceContext());
        yield "

    ";
        // line 39
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 39, $this->source); })()), "shortdesc", [], "any", false, false, false, 39) || CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 39, $this->source); })()), "longdesc", [], "any", false, false, false, 39))) {
            // line 40
            yield "        <div class=\"description\">
            ";
            // line 41
            if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 41, $this->source); })()), "shortdesc", [], "any", false, false, false, 41)) {
                // line 42
                yield "<p>";
                yield $this->extensions['Doctum\Renderer\TwigExtension']->markdownToHtml($this->extensions['Doctum\Renderer\TwigExtension']->parseDesc(CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 42, $this->source); })()), "shortdesc", [], "any", false, false, false, 42), (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 42, $this->source); })())));
                yield "</p>";
            }
            // line 44
            yield "            ";
            if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 44, $this->source); })()), "longdesc", [], "any", false, false, false, 44)) {
                // line 45
                yield "<p>";
                yield $this->extensions['Doctum\Renderer\TwigExtension']->markdownToHtml($this->extensions['Doctum\Renderer\TwigExtension']->parseDesc(CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 45, $this->source); })()), "longdesc", [], "any", false, false, false, 45), (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 45, $this->source); })())));
                yield "</p>";
            }
            // line 47
            yield "        </div>
    ";
        }
        // line 49
        yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_todos", [(isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 49, $this->source); })())], 49, $context, $this->getSourceContext());
        // line 51
        if ((isset($context["traits"]) || array_key_exists("traits", $context) ? $context["traits"] : (function () { throw new RuntimeError('Variable "traits" does not exist.', 51, $this->source); })())) {
            // line 52
            yield "        <h2>";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Traits");
            yield "</h2>

        ";
            // line 54
            yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_render_classes", [(isset($context["traits"]) || array_key_exists("traits", $context) ? $context["traits"] : (function () { throw new RuntimeError('Variable "traits" does not exist.', 54, $this->source); })())], 54, $context, $this->getSourceContext());
            yield "
    ";
        }
        // line 56
        yield "
    ";
        // line 57
        if ((isset($context["constants"]) || array_key_exists("constants", $context) ? $context["constants"] : (function () { throw new RuntimeError('Variable "constants" does not exist.', 57, $this->source); })())) {
            // line 58
            yield "        <h2>";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Constants");
            yield "</h2>";
            // line 60
            yield from             $this->unwrap()->yieldBlock("constants", $context, $blocks);
            yield "
    ";
        }
        // line 62
        yield "
    ";
        // line 63
        if ((isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 63, $this->source); })())) {
            // line 64
            yield "        <h2>";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Properties");
            yield "</h2>

        ";
            // line 66
            yield from             $this->unwrap()->yieldBlock("properties", $context, $blocks);
            yield "
    ";
        }
        // line 68
        yield "
    ";
        // line 69
        if ((isset($context["methods"]) || array_key_exists("methods", $context) ? $context["methods"] : (function () { throw new RuntimeError('Variable "methods" does not exist.', 69, $this->source); })())) {
            // line 70
            yield "        <h2>";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Methods");
            yield "</h2>

        ";
            // line 72
            yield from             $this->unwrap()->yieldBlock("methods", $context, $blocks);
            yield "

        <h2>";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Details");
            // line 74
            yield "</h2>

        ";
            // line 76
            yield from             $this->unwrap()->yieldBlock("methods_details", $context, $blocks);
            yield "
    ";
        }
        // line 78
        yield "
";
        return; yield '';
    }

    // line 81
    public function block_class_signature($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 82
        if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 82, $this->source); })()), "final", [], "any", false, false, false, 82)) {
            yield "final ";
        }
        // line 83
        yield "    ";
        if (( !CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 83, $this->source); })()), "interface", [], "any", false, false, false, 83) && CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 83, $this->source); })()), "abstract", [], "any", false, false, false, 83))) {
            yield "abstract ";
        }
        // line 84
        yield "    ";
        yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_class_category_name", [CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 84, $this->source); })()), "getCategoryId", [], "method", false, false, false, 84)], 84, $context, $this->getSourceContext());
        yield "
    <strong>";
        // line 85
        yield CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 85, $this->source); })()), "shortname", [], "any", false, false, false, 85);
        yield "</strong>";
        // line 86
        if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 86, $this->source); })()), "parent", [], "any", false, false, false, 86)) {
            // line 87
            yield "        ";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("extends");
            yield " ";
            yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_class_link", [CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 87, $this->source); })()), "parent", [], "any", false, false, false, 87)], 87, $context, $this->getSourceContext());
        }
        // line 89
        if ((Twig\Extension\CoreExtension::length($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 89, $this->source); })()), "interfaces", [], "any", false, false, false, 89)) > 0)) {
            // line 90
            yield "        ";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("implements");
            // line 91
            yield "        ";
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 91, $this->source); })()), "interfaces", [], "any", false, false, false, 91));
            $context['loop'] = [
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            ];
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["_key"] => $context["interface"]) {
                // line 92
                yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_class_link", [$context["interface"]], 92, $context, $this->getSourceContext());
                // line 93
                if ( !CoreExtension::getAttribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, false, 93)) {
                    yield ", ";
                }
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['length'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['interface'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        }
        // line 96
        if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 96, $this->source); })()), "hasMixins", [], "any", false, false, false, 96)) {
            // line 97
            yield "        ";
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 97, $this->source); })()), "getMixins", [], "method", false, false, false, 97));
            foreach ($context['_seq'] as $context["_key"] => $context["mixin"]) {
                // line 98
                yield "            <i>mixin</i> ";
                yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_class_link", [CoreExtension::getAttribute($this->env, $this->source, $context["mixin"], "class", [], "any", false, false, false, 98)], 98, $context, $this->getSourceContext());
                yield "
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['mixin'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        }
        // line 101
        yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_source_link", [(isset($context["project"]) || array_key_exists("project", $context) ? $context["project"] : (function () { throw new RuntimeError('Variable "project" does not exist.', 101, $this->source); })()), (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 101, $this->source); })())], 101, $context, $this->getSourceContext());
        yield "
";
        return; yield '';
    }

    // line 104
    public function block_method_signature($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 105
        if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 105, $this->source); })()), "final", [], "any", false, false, false, 105)) {
            yield "final";
        }
        // line 106
        yield "    ";
        if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 106, $this->source); })()), "abstract", [], "any", false, false, false, 106)) {
            yield "abstract";
        }
        // line 107
        yield "    ";
        if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 107, $this->source); })()), "static", [], "any", false, false, false, 107)) {
            yield "static";
        }
        // line 108
        yield "    ";
        if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 108, $this->source); })()), "protected", [], "any", false, false, false, 108)) {
            yield "protected";
        }
        // line 109
        yield "    ";
        if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 109, $this->source); })()), "private", [], "any", false, false, false, 109)) {
            yield "private";
        }
        // line 110
        yield "    ";
        yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_hint_link", [CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 110, $this->source); })()), "hint", [], "any", false, false, false, 110), CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 110, $this->source); })()), "isIntersectionType", [], "method", false, false, false, 110)], 110, $context, $this->getSourceContext());
        yield "
    <strong>";
        // line 111
        yield CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 111, $this->source); })()), "name", [], "any", false, false, false, 111);
        yield "</strong>";
        yield from         $this->unwrap()->yieldBlock("method_parameters_signature", $context, $blocks);
        return; yield '';
    }

    // line 114
    public function block_method_parameters_signature($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 115
        $macros["__internal_parse_14"] = $this->loadTemplate("macros.twig", "class.twig", 115)->unwrap();
        // line 116
        yield CoreExtension::callMacro($macros["__internal_parse_14"], "macro_method_parameters_signature", [(isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 116, $this->source); })())], 116, $context, $this->getSourceContext());
        yield "
    ";
        // line 117
        yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_deprecated", [(isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 117, $this->source); })())], 117, $context, $this->getSourceContext());
        return; yield '';
    }

    // line 120
    public function block_parameters($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 121
        yield "    <table class=\"table table-condensed\">
        ";
        // line 122
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 122, $this->source); })()), "parameters", [], "any", false, false, false, 122));
        foreach ($context['_seq'] as $context["_key"] => $context["parameter"]) {
            // line 123
            yield "            <tr>
                <td>";
            // line 124
            if (CoreExtension::getAttribute($this->env, $this->source, $context["parameter"], "hint", [], "any", false, false, false, 124)) {
                yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_hint_link", [CoreExtension::getAttribute($this->env, $this->source, $context["parameter"], "hint", [], "any", false, false, false, 124), CoreExtension::getAttribute($this->env, $this->source, $context["parameter"], "isIntersectionType", [], "method", false, false, false, 124)], 124, $context, $this->getSourceContext());
            }
            yield "</td>
                <td>";
            // line 125
            if (CoreExtension::getAttribute($this->env, $this->source, $context["parameter"], "variadic", [], "any", false, false, false, 125)) {
                yield "...";
            }
            yield "\$";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["parameter"], "name", [], "any", false, false, false, 125);
            yield "</td>
                <td>";
            // line 126
            yield $this->extensions['Doctum\Renderer\TwigExtension']->markdownToHtml($this->extensions['Doctum\Renderer\TwigExtension']->parseDesc(CoreExtension::getAttribute($this->env, $this->source, $context["parameter"], "shortdesc", [], "any", false, false, false, 126), (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 126, $this->source); })())));
            yield "</td>
            </tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['parameter'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 129
        yield "    </table>
";
        return; yield '';
    }

    // line 132
    public function block_return($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 133
        yield "    <table class=\"table table-condensed\">
        <tr>
            <td>";
        // line 135
        yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_hint_link", [CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 135, $this->source); })()), "hint", [], "any", false, false, false, 135), CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 135, $this->source); })()), "isIntersectionType", [], "method", false, false, false, 135)], 135, $context, $this->getSourceContext());
        yield "</td>
            <td>";
        // line 136
        yield $this->extensions['Doctum\Renderer\TwigExtension']->markdownToHtml($this->extensions['Doctum\Renderer\TwigExtension']->parseDesc(CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 136, $this->source); })()), "hintDesc", [], "any", false, false, false, 136), (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 136, $this->source); })())));
        yield "</td>
        </tr>
    </table>
";
        return; yield '';
    }

    // line 141
    public function block_exceptions($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 142
        yield "    <table class=\"table table-condensed\">
        ";
        // line 143
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 143, $this->source); })()), "exceptions", [], "any", false, false, false, 143));
        foreach ($context['_seq'] as $context["_key"] => $context["exception"]) {
            // line 144
            yield "            <tr>
                <td>";
            // line 145
            yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_class_link", [CoreExtension::getAttribute($this->env, $this->source, $context["exception"], 0, [], "array", false, false, false, 145)], 145, $context, $this->getSourceContext());
            yield "</td>
                <td>";
            // line 146
            yield $this->extensions['Doctum\Renderer\TwigExtension']->markdownToHtml($this->extensions['Doctum\Renderer\TwigExtension']->parseDesc(CoreExtension::getAttribute($this->env, $this->source, $context["exception"], 1, [], "array", false, false, false, 146), (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 146, $this->source); })())));
            yield "</td>
            </tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['exception'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 149
        yield "    </table>
";
        return; yield '';
    }

    // line 152
    public function block_examples($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 153
        yield "    <table class=\"table table-condensed\">
        ";
        // line 154
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 154, $this->source); })()), "getExamples", [], "method", false, false, false, 154));
        foreach ($context['_seq'] as $context["_key"] => $context["example"]) {
            // line 155
            yield "            <tr>
                <td><pre class=\"examples\">";
            // line 157
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::join($context["example"], " "), "html", null, true);
            // line 158
            yield "</pre></td>
            </tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['example'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 161
        yield "    </table>
";
        return; yield '';
    }

    // line 164
    public function block_see($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 165
        yield "    <table class=\"table table-condensed\">
        ";
        // line 166
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 166, $this->source); })()), "getSee", [], "method", false, false, false, 166));
        foreach ($context['_seq'] as $context["_key"] => $context["see"]) {
            // line 167
            yield "            <tr>
                <td>
                    ";
            // line 169
            if (CoreExtension::getAttribute($this->env, $this->source, $context["see"], 4, [], "array", false, false, false, 169)) {
                // line 170
                yield "                        <a href=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["see"], 4, [], "array", false, false, false, 170), "html", null, true);
                yield "\">";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["see"], 4, [], "array", false, false, false, 170), "html", null, true);
                yield "</a>
                    ";
            } elseif (CoreExtension::getAttribute($this->env, $this->source,             // line 171
$context["see"], 3, [], "array", false, false, false, 171)) {
                // line 172
                yield "                        ";
                yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_method_link", [CoreExtension::getAttribute($this->env, $this->source, $context["see"], 3, [], "array", false, false, false, 172), false, false], 172, $context, $this->getSourceContext());
                yield "
                    ";
            } elseif (CoreExtension::getAttribute($this->env, $this->source,             // line 173
$context["see"], 2, [], "array", false, false, false, 173)) {
                // line 174
                yield "                        ";
                yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_class_link", [CoreExtension::getAttribute($this->env, $this->source, $context["see"], 2, [], "array", false, false, false, 174)], 174, $context, $this->getSourceContext());
                yield "
                    ";
            } else {
                // line 176
                yield "                        ";
                yield CoreExtension::getAttribute($this->env, $this->source, $context["see"], 0, [], "array", false, false, false, 176);
                yield "
                    ";
            }
            // line 178
            yield "                </td>
                <td>";
            // line 179
            yield CoreExtension::getAttribute($this->env, $this->source, $context["see"], 1, [], "array", false, false, false, 179);
            yield "</td>
            </tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['see'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 182
        yield "    </table>
";
        return; yield '';
    }

    // line 185
    public function block_constants($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 186
        yield "    <table class=\"table table-condensed\">
        ";
        // line 187
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["constants"]) || array_key_exists("constants", $context) ? $context["constants"] : (function () { throw new RuntimeError('Variable "constants" does not exist.', 187, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["constant"]) {
            // line 188
            yield "            <tr>
                <td>
                    ";
            // line 191
            yield "                    ";
            // line 192
            yield "                    ";
            if (CoreExtension::getAttribute($this->env, $this->source, $context["constant"], "isPrivate", [], "method", false, false, false, 192)) {
                yield "private
                    ";
            } elseif (CoreExtension::getAttribute($this->env, $this->source,             // line 193
$context["constant"], "isProtected", [], "method", false, false, false, 193)) {
                yield "protected";
            }
            // line 194
            yield "                    ";
            if (CoreExtension::getAttribute($this->env, $this->source, $context["constant"], "isInternal", [], "method", false, false, false, 194)) {
                yield "<span class=\"label label-warning\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("internal");
                yield "</span>";
            }
            // line 195
            yield "                    ";
            if (CoreExtension::getAttribute($this->env, $this->source, $context["constant"], "isDeprecated", [], "method", false, false, false, 195)) {
                yield "<span class=\"label label-danger\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("deprecated");
                yield "</span>";
            }
            // line 196
            yield "                    ";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["constant"], "name", [], "any", false, false, false, 196);
            yield "
                    ";
            // line 197
            if (CoreExtension::getAttribute($this->env, $this->source, $context["constant"], "hasSince", [], "method", false, false, false, 197)) {
                // line 198
                yield "                        <i>";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(\Wdes\phpI18nL10n\Launcher::gettext("Since:"), "html", null, true);
                yield " ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["constant"], "getSince", [], "method", false, false, false, 198), "html", null, true);
                yield "</i>
                        <br>
                    ";
            }
            // line 201
            yield "                </td>
                <td class=\"last\">
                    <p><em>";
            // line 203
            yield $this->extensions['Doctum\Renderer\TwigExtension']->markdownToHtml($this->extensions['Doctum\Renderer\TwigExtension']->parseDesc(CoreExtension::getAttribute($this->env, $this->source, $context["constant"], "shortdesc", [], "any", false, false, false, 203), (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 203, $this->source); })())));
            yield "</em></p>
                    <p>";
            // line 204
            yield $this->extensions['Doctum\Renderer\TwigExtension']->markdownToHtml($this->extensions['Doctum\Renderer\TwigExtension']->parseDesc(CoreExtension::getAttribute($this->env, $this->source, $context["constant"], "longdesc", [], "any", false, false, false, 204), (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 204, $this->source); })())));
            yield "</p>
                </td>
            </tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['constant'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 208
        yield "    </table>
";
        return; yield '';
    }

    // line 211
    public function block_properties($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 212
        yield "    <table class=\"table table-condensed\">
        ";
        // line 213
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["properties"]) || array_key_exists("properties", $context) ? $context["properties"] : (function () { throw new RuntimeError('Variable "properties" does not exist.', 213, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["property"]) {
            // line 214
            yield "            <tr>
                <td class=\"type\" id=\"property_";
            // line 215
            yield CoreExtension::getAttribute($this->env, $this->source, $context["property"], "name", [], "any", false, false, false, 215);
            yield "\">
                    ";
            // line 216
            if (CoreExtension::getAttribute($this->env, $this->source, $context["property"], "isStatic", [], "method", false, false, false, 216)) {
                yield "static";
            }
            // line 217
            yield "                    ";
            if (CoreExtension::getAttribute($this->env, $this->source, $context["property"], "isProtected", [], "method", false, false, false, 217)) {
                yield "protected";
            }
            // line 218
            yield "                    ";
            if (CoreExtension::getAttribute($this->env, $this->source, $context["property"], "isPrivate", [], "method", false, false, false, 218)) {
                yield "private";
            }
            // line 219
            yield "                    ";
            yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_hint_link", [CoreExtension::getAttribute($this->env, $this->source, $context["property"], "hint", [], "any", false, false, false, 219), CoreExtension::getAttribute($this->env, $this->source, $context["property"], "isIntersectionType", [], "method", false, false, false, 219)], 219, $context, $this->getSourceContext());
            yield "
                    ";
            // line 220
            if (CoreExtension::getAttribute($this->env, $this->source, $context["property"], "isInternal", [], "method", false, false, false, 220)) {
                yield "<span class=\"label label-warning\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("internal");
                yield "</span>";
            }
            // line 221
            yield "                    ";
            if (CoreExtension::getAttribute($this->env, $this->source, $context["property"], "isDeprecated", [], "method", false, false, false, 221)) {
                yield "<span class=\"label label-danger\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("deprecated");
                yield "</span>";
            }
            // line 222
            yield "                    ";
            if (CoreExtension::getAttribute($this->env, $this->source, $context["property"], "isReadOnly", [], "method", false, false, false, 222)) {
                yield "<span class=\"label label-primary\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("read-only");
                yield "</span>";
            }
            // line 223
            yield "                    ";
            if (CoreExtension::getAttribute($this->env, $this->source, $context["property"], "isWriteOnly", [], "method", false, false, false, 223)) {
                yield "<span class=\"label label-success\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("write-only");
                yield "</span>";
            }
            // line 224
            yield "
                    ";
            // line 225
            if (CoreExtension::getAttribute($this->env, $this->source, $context["property"], "hasSince", [], "method", false, false, false, 225)) {
                // line 226
                yield "                        <i>";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(\Wdes\phpI18nL10n\Launcher::gettext("Since:"), "html", null, true);
                yield " ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["property"], "getSince", [], "method", false, false, false, 226), "html", null, true);
                yield "</i>
                        <br>
                    ";
            }
            // line 229
            yield "                </td>
                <td>\$";
            // line 230
            yield CoreExtension::getAttribute($this->env, $this->source, $context["property"], "name", [], "any", false, false, false, 230);
            yield "</td>
                <td class=\"last\">";
            // line 231
            yield $this->extensions['Doctum\Renderer\TwigExtension']->markdownToHtml($this->extensions['Doctum\Renderer\TwigExtension']->parseDesc(CoreExtension::getAttribute($this->env, $this->source, $context["property"], "shortdesc", [], "any", false, false, false, 231), (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 231, $this->source); })())));
            yield "</td>
                <td>";
            // line 233
            if ( !(CoreExtension::getAttribute($this->env, $this->source, $context["property"], "class", [], "any", false, false, false, 233) === (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 233, $this->source); })()))) {
                // line 234
                yield "<small>";
                yield Twig\Extension\CoreExtension::sprintf(\Wdes\phpI18nL10n\Launcher::gettext("from&nbsp;%s"), CoreExtension::callMacro($macros["__internal_parse_13"], "macro_property_link", [$context["property"], false, true], 234, $context, $this->getSourceContext()));
                yield "</small>";
            }
            // line 236
            yield "</td>
            </tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['property'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 239
        yield "    </table>
";
        return; yield '';
    }

    // line 242
    public function block_methods($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 243
        yield "    <div class=\"container-fluid underlined\">
        ";
        // line 244
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["methods"]) || array_key_exists("methods", $context) ? $context["methods"] : (function () { throw new RuntimeError('Variable "methods" does not exist.', 244, $this->source); })()));
        $context['loop'] = [
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        ];
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["method"]) {
            // line 245
            yield "            <div class=\"row\">
                <div class=\"col-md-2 type\">
                    ";
            // line 247
            if (CoreExtension::getAttribute($this->env, $this->source, $context["method"], "static", [], "any", false, false, false, 247)) {
                yield "static&nbsp;";
            }
            yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_hint_link", [CoreExtension::getAttribute($this->env, $this->source, $context["method"], "hint", [], "any", false, false, false, 247), CoreExtension::getAttribute($this->env, $this->source, $context["method"], "isIntersectionType", [], "method", false, false, false, 247)], 247, $context, $this->getSourceContext());
            yield "
                </div>
                <div class=\"col-md-8\">
                    <a href=\"#method_";
            // line 250
            yield CoreExtension::getAttribute($this->env, $this->source, $context["method"], "name", [], "any", false, false, false, 250);
            yield "\">";
            yield CoreExtension::getAttribute($this->env, $this->source, $context["method"], "name", [], "any", false, false, false, 250);
            yield "</a>";
            yield from             $this->unwrap()->yieldBlock("method_parameters_signature", $context, $blocks);
            yield "
                    ";
            // line 251
            if ( !CoreExtension::getAttribute($this->env, $this->source, $context["method"], "shortdesc", [], "any", false, false, false, 251)) {
                // line 252
                yield "                        <p class=\"no-description\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("No description");
                yield "</p>
                    ";
            } else {
                // line 254
                yield "                        <p>";
                yield $this->extensions['Doctum\Renderer\TwigExtension']->markdownToHtml($this->extensions['Doctum\Renderer\TwigExtension']->parseDesc(CoreExtension::getAttribute($this->env, $this->source, $context["method"], "shortdesc", [], "any", false, false, false, 254), (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 254, $this->source); })())));
                yield "</p>";
            }
            // line 256
            yield "                </div>
                <div class=\"col-md-2\">";
            // line 258
            if ( !(CoreExtension::getAttribute($this->env, $this->source, $context["method"], "class", [], "any", false, false, false, 258) === (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 258, $this->source); })()))) {
                // line 259
                yield "<small>";
                yield Twig\Extension\CoreExtension::sprintf(\Wdes\phpI18nL10n\Launcher::gettext("from&nbsp;%s"), CoreExtension::callMacro($macros["__internal_parse_13"], "macro_method_link", [$context["method"], false, true], 259, $context, $this->getSourceContext()));
                yield "</small>";
            }
            // line 261
            yield "</div>
            </div>
        ";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['method'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 264
        yield "    </div>
";
        return; yield '';
    }

    // line 267
    public function block_methods_details($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 268
        yield "    <div id=\"method-details\">
        ";
        // line 269
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["methods"]) || array_key_exists("methods", $context) ? $context["methods"] : (function () { throw new RuntimeError('Variable "methods" does not exist.', 269, $this->source); })()));
        $context['loop'] = [
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        ];
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["method"]) {
            // line 270
            yield "            <div class=\"method-item\">
                ";
            // line 271
            yield from             $this->unwrap()->yieldBlock("method", $context, $blocks);
            yield "
            </div>
        ";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['method'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 274
        yield "    </div>
";
        return; yield '';
    }

    // line 277
    public function block_method($context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 278
        yield "    <h3 id=\"method_";
        yield CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 278, $this->source); })()), "name", [], "any", false, false, false, 278);
        yield "\">
        <div class=\"location\">";
        // line 279
        if ( !(CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 279, $this->source); })()), "class", [], "any", false, false, false, 279) === (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 279, $this->source); })()))) {
            yield Twig\Extension\CoreExtension::sprintf(\Wdes\phpI18nL10n\Launcher::gettext("in %s"), CoreExtension::callMacro($macros["__internal_parse_13"], "macro_method_link", [(isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 279, $this->source); })()), false, true], 279, $context, $this->getSourceContext()));
            yield " ";
        }
        yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_method_source_link", [(isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 279, $this->source); })())], 279, $context, $this->getSourceContext());
        yield "</div>
        <code>";
        // line 280
        yield from         $this->unwrap()->yieldBlock("method_signature", $context, $blocks);
        yield "</code>
    </h3>
    <div class=\"details\">";
        // line 283
        if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 283, $this->source); })()), "hasSince", [], "method", false, false, false, 283)) {
            // line 284
            yield "<i>";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(\Wdes\phpI18nL10n\Launcher::gettext("Since:"), "html", null, true);
            yield " ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 284, $this->source); })()), "getSince", [], "method", false, false, false, 284), "html", null, true);
            yield "</i>
            <br>";
        }
        // line 287
        yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_deprecations", [(isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 287, $this->source); })())], 287, $context, $this->getSourceContext());
        yield "
        ";
        // line 288
        yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_internals", [(isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 288, $this->source); })())], 288, $context, $this->getSourceContext());
        yield "

        <div class=\"method-description\">
            ";
        // line 291
        if (( !CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 291, $this->source); })()), "shortdesc", [], "any", false, false, false, 291) &&  !CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 291, $this->source); })()), "longdesc", [], "any", false, false, false, 291))) {
            // line 292
            yield "                <p class=\"no-description\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("No description");
            yield "</p>
            ";
        } else {
            // line 294
            yield "                ";
            if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 294, $this->source); })()), "shortdesc", [], "any", false, false, false, 294)) {
                // line 295
                yield "<p>";
                yield $this->extensions['Doctum\Renderer\TwigExtension']->markdownToHtml($this->extensions['Doctum\Renderer\TwigExtension']->parseDesc(CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 295, $this->source); })()), "shortdesc", [], "any", false, false, false, 295), (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 295, $this->source); })())));
                yield "</p>";
            }
            // line 297
            yield "                ";
            if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 297, $this->source); })()), "longdesc", [], "any", false, false, false, 297)) {
                // line 298
                yield "<p>";
                yield $this->extensions['Doctum\Renderer\TwigExtension']->markdownToHtml($this->extensions['Doctum\Renderer\TwigExtension']->parseDesc(CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 298, $this->source); })()), "longdesc", [], "any", false, false, false, 298), (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 298, $this->source); })())));
                yield "</p>";
            }
        }
        // line 301
        yield CoreExtension::callMacro($macros["__internal_parse_13"], "macro_todos", [(isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 301, $this->source); })())], 301, $context, $this->getSourceContext());
        // line 302
        yield "</div>
        <div class=\"tags\">
            ";
        // line 304
        if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 304, $this->source); })()), "parameters", [], "any", false, false, false, 304)) {
            // line 305
            yield "                <h4>";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Parameters");
            yield "</h4>

                ";
            // line 307
            yield from             $this->unwrap()->yieldBlock("parameters", $context, $blocks);
            yield "
            ";
        }
        // line 309
        yield "
            ";
        // line 310
        if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 310, $this->source); })()), "hintDesc", [], "any", false, false, false, 310) || CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 310, $this->source); })()), "hint", [], "any", false, false, false, 310))) {
            // line 311
            yield "                <h4>";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Return Value");
            yield "</h4>

                ";
            // line 313
            yield from             $this->unwrap()->yieldBlock("return", $context, $blocks);
            yield "
            ";
        }
        // line 315
        yield "
            ";
        // line 316
        if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 316, $this->source); })()), "exceptions", [], "any", false, false, false, 316)) {
            // line 317
            yield "                <h4>";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Exceptions");
            yield "</h4>

                ";
            // line 319
            yield from             $this->unwrap()->yieldBlock("exceptions", $context, $blocks);
            yield "
            ";
        }
        // line 321
        yield "
            ";
        // line 322
        if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 322, $this->source); })()), "tags", ["see"], "method", false, false, false, 322)) {
            // line 323
            yield "                <h4>";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("See also");
            yield "</h4>

                ";
            // line 325
            yield from             $this->unwrap()->yieldBlock("see", $context, $blocks);
            yield "
            ";
        }
        // line 327
        yield "
            ";
        // line 328
        if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 328, $this->source); })()), "hasExamples", [], "method", false, false, false, 328)) {
            // line 329
            yield "                <h4>";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("Examples");
            yield "</h4>

                ";
            // line 331
            yield from             $this->unwrap()->yieldBlock("examples", $context, $blocks);
            yield "
            ";
        }
        // line 333
        yield "        </div>
    </div>
";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "class.twig";
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
        return array (  1095 => 333,  1090 => 331,  1084 => 329,  1082 => 328,  1079 => 327,  1074 => 325,  1068 => 323,  1066 => 322,  1063 => 321,  1058 => 319,  1052 => 317,  1050 => 316,  1047 => 315,  1042 => 313,  1036 => 311,  1034 => 310,  1031 => 309,  1026 => 307,  1020 => 305,  1018 => 304,  1014 => 302,  1012 => 301,  1006 => 298,  1003 => 297,  998 => 295,  995 => 294,  989 => 292,  987 => 291,  981 => 288,  977 => 287,  969 => 284,  967 => 283,  962 => 280,  954 => 279,  949 => 278,  945 => 277,  939 => 274,  922 => 271,  919 => 270,  902 => 269,  899 => 268,  895 => 267,  889 => 264,  873 => 261,  868 => 259,  866 => 258,  863 => 256,  858 => 254,  852 => 252,  850 => 251,  842 => 250,  833 => 247,  829 => 245,  812 => 244,  809 => 243,  805 => 242,  799 => 239,  791 => 236,  786 => 234,  784 => 233,  780 => 231,  776 => 230,  773 => 229,  764 => 226,  762 => 225,  759 => 224,  752 => 223,  745 => 222,  738 => 221,  732 => 220,  727 => 219,  722 => 218,  717 => 217,  713 => 216,  709 => 215,  706 => 214,  702 => 213,  699 => 212,  695 => 211,  689 => 208,  679 => 204,  675 => 203,  671 => 201,  662 => 198,  660 => 197,  655 => 196,  648 => 195,  641 => 194,  637 => 193,  632 => 192,  630 => 191,  626 => 188,  622 => 187,  619 => 186,  615 => 185,  609 => 182,  600 => 179,  597 => 178,  591 => 176,  585 => 174,  583 => 173,  578 => 172,  576 => 171,  569 => 170,  567 => 169,  563 => 167,  559 => 166,  556 => 165,  552 => 164,  546 => 161,  538 => 158,  536 => 157,  533 => 155,  529 => 154,  526 => 153,  522 => 152,  516 => 149,  507 => 146,  503 => 145,  500 => 144,  496 => 143,  493 => 142,  489 => 141,  480 => 136,  476 => 135,  472 => 133,  468 => 132,  462 => 129,  453 => 126,  445 => 125,  439 => 124,  436 => 123,  432 => 122,  429 => 121,  425 => 120,  420 => 117,  416 => 116,  414 => 115,  410 => 114,  403 => 111,  398 => 110,  393 => 109,  388 => 108,  383 => 107,  378 => 106,  374 => 105,  370 => 104,  363 => 101,  353 => 98,  348 => 97,  346 => 96,  329 => 93,  327 => 92,  309 => 91,  306 => 90,  304 => 89,  298 => 87,  296 => 86,  293 => 85,  288 => 84,  283 => 83,  279 => 82,  275 => 81,  269 => 78,  264 => 76,  260 => 74,  254 => 72,  248 => 70,  246 => 69,  243 => 68,  238 => 66,  232 => 64,  230 => 63,  227 => 62,  222 => 60,  218 => 58,  216 => 57,  213 => 56,  208 => 54,  202 => 52,  200 => 51,  198 => 49,  194 => 47,  189 => 45,  186 => 44,  181 => 42,  179 => 41,  176 => 40,  174 => 39,  169 => 37,  165 => 36,  160 => 34,  157 => 33,  148 => 30,  146 => 29,  141 => 26,  135 => 25,  131 => 24,  129 => 23,  125 => 20,  121 => 19,  110 => 13,  108 => 12,  104 => 11,  100 => 9,  97 => 8,  93 => 7,  85 => 5,  77 => 4,  67 => 3,  62 => 1,  60 => 2,  53 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"layout/layout.twig\" %}
{% from \"macros.twig\" import render_classes, breadcrumbs, namespace_link, class_link, property_link, method_link, hint_link, source_link, method_source_link, deprecated, deprecations, internals, todo, todos, class_category_name %}
{% block title %}{{ class|raw }} | {{ parent() }}{% endblock %}
{% block body_class 'class' %}
{% block page_id 'class:' ~ (class.name|replace({'\\\\': '_'})) %}

{% block below_menu %}
    {% if class.namespace %}
        <div class=\"namespace-breadcrumbs\">
            <ol class=\"breadcrumb\">
                <li><span class=\"label label-default\">{{ class_category_name(class.getCategoryId()) }}</span></li>
                {{ breadcrumbs(class.namespace) -}}
                <li>{{ class.shortname|raw }}</li>
            </ol>
        </div>
    {% endif %}
{% endblock %}

{% block page_content %}

    <div class=\"page-header\">
        <h1>
            {{- class.name|split('\\\\')|last|raw -}}
            {{- deprecated(class) }}
            {% if class.isReadOnly() %}<small><span class=\"label label-primary\">{% trans 'read-only' %}</span></small>{% endif -%}
        </h1>
    </div>

    {% if class.hasSince() %}
        <i>{{ 'Since:'|trans }} {{ class.getSince() }}</i>
        <br>
    {% endif %}

    <p>{{ block('class_signature') }}</p>

    {{ deprecations(class) }}
    {{ internals(class) }}

    {% if class.shortdesc or class.longdesc %}
        <div class=\"description\">
            {% if class.shortdesc -%}
                <p>{{ class.shortdesc|desc(class)|md_to_html }}</p>
            {%- endif %}
            {% if class.longdesc -%}
                <p>{{ class.longdesc|desc(class)|md_to_html }}</p>
            {%- endif %}
        </div>
    {% endif %}
    {{- todos(class) -}}

    {% if traits %}
        <h2>{% trans 'Traits' %}</h2>

        {{ render_classes(traits) }}
    {% endif %}

    {% if constants %}
        <h2>{% trans 'Constants' %}</h2>

        {{- block('constants') }}
    {% endif %}

    {% if properties %}
        <h2>{% trans 'Properties' %}</h2>

        {{ block('properties') }}
    {% endif %}

    {% if methods %}
        <h2>{% trans 'Methods' %}</h2>

        {{ block('methods') }}

        <h2>{% trans 'Details' %}</h2>

        {{ block('methods_details') }}
    {% endif %}

{% endblock %}

{% block class_signature -%}
    {% if class.final %}final {% endif %}
    {% if not class.interface and class.abstract %}abstract {% endif %}
    {{ class_category_name(class.getCategoryId()) }}
    <strong>{{ class.shortname|raw }}</strong>
    {%- if class.parent %}
        {% trans 'extends' %} {{ class_link(class.parent) }}
    {%- endif %}
    {%- if class.interfaces|length > 0 %}
        {% trans 'implements' %}
        {% for interface in class.interfaces %}
            {{- class_link(interface) }}
            {%- if not loop.last %}, {% endif %}
        {%- endfor %}
    {%- endif %}
    {%- if class.hasMixins %}
        {% for mixin in class.getMixins() %}
            <i>mixin</i> {{ class_link(mixin.class) }}
        {% endfor %}
    {%- endif %}
    {{- source_link(project, class) }}
{% endblock %}

{% block method_signature -%}
    {% if method.final %}final{% endif %}
    {% if method.abstract %}abstract{% endif %}
    {% if method.static %}static{% endif %}
    {% if method.protected %}protected{% endif %}
    {% if method.private %}private{% endif %}
    {{ hint_link(method.hint, method.isIntersectionType()) }}
    <strong>{{ method.name|raw }}</strong>{{ block('method_parameters_signature') }}
{%- endblock %}

{% block method_parameters_signature -%}
    {%- from \"macros.twig\" import method_parameters_signature -%}
    {{ method_parameters_signature(method) }}
    {{ deprecated(method) }}
{%- endblock %}

{% block parameters %}
    <table class=\"table table-condensed\">
        {% for parameter in method.parameters %}
            <tr>
                <td>{% if parameter.hint %}{{ hint_link(parameter.hint, parameter.isIntersectionType()) }}{% endif %}</td>
                <td>{%- if parameter.variadic %}...{% endif %}\${{ parameter.name|raw }}</td>
                <td>{{ parameter.shortdesc|desc(class)|md_to_html }}</td>
            </tr>
        {% endfor %}
    </table>
{% endblock %}

{% block return %}
    <table class=\"table table-condensed\">
        <tr>
            <td>{{ hint_link(method.hint, method.isIntersectionType()) }}</td>
            <td>{{ method.hintDesc|desc(class)|md_to_html }}</td>
        </tr>
    </table>
{% endblock %}

{% block exceptions %}
    <table class=\"table table-condensed\">
        {% for exception in method.exceptions %}
            <tr>
                <td>{{ class_link(exception[0]) }}</td>
                <td>{{ exception[1]|desc(class)|md_to_html }}</td>
            </tr>
        {% endfor %}
    </table>
{% endblock %}

{% block examples %}
    <table class=\"table table-condensed\">
        {% for example in method.getExamples() %}
            <tr>
                <td><pre class=\"examples\">
                    {{- example|join(' ') -}}
                </pre></td>
            </tr>
        {% endfor %}
    </table>
{% endblock %}

{% block see %}
    <table class=\"table table-condensed\">
        {% for see in method.getSee() %}
            <tr>
                <td>
                    {% if see[4] %}
                        <a href=\"{{see[4]}}\">{{see[4]}}</a>
                    {% elseif see[3] %}
                        {{ method_link(see[3], false, false) }}
                    {% elseif see[2] %}
                        {{ class_link(see[2]) }}
                    {% else %}
                        {{ see[0]|raw }}
                    {% endif %}
                </td>
                <td>{{ see[1]|raw }}</td>
            </tr>
        {% endfor %}
    </table>
{% endblock %}

{% block constants %}
    <table class=\"table table-condensed\">
        {% for constant in constants %}
            <tr>
                <td>
                    {# Keep in order with an else if, it can be set by typehints and by annotations #}
                    {# More restricted wins #}
                    {% if constant.isPrivate() %}private
                    {% elseif constant.isProtected() %}protected{% endif %}
                    {% if constant.isInternal() %}<span class=\"label label-warning\">{% trans 'internal' %}</span>{% endif %}
                    {% if constant.isDeprecated() %}<span class=\"label label-danger\">{% trans 'deprecated' %}</span>{% endif %}
                    {{ constant.name|raw }}
                    {% if constant.hasSince() %}
                        <i>{{ 'Since:'|trans }} {{ constant.getSince() }}</i>
                        <br>
                    {% endif %}
                </td>
                <td class=\"last\">
                    <p><em>{{ constant.shortdesc|desc(class)|md_to_html }}</em></p>
                    <p>{{ constant.longdesc|desc(class)|md_to_html }}</p>
                </td>
            </tr>
        {% endfor %}
    </table>
{% endblock %}

{% block properties %}
    <table class=\"table table-condensed\">
        {% for property in properties %}
            <tr>
                <td class=\"type\" id=\"property_{{ property.name|raw }}\">
                    {% if property.isStatic() %}static{% endif %}
                    {% if property.isProtected() %}protected{% endif %}
                    {% if property.isPrivate() %}private{% endif %}
                    {{ hint_link(property.hint, property.isIntersectionType()) }}
                    {% if property.isInternal() %}<span class=\"label label-warning\">{% trans 'internal' %}</span>{% endif %}
                    {% if property.isDeprecated() %}<span class=\"label label-danger\">{% trans 'deprecated' %}</span>{% endif %}
                    {% if property.isReadOnly() %}<span class=\"label label-primary\">{% trans 'read-only' %}</span>{% endif %}
                    {% if property.isWriteOnly() %}<span class=\"label label-success\">{% trans 'write-only' %}</span>{% endif %}

                    {% if property.hasSince() %}
                        <i>{{ 'Since:'|trans }} {{ property.getSince() }}</i>
                        <br>
                    {% endif %}
                </td>
                <td>\${{ property.name|raw }}</td>
                <td class=\"last\">{{ property.shortdesc|desc(class)|md_to_html }}</td>
                <td>
                    {%- if property.class is not same as(class) -%}
                        <small>{{ 'from&nbsp;%s'|trans|format(property_link(property, false, true))|raw }}</small>
                    {%- endif -%}
                </td>
            </tr>
        {% endfor %}
    </table>
{% endblock %}

{% block methods %}
    <div class=\"container-fluid underlined\">
        {% for method in methods %}
            <div class=\"row\">
                <div class=\"col-md-2 type\">
                    {% if method.static %}static&nbsp;{% endif %}{{ hint_link(method.hint, method.isIntersectionType()) }}
                </div>
                <div class=\"col-md-8\">
                    <a href=\"#method_{{ method.name|raw }}\">{{ method.name|raw }}</a>{{ block('method_parameters_signature') }}
                    {% if not method.shortdesc %}
                        <p class=\"no-description\">{% trans 'No description' %}</p>
                    {% else %}
                        <p>{{ method.shortdesc|desc(class)|md_to_html }}</p>
                    {%- endif %}
                </div>
                <div class=\"col-md-2\">
                    {%- if method.class is not same as(class) -%}
                        <small>{{ 'from&nbsp;%s'|trans|format(method_link(method, false, true))|raw }}</small>
                    {%- endif -%}
                </div>
            </div>
        {% endfor %}
    </div>
{% endblock %}

{% block methods_details %}
    <div id=\"method-details\">
        {% for method in methods %}
            <div class=\"method-item\">
                {{ block('method') }}
            </div>
        {% endfor %}
    </div>
{% endblock %}

{% block method %}
    <h3 id=\"method_{{ method.name|raw }}\">
        <div class=\"location\">{% if method.class is not same as(class) %}{{ 'in %s'|trans|format(method_link(method, false, true))|raw }} {% endif %}{{ method_source_link(method) }}</div>
        <code>{{ block('method_signature') }}</code>
    </h3>
    <div class=\"details\">
        {%- if method.hasSince() -%}
            <i>{{ 'Since:'|trans }} {{ method.getSince() }}</i>
            <br>
        {%- endif -%}
        {{ deprecations(method) }}
        {{ internals(method) }}

        <div class=\"method-description\">
            {% if not method.shortdesc and not method.longdesc %}
                <p class=\"no-description\">{% trans 'No description' %}</p>
            {% else %}
                {% if method.shortdesc -%}
                <p>{{ method.shortdesc|desc(class)|md_to_html }}</p>
                {%- endif %}
                {% if method.longdesc -%}
                <p>{{ method.longdesc|desc(class)|md_to_html }}</p>
                {%- endif %}
            {%- endif %}
            {{- todos(method) -}}
        </div>
        <div class=\"tags\">
            {% if method.parameters %}
                <h4>{% trans 'Parameters' %}</h4>

                {{ block('parameters') }}
            {% endif %}

            {% if method.hintDesc or method.hint %}
                <h4>{% trans 'Return Value' %}</h4>

                {{ block('return') }}
            {% endif %}

            {% if method.exceptions %}
                <h4>{% trans 'Exceptions' %}</h4>

                {{ block('exceptions') }}
            {% endif %}

            {% if method.tags('see') %}
                <h4>{% trans 'See also' %}</h4>

                {{ block('see') }}
            {% endif %}

            {% if method.hasExamples() %}
                <h4>{% trans 'Examples' %}</h4>

                {{ block('examples') }}
            {% endif %}
        </div>
    </div>
{% endblock %}
", "class.twig", "/home/santi/backapp/vendor/code-lts/doctum/src/Resources/themes/default/class.twig");
    }
}
