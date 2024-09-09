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

/* macros.twig */
class __TwigTemplate_6261840ac2b36d81994a2412456ff9a2 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        yield "
";
        // line 7
        yield "
";
        // line 11
        yield "
";
        // line 21
        yield "
";
        // line 27
        yield "
";
        // line 33
        yield "
";
        // line 49
        yield "
";
        // line 55
        yield "
";
        // line 69
        yield "
";
        // line 81
        yield "
";
        // line 93
        yield "
";
        // line 115
        yield "
";
        // line 127
        yield "
";
        // line 131
        yield "
";
        // line 147
        yield "
";
        // line 161
        yield "
";
        // line 167
        yield "
";
        return; yield '';
    }

    // line 2
    public function macro_class_category_name($__categoryId__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "categoryId" => $__categoryId__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        return ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 3
            if (((isset($context["categoryId"]) || array_key_exists("categoryId", $context) ? $context["categoryId"] : (function () { throw new RuntimeError('Variable "categoryId" does not exist.', 3, $this->source); })()) == 1)) {
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("class");
            }
            // line 4
            if (((isset($context["categoryId"]) || array_key_exists("categoryId", $context) ? $context["categoryId"] : (function () { throw new RuntimeError('Variable "categoryId" does not exist.', 4, $this->source); })()) == 2)) {
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("interface");
            }
            // line 5
            if (((isset($context["categoryId"]) || array_key_exists("categoryId", $context) ? $context["categoryId"] : (function () { throw new RuntimeError('Variable "categoryId" does not exist.', 5, $this->source); })()) == 3)) {
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("trait");
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
    }

    // line 8
    public function macro_namespace_link($__namespace__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "namespace" => $__namespace__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        return ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 9
            yield "<a href=\"";
            yield $this->extensions['Doctum\Renderer\TwigExtension']->pathForNamespace($context, (isset($context["namespace"]) || array_key_exists("namespace", $context) ? $context["namespace"] : (function () { throw new RuntimeError('Variable "namespace" does not exist.', 9, $this->source); })()));
            yield "\">";
            ((((isset($context["namespace"]) || array_key_exists("namespace", $context) ? $context["namespace"] : (function () { throw new RuntimeError('Variable "namespace" does not exist.', 9, $this->source); })()) == "")) ? (yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Doctum\Tree::getGlobalNamespaceName(), "html", null, true)) : (yield (isset($context["namespace"]) || array_key_exists("namespace", $context) ? $context["namespace"] : (function () { throw new RuntimeError('Variable "namespace" does not exist.', 9, $this->source); })())));
            yield "</a>";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
    }

    // line 12
    public function macro_class_link($__class__ = null, $__absolute__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "class" => $__class__,
            "absolute" => $__absolute__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        return ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 13
            if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 13, $this->source); })()), "isProjectClass", [], "method", false, false, false, 13)) {
                // line 14
                yield "<a href=\"";
                yield $this->extensions['Doctum\Renderer\TwigExtension']->pathForClass($context, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 14, $this->source); })()));
                yield "\">";
            } elseif (CoreExtension::getAttribute($this->env, $this->source,             // line 15
(isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 15, $this->source); })()), "isPhpClass", [], "method", false, false, false, 15)) {
                // line 16
                yield "<a target=\"_blank\" rel=\"noopener\" href=\"https://www.php.net/";
                yield (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 16, $this->source); })());
                yield "\">";
            }
            // line 18
            yield $this->env->getFunction('abbr_class')->getCallable()((isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 18, $this->source); })()), ((array_key_exists("absolute", $context)) ? (Twig\Extension\CoreExtension::default((isset($context["absolute"]) || array_key_exists("absolute", $context) ? $context["absolute"] : (function () { throw new RuntimeError('Variable "absolute" does not exist.', 18, $this->source); })()), false)) : (false)));
            // line 19
            if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 19, $this->source); })()), "isProjectClass", [], "method", false, false, false, 19) || CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 19, $this->source); })()), "isPhpClass", [], "method", false, false, false, 19))) {
                yield "</a>";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
    }

    // line 22
    public function macro_method_link($__method__ = null, $__absolute__ = null, $__classonly__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "method" => $__method__,
            "absolute" => $__absolute__,
            "classonly" => $__classonly__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        return ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 23
            yield "<a href=\"";
            yield $this->extensions['Doctum\Renderer\TwigExtension']->pathForMethod($context, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 23, $this->source); })()));
            yield "\">
";
            // line 24
            yield $this->env->getFunction('abbr_class')->getCallable()(CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 24, $this->source); })()), "class", [], "any", false, false, false, 24));
            if ( !((array_key_exists("classonly", $context)) ? (Twig\Extension\CoreExtension::default((isset($context["classonly"]) || array_key_exists("classonly", $context) ? $context["classonly"] : (function () { throw new RuntimeError('Variable "classonly" does not exist.', 24, $this->source); })()), false)) : (false))) {
                yield "::";
                yield CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 24, $this->source); })()), "name", [], "any", false, false, false, 24);
            }
            // line 25
            yield "</a>";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
    }

    // line 28
    public function macro_property_link($__property__ = null, $__absolute__ = null, $__classonly__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "property" => $__property__,
            "absolute" => $__absolute__,
            "classonly" => $__classonly__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        return ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 29
            yield "<a href=\"";
            yield $this->extensions['Doctum\Renderer\TwigExtension']->pathForProperty($context, (isset($context["property"]) || array_key_exists("property", $context) ? $context["property"] : (function () { throw new RuntimeError('Variable "property" does not exist.', 29, $this->source); })()));
            yield "\">
";
            // line 30
            yield $this->env->getFunction('abbr_class')->getCallable()(CoreExtension::getAttribute($this->env, $this->source, (isset($context["property"]) || array_key_exists("property", $context) ? $context["property"] : (function () { throw new RuntimeError('Variable "property" does not exist.', 30, $this->source); })()), "class", [], "any", false, false, false, 30));
            if ( !((array_key_exists("classonly", $context)) ? (Twig\Extension\CoreExtension::default((isset($context["classonly"]) || array_key_exists("classonly", $context) ? $context["classonly"] : (function () { throw new RuntimeError('Variable "classonly" does not exist.', 30, $this->source); })()), false)) : (false))) {
                yield "#";
                yield CoreExtension::getAttribute($this->env, $this->source, (isset($context["property"]) || array_key_exists("property", $context) ? $context["property"] : (function () { throw new RuntimeError('Variable "property" does not exist.', 30, $this->source); })()), "name", [], "any", false, false, false, 30);
            }
            // line 31
            yield "</a>";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
    }

    // line 34
    public function macro_hint_link($__hints__ = null, $__isIntersectionType__ = false, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "hints" => $__hints__,
            "isIntersectionType" => $__isIntersectionType__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        return ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 35
            $macros["__internal_parse_1"] = $this;
            // line 37
            if ((isset($context["hints"]) || array_key_exists("hints", $context) ? $context["hints"] : (function () { throw new RuntimeError('Variable "hints" does not exist.', 37, $this->source); })())) {
                // line 38
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable((isset($context["hints"]) || array_key_exists("hints", $context) ? $context["hints"] : (function () { throw new RuntimeError('Variable "hints" does not exist.', 38, $this->source); })()));
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
                foreach ($context['_seq'] as $context["_key"] => $context["hint"]) {
                    // line 39
                    if (CoreExtension::getAttribute($this->env, $this->source, $context["hint"], "class", [], "any", false, false, false, 39)) {
                        // line 40
                        yield CoreExtension::callMacro($macros["__internal_parse_1"], "macro_class_link", [CoreExtension::getAttribute($this->env, $this->source, $context["hint"], "name", [], "any", false, false, false, 40)], 40, $context, $this->getSourceContext());
                    } elseif (CoreExtension::getAttribute($this->env, $this->source,                     // line 41
$context["hint"], "name", [], "any", false, false, false, 41)) {
                        // line 42
                        yield $this->env->getFunction('abbr_class')->getCallable()(CoreExtension::getAttribute($this->env, $this->source, $context["hint"], "name", [], "any", false, false, false, 42));
                    }
                    // line 44
                    if (CoreExtension::getAttribute($this->env, $this->source, $context["hint"], "array", [], "any", false, false, false, 44)) {
                        yield "[]";
                    }
                    // line 45
                    if ( !CoreExtension::getAttribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, false, 45)) {
                        if ((isset($context["isIntersectionType"]) || array_key_exists("isIntersectionType", $context) ? $context["isIntersectionType"] : (function () { throw new RuntimeError('Variable "isIntersectionType" does not exist.', 45, $this->source); })())) {
                            yield "&";
                        } else {
                            yield "|";
                        }
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
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['hint'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
    }

    // line 50
    public function macro_source_link($__project__ = null, $__class__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "project" => $__project__,
            "class" => $__class__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        return ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 51
            if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 51, $this->source); })()), "sourcepath", [], "any", false, false, false, 51)) {
                // line 52
                yield "        (<a href=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["class"]) || array_key_exists("class", $context) ? $context["class"] : (function () { throw new RuntimeError('Variable "class" does not exist.', 52, $this->source); })()), "sourcepath", [], "any", false, false, false, 52), "html", null, true);
                yield "\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("View source");
                yield "</a>)";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
    }

    // line 56
    public function macro_method_source_link($__method__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "method" => $__method__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        return ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 57
            if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 57, $this->source); })()), "sourcepath", [], "any", false, false, false, 57)) {
                // line 59
                yield "<a href=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 59, $this->source); })()), "sourcepath", [], "any", false, false, false, 59), "html", null, true);
                yield "\">";
                yield Twig\Extension\CoreExtension::sprintf(\Wdes\phpI18nL10n\Launcher::gettext("at line %s"), CoreExtension::getAttribute($this->env, $this->source,                 // line 60
(isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 60, $this->source); })()), "line", [], "any", false, false, false, 60));
                // line 61
                yield "</a>";
            } else {
                // line 64
                yield Twig\Extension\CoreExtension::sprintf(\Wdes\phpI18nL10n\Launcher::gettext("at line %s"), CoreExtension::getAttribute($this->env, $this->source,                 // line 65
(isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 65, $this->source); })()), "line", [], "any", false, false, false, 65));
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
    }

    // line 70
    public function macro_method_parameters_signature($__method__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "method" => $__method__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        return ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 71
            $macros["__internal_parse_2"] = $this->loadTemplate("macros.twig", "macros.twig", 71)->unwrap();
            // line 72
            yield "(";
            // line 73
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 73, $this->source); })()), "parameters", [], "any", false, false, false, 73));
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
            foreach ($context['_seq'] as $context["_key"] => $context["parameter"]) {
                // line 74
                if (CoreExtension::getAttribute($this->env, $this->source, $context["parameter"], "hashint", [], "any", false, false, false, 74)) {
                    yield CoreExtension::callMacro($macros["__internal_parse_2"], "macro_hint_link", [CoreExtension::getAttribute($this->env, $this->source, $context["parameter"], "hint", [], "any", false, false, false, 74), CoreExtension::getAttribute($this->env, $this->source, $context["parameter"], "isIntersectionType", [], "method", false, false, false, 74)], 74, $context, $this->getSourceContext());
                    yield " ";
                }
                // line 75
                if (CoreExtension::getAttribute($this->env, $this->source, $context["parameter"], "variadic", [], "any", false, false, false, 75)) {
                    yield "...";
                }
                yield "\$";
                yield CoreExtension::getAttribute($this->env, $this->source, $context["parameter"], "name", [], "any", false, false, false, 75);
                // line 76
                if ( !(null === CoreExtension::getAttribute($this->env, $this->source, $context["parameter"], "default", [], "any", false, false, false, 76))) {
                    yield " = ";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["parameter"], "default", [], "any", false, false, false, 76), "html", null, true);
                }
                // line 77
                if ( !CoreExtension::getAttribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, false, 77)) {
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['parameter'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 79
            yield ")";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
    }

    // line 82
    public function macro_function_parameters_signature($__method__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "method" => $__method__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        return ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 83
            $macros["__internal_parse_3"] = $this->loadTemplate("macros.twig", "macros.twig", 83)->unwrap();
            // line 84
            yield "(";
            // line 85
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["method"]) || array_key_exists("method", $context) ? $context["method"] : (function () { throw new RuntimeError('Variable "method" does not exist.', 85, $this->source); })()), "parameters", [], "any", false, false, false, 85));
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
            foreach ($context['_seq'] as $context["_key"] => $context["parameter"]) {
                // line 86
                if (CoreExtension::getAttribute($this->env, $this->source, $context["parameter"], "hashint", [], "any", false, false, false, 86)) {
                    yield CoreExtension::callMacro($macros["__internal_parse_3"], "macro_hint_link", [CoreExtension::getAttribute($this->env, $this->source, $context["parameter"], "hint", [], "any", false, false, false, 86), CoreExtension::getAttribute($this->env, $this->source, $context["parameter"], "isIntersectionType", [], "method", false, false, false, 86)], 86, $context, $this->getSourceContext());
                    yield " ";
                }
                // line 87
                if (CoreExtension::getAttribute($this->env, $this->source, $context["parameter"], "variadic", [], "any", false, false, false, 87)) {
                    yield "...";
                }
                yield "\$";
                yield CoreExtension::getAttribute($this->env, $this->source, $context["parameter"], "name", [], "any", false, false, false, 87);
                // line 88
                if ( !(null === CoreExtension::getAttribute($this->env, $this->source, $context["parameter"], "default", [], "any", false, false, false, 88))) {
                    yield " = ";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["parameter"], "default", [], "any", false, false, false, 88), "html", null, true);
                }
                // line 89
                if ( !CoreExtension::getAttribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, false, 89)) {
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['parameter'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 91
            yield ")";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
    }

    // line 94
    public function macro_render_classes($__classes__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "classes" => $__classes__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        return ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 95
            $macros["__internal_parse_4"] = $this;
            // line 96
            yield "
    <div class=\"container-fluid underlined\">
        ";
            // line 98
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable((isset($context["classes"]) || array_key_exists("classes", $context) ? $context["classes"] : (function () { throw new RuntimeError('Variable "classes" does not exist.', 98, $this->source); })()));
            foreach ($context['_seq'] as $context["_key"] => $context["class"]) {
                // line 99
                yield "            <div class=\"row\">
                <div class=\"col-md-6\">
                    ";
                // line 101
                if (CoreExtension::getAttribute($this->env, $this->source, $context["class"], "isInterface", [], "any", false, false, false, 101)) {
                    // line 102
                    yield "                        <em>";
                    yield CoreExtension::callMacro($macros["__internal_parse_4"], "macro_class_link", [$context["class"], true], 102, $context, $this->getSourceContext());
                    yield "</em>
                    ";
                } else {
                    // line 104
                    yield CoreExtension::callMacro($macros["__internal_parse_4"], "macro_class_link", [$context["class"], true], 104, $context, $this->getSourceContext());
                }
                // line 106
                yield CoreExtension::callMacro($macros["__internal_parse_4"], "macro_deprecated", [$context["class"]], 106, $context, $this->getSourceContext());
                // line 107
                yield "</div>
                <div class=\"col-md-6\">";
                // line 109
                yield $this->extensions['Doctum\Renderer\TwigExtension']->markdownToHtml($this->extensions['Doctum\Renderer\TwigExtension']->parseDesc(CoreExtension::getAttribute($this->env, $this->source, $context["class"], "shortdesc", [], "any", false, false, false, 109), $context["class"]));
                // line 110
                yield "</div>
            </div>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['class'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 113
            yield "    </div>";
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
    }

    // line 116
    public function macro_breadcrumbs($__namespace__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "namespace" => $__namespace__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        return ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 117
            yield "    ";
            $context["current_ns"] = "";
            // line 118
            yield "    ";
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(Twig\Extension\CoreExtension::split($this->env->getCharset(), (isset($context["namespace"]) || array_key_exists("namespace", $context) ? $context["namespace"] : (function () { throw new RuntimeError('Variable "namespace" does not exist.', 118, $this->source); })()), "\\"));
            foreach ($context['_seq'] as $context["_key"] => $context["ns"]) {
                // line 119
                if ((isset($context["current_ns"]) || array_key_exists("current_ns", $context) ? $context["current_ns"] : (function () { throw new RuntimeError('Variable "current_ns" does not exist.', 119, $this->source); })())) {
                    // line 120
                    $context["current_ns"] = (((isset($context["current_ns"]) || array_key_exists("current_ns", $context) ? $context["current_ns"] : (function () { throw new RuntimeError('Variable "current_ns" does not exist.', 120, $this->source); })()) . "\\") . $context["ns"]);
                } else {
                    // line 122
                    $context["current_ns"] = $context["ns"];
                }
                // line 124
                yield "<li><a href=\"";
                yield $this->extensions['Doctum\Renderer\TwigExtension']->pathForNamespace($context, (isset($context["current_ns"]) || array_key_exists("current_ns", $context) ? $context["current_ns"] : (function () { throw new RuntimeError('Variable "current_ns" does not exist.', 124, $this->source); })()));
                yield "\">";
                yield $context["ns"];
                yield "</a></li><li class=\"backslash\">\\</li>";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['ns'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
    }

    // line 128
    public function macro_deprecated($__reflection__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "reflection" => $__reflection__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        return ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 129
            yield "    ";
            if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["reflection"]) || array_key_exists("reflection", $context) ? $context["reflection"] : (function () { throw new RuntimeError('Variable "reflection" does not exist.', 129, $this->source); })()), "deprecated", [], "any", false, false, false, 129)) {
                yield "<small><span class=\"label label-danger\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("deprecated");
                yield "</span></small>";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
    }

    // line 132
    public function macro_deprecations($__reflection__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "reflection" => $__reflection__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        return ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 133
            yield "    ";
            $macros["__internal_parse_5"] = $this;
            // line 134
            yield "
    ";
            // line 135
            if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["reflection"]) || array_key_exists("reflection", $context) ? $context["reflection"] : (function () { throw new RuntimeError('Variable "reflection" does not exist.', 135, $this->source); })()), "deprecated", [], "any", false, false, false, 135)) {
                // line 136
                yield "        <p>
            ";
                // line 137
                yield CoreExtension::callMacro($macros["__internal_parse_5"], "macro_deprecated", [(isset($context["reflection"]) || array_key_exists("reflection", $context) ? $context["reflection"] : (function () { throw new RuntimeError('Variable "reflection" does not exist.', 137, $this->source); })())], 137, $context, $this->getSourceContext());
                yield "
            ";
                // line 138
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["reflection"]) || array_key_exists("reflection", $context) ? $context["reflection"] : (function () { throw new RuntimeError('Variable "reflection" does not exist.', 138, $this->source); })()), "deprecated", [], "any", false, false, false, 138));
                foreach ($context['_seq'] as $context["_key"] => $context["tag"]) {
                    // line 139
                    yield "                <tr>
                    <td>";
                    // line 140
                    yield CoreExtension::getAttribute($this->env, $this->source, $context["tag"], 0, [], "array", false, false, false, 140);
                    yield "</td>
                    <td>";
                    // line 141
                    yield Twig\Extension\CoreExtension::join(Twig\Extension\CoreExtension::slice($this->env->getCharset(), $context["tag"], 1, null), " ");
                    yield "</td>
                </tr>
            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tag'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 144
                yield "        </p>
    ";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
    }

    // line 148
    public function macro_internals($__reflection__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "reflection" => $__reflection__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        return ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 149
            yield "    ";
            if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["reflection"]) || array_key_exists("reflection", $context) ? $context["reflection"] : (function () { throw new RuntimeError('Variable "reflection" does not exist.', 149, $this->source); })()), "isInternal", [], "method", false, false, false, 149)) {
                // line 150
                yield "        ";
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["reflection"]) || array_key_exists("reflection", $context) ? $context["reflection"] : (function () { throw new RuntimeError('Variable "reflection" does not exist.', 150, $this->source); })()), "getInternal", [], "method", false, false, false, 150));
                foreach ($context['_seq'] as $context["_key"] => $context["internalTag"]) {
                    // line 151
                    yield "        <table>
            <tr>
                <td><span class=\"label label-warning\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("internal");
                    // line 153
                    yield "</span></td>
                <td>&nbsp;";
                    // line 154
                    yield CoreExtension::getAttribute($this->env, $this->source, $context["internalTag"], 0, [], "array", false, false, false, 154);
                    yield " ";
                    yield Twig\Extension\CoreExtension::join(Twig\Extension\CoreExtension::slice($this->env->getCharset(), $context["internalTag"], 1, null), " ");
                    yield "</td>
            </tr>
        </table>
        ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['internalTag'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 158
                yield "        &nbsp;
    ";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
    }

    // line 162
    public function macro_todo($__reflection__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "reflection" => $__reflection__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        return ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 163
            yield "        ";
            if ((CoreExtension::getAttribute($this->env, $this->source, (isset($context["project"]) || array_key_exists("project", $context) ? $context["project"] : (function () { throw new RuntimeError('Variable "project" does not exist.', 163, $this->source); })()), "config", ["insert_todos"], "method", false, false, false, 163) == true)) {
                // line 164
                yield "            ";
                if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["reflection"]) || array_key_exists("reflection", $context) ? $context["reflection"] : (function () { throw new RuntimeError('Variable "reflection" does not exist.', 164, $this->source); })()), "todo", [], "any", false, false, false, 164)) {
                    yield "<small><span class=\"label label-info\">";
yield \Wdes\phpI18nL10n\Launcher::getPlugin()->gettext("todo");
                    yield "</span></small>";
                }
                // line 165
                yield "        ";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
    }

    // line 168
    public function macro_todos($__reflection__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "reflection" => $__reflection__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        return ('' === $tmp = \Twig\Extension\CoreExtension::captureOutput((function () use (&$context, $macros, $blocks) {
            // line 169
            yield "        ";
            $macros["__internal_parse_6"] = $this;
            // line 170
            yield "
        ";
            // line 171
            if (CoreExtension::getAttribute($this->env, $this->source, (isset($context["reflection"]) || array_key_exists("reflection", $context) ? $context["reflection"] : (function () { throw new RuntimeError('Variable "reflection" does not exist.', 171, $this->source); })()), "todo", [], "any", false, false, false, 171)) {
                // line 172
                yield "            <p>
                ";
                // line 173
                yield CoreExtension::callMacro($macros["__internal_parse_6"], "macro_todo", [(isset($context["reflection"]) || array_key_exists("reflection", $context) ? $context["reflection"] : (function () { throw new RuntimeError('Variable "reflection" does not exist.', 173, $this->source); })())], 173, $context, $this->getSourceContext());
                yield "
                ";
                // line 174
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["reflection"]) || array_key_exists("reflection", $context) ? $context["reflection"] : (function () { throw new RuntimeError('Variable "reflection" does not exist.', 174, $this->source); })()), "todo", [], "any", false, false, false, 174));
                foreach ($context['_seq'] as $context["_key"] => $context["tag"]) {
                    // line 175
                    yield "                    <tr>
                        <td>";
                    // line 176
                    yield CoreExtension::getAttribute($this->env, $this->source, $context["tag"], 0, [], "array", false, false, false, 176);
                    yield "</td>
                        <td>";
                    // line 177
                    yield Twig\Extension\CoreExtension::join(Twig\Extension\CoreExtension::slice($this->env->getCharset(), $context["tag"], 1, null), " ");
                    yield "</td>
                        </tr>
                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tag'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 180
                yield "            </p>
        ";
            }
            return; yield '';
        })())) ? '' : new Markup($tmp, $this->env->getCharset());
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "macros.twig";
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
        return array (  813 => 180,  804 => 177,  800 => 176,  797 => 175,  793 => 174,  789 => 173,  786 => 172,  784 => 171,  781 => 170,  778 => 169,  766 => 168,  759 => 165,  752 => 164,  749 => 163,  737 => 162,  729 => 158,  717 => 154,  714 => 153,  709 => 151,  704 => 150,  701 => 149,  689 => 148,  681 => 144,  672 => 141,  668 => 140,  665 => 139,  661 => 138,  657 => 137,  654 => 136,  652 => 135,  649 => 134,  646 => 133,  634 => 132,  623 => 129,  611 => 128,  597 => 124,  594 => 122,  591 => 120,  589 => 119,  584 => 118,  581 => 117,  569 => 116,  563 => 113,  555 => 110,  553 => 109,  550 => 107,  548 => 106,  545 => 104,  539 => 102,  537 => 101,  533 => 99,  529 => 98,  525 => 96,  523 => 95,  511 => 94,  505 => 91,  489 => 89,  484 => 88,  478 => 87,  473 => 86,  456 => 85,  454 => 84,  452 => 83,  440 => 82,  434 => 79,  418 => 77,  413 => 76,  407 => 75,  402 => 74,  385 => 73,  383 => 72,  381 => 71,  369 => 70,  362 => 65,  361 => 64,  358 => 61,  356 => 60,  352 => 59,  350 => 57,  338 => 56,  327 => 52,  325 => 51,  312 => 50,  287 => 45,  283 => 44,  280 => 42,  278 => 41,  276 => 40,  274 => 39,  257 => 38,  255 => 37,  253 => 35,  240 => 34,  234 => 31,  228 => 30,  223 => 29,  209 => 28,  203 => 25,  197 => 24,  192 => 23,  178 => 22,  170 => 19,  168 => 18,  163 => 16,  161 => 15,  157 => 14,  155 => 13,  142 => 12,  132 => 9,  120 => 8,  112 => 5,  108 => 4,  104 => 3,  92 => 2,  86 => 167,  83 => 161,  80 => 147,  77 => 131,  74 => 127,  71 => 115,  68 => 93,  65 => 81,  62 => 69,  59 => 55,  56 => 49,  53 => 33,  50 => 27,  47 => 21,  44 => 11,  41 => 7,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("
{% macro class_category_name(categoryId) -%}
{% if categoryId == 1 %}{% trans 'class' %}{% endif %}
{% if categoryId == 2 %}{% trans 'interface' %}{% endif %}
{% if categoryId == 3 %}{% trans 'trait' %}{% endif %}
{%- endmacro %}

{% macro namespace_link(namespace) -%}
    <a href=\"{{ namespace_path(namespace) }}\">{{ namespace == '' ? global_namespace_name() : namespace|raw }}</a>
{%- endmacro %}

{% macro class_link(class, absolute) -%}
    {%- if class.isProjectClass() -%}
        <a href=\"{{ class_path(class) }}\">
    {%- elseif class.isPhpClass() -%}
        <a target=\"_blank\" rel=\"noopener\" href=\"https://www.php.net/{{ class|raw }}\">
    {%- endif %}
    {{- abbr_class(class, absolute|default(false)) }}
    {%- if class.isProjectClass() or class.isPhpClass() %}</a>{% endif %}
{%- endmacro %}

{% macro method_link(method, absolute, classonly) -%}
{#  #}<a href=\"{{ method_path(method) }}\">
{#    #}{{- abbr_class(method.class) }}{% if not classonly|default(false) %}::{{ method.name|raw }}{% endif -%}
{#  #}</a>
{%- endmacro %}

{% macro property_link(property, absolute, classonly) -%}
{#  #}<a href=\"{{ property_path(property) }}\">
{#    #}{{- abbr_class(property.class) }}{% if not classonly|default(false) %}#{{ property.name|raw }}{% endif -%}
{#  #}</a>
{%- endmacro %}

{% macro hint_link(hints, isIntersectionType = false) -%}
    {%- from _self import class_link %}

    {%- if hints %}
        {%- for hint in hints %}
            {%- if hint.class %}
                {{- class_link(hint.name) }}
            {%- elseif hint.name %}
                {{- abbr_class(hint.name) }}
            {%- endif %}
            {%- if hint.array %}[]{% endif %}
            {%- if not loop.last %}{%- if isIntersectionType %}&{% else %}|{% endif %}{% endif %}
        {%- endfor %}
    {%- endif %}
{%- endmacro %}

{% macro source_link(project, class) -%}
    {% if class.sourcepath %}
        (<a href=\"{{ class.sourcepath }}\">{% trans 'View source'%}</a>)
    {%- endif %}
{%- endmacro %}

{% macro method_source_link(method) -%}
    {% if method.sourcepath %}
        {#- l10n: Method at line %s -#}
        <a href=\"{{ method.sourcepath }}\">{{'at line %s'|trans|format(
            method.line
        )|raw }}</a>
    {%- else %}
        {#- l10n: Method at line %s -#}
        {{- 'at line %s'|trans|format(
            method.line
        )|raw -}}
    {%- endif %}
{%- endmacro %}

{% macro method_parameters_signature(method) -%}
    {%- from \"macros.twig\" import hint_link -%}
    (
        {%- for parameter in method.parameters %}
            {%- if parameter.hashint %}{{ hint_link(parameter.hint, parameter.isIntersectionType()) }} {% endif -%}
            {%- if parameter.variadic %}...{% endif %}\${{ parameter.name|raw }}
            {%- if parameter.default is not null %} = {{ parameter.default }}{% endif %}
            {%- if not loop.last %}, {% endif %}
        {%- endfor -%}
    )
{%- endmacro %}

{% macro function_parameters_signature(method) -%}
    {%- from \"macros.twig\" import hint_link -%}
    (
        {%- for parameter in method.parameters %}
            {%- if parameter.hashint %}{{ hint_link(parameter.hint, parameter.isIntersectionType()) }} {% endif -%}
            {%- if parameter.variadic %}...{% endif %}\${{ parameter.name|raw }}
            {%- if parameter.default is not null %} = {{ parameter.default }}{% endif %}
            {%- if not loop.last %}, {% endif %}
        {%- endfor -%}
    )
{%- endmacro %}

{% macro render_classes(classes) -%}
    {% from _self import class_link, deprecated %}

    <div class=\"container-fluid underlined\">
        {% for class in classes %}
            <div class=\"row\">
                <div class=\"col-md-6\">
                    {% if class.isInterface %}
                        <em>{{- class_link(class, true) -}}</em>
                    {% else %}
                        {{- class_link(class, true) -}}
                    {% endif %}
                    {{- deprecated(class) -}}
                </div>
                <div class=\"col-md-6\">
                    {{- class.shortdesc|desc(class)|md_to_html -}}
                </div>
            </div>
        {% endfor %}
    </div>
{%- endmacro %}

{% macro breadcrumbs(namespace) %}
    {% set current_ns = '' %}
    {% for ns in namespace|split('\\\\') %}
        {%- if current_ns -%}
            {% set current_ns = current_ns ~ '\\\\' ~ ns %}
        {%- else -%}
            {% set current_ns = ns %}
        {%- endif -%}
        <li><a href=\"{{ namespace_path(current_ns) }}\">{{ ns|raw }}</a></li><li class=\"backslash\">\\</li>
    {%- endfor %}
{% endmacro %}

{% macro deprecated(reflection) %}
    {% if reflection.deprecated %}<small><span class=\"label label-danger\">{% trans 'deprecated' %}</span></small>{% endif %}
{% endmacro %}

{% macro deprecations(reflection) %}
    {% from _self import deprecated %}

    {% if reflection.deprecated %}
        <p>
            {{ deprecated(reflection )}}
            {% for tag in reflection.deprecated %}
                <tr>
                    <td>{{ tag[0]|raw }}</td>
                    <td>{{ tag[1:]|join(' ')|raw }}</td>
                </tr>
            {% endfor %}
        </p>
    {% endif %}
{% endmacro %}

{% macro internals(reflection) %}
    {% if reflection.isInternal() %}
        {% for internalTag in reflection.getInternal() %}
        <table>
            <tr>
                <td><span class=\"label label-warning\">{% trans 'internal' %}</span></td>
                <td>&nbsp;{{ internalTag[0]|raw }} {{ internalTag[1:]|join(' ')|raw }}</td>
            </tr>
        </table>
        {% endfor %}
        &nbsp;
    {% endif %}
{% endmacro %}

{% macro todo(reflection) %}
        {% if project.config('insert_todos') == true %}
            {% if reflection.todo %}<small><span class=\"label label-info\">{% trans 'todo' %}</span></small>{% endif %}
        {% endif %}
{% endmacro %}

{% macro todos(reflection) %}
        {% from _self import todo %}

        {% if reflection.todo %}
            <p>
                {{ todo(reflection )}}
                {% for tag in reflection.todo %}
                    <tr>
                        <td>{{ tag[0]|raw }}</td>
                        <td>{{ tag[1:]|join(' ')|raw }}</td>
                        </tr>
                {% endfor %}
            </p>
        {% endif %}
{% endmacro %}
", "macros.twig", "/home/santi/backapp/vendor/code-lts/doctum/src/Resources/themes/default/macros.twig");
    }
}
