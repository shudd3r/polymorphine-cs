<?php

/*
 * This file is part of Polymorphine/CodeStandards package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Polymorphine\CodeStandards;

use PhpCsFixer;


class FixerFactory
{
    const HEADER = <<<'EOF'
This file is part of {{name}} package.

(c) Shudd3r <q3.shudder@gmail.com>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

    protected static $rules = [
        '@PSR1'                                 => true,
        '@PSR2'                                 => true,
        '@Symfony'                              => true,
        'single_line_after_imports'             => false,
        'braces'                                => ['allow_single_line_closure' => true],
        'backtick_to_shell_exec'                => true,
        'binary_operator_spaces'                => true,
        'blank_line_before_statement'           => false,
        'cast_spaces'                           => true,
        'align_multiline_comment'               => true,
        'array_syntax'                          => ['syntax' => 'short'],
        'combine_consecutive_issets'            => true,
        'combine_consecutive_unsets'            => true,
        'compact_nullable_typehint'             => true,
        'concat_space'                          => ['spacing' => 'one'],
        'escape_implicit_backslashes'           => true,
        'explicit_indirect_variable'            => true,
        'explicit_string_variable'              => false,
        'final_internal_class'                  => true,
        'function_to_constant'                  => true,
        'header_comment'                        => ['commentType' => 'comment'],
        'heredoc_to_nowdoc'                     => true,
        'increment_style'                       => false,
        'list_syntax'                           => ['syntax' => 'short'],
        'method_chaining_indentation'           => false,
        'method_argument_space'                 => ['ensure_fully_multiline' => true],
        'modernize_types_casting'               => true,
        'multiline_comment_opening_closing'     => true,
        'no_extra_blank_lines'                  => [],
        'no_homoglyph_names'                    => true,
        'no_null_property_initialization'       => true,
        'no_php4_constructor'                   => true,
        'no_short_echo_tag'                     => false,
        'no_superfluous_elseif'                 => true,
        'no_superfluous_phpdoc_tags'            => false,
        'no_unneeded_curly_braces'              => true,
        'no_unneeded_final_method'              => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else'                       => true,
        'no_useless_return'                     => true,
        'non_printable_character'               => ['use_escape_sequences_in_strings' => true],
        'ordered_class_elements'                => true,
        'ordered_imports'                       => false,
        'php_unit_strict'                       => false,
        'php_unit_namespaced'                   => true,
        'php_unit_test_annotation'              => false,
        'php_unit_test_class_requires_covers'   => false,
        'phpdoc_add_missing_param_annotation'   => true,
        'phpdoc_order'                          => true,
        'phpdoc_types_order'                    => true,
        'pow_to_exponentiation'                 => true,
        'psr4'                                  => true,
        'semicolon_after_instruction'           => true,
        'simplified_null_return'                => false,
        'single_line_comment_style'             => true,
        'strict_comparison'                     => true,
        'strict_param'                          => true,
        'ternary_to_null_coalescing'            => true,
        'trailing_comma_in_multiline_array'     => false,
        'yoda_style'                            => false
    ];

    public static function createFor(string $packageName, string $workingDir)
    {
        self::$rules['header_comment']['header'] = str_replace('{{name}}', $packageName, self::HEADER);
        self::$rules['no_extra_blank_lines']['tokens'] = [
            'break', 'continue', 'extra', 'return', 'throw', 'parenthesis_brace_block',
            'square_brace_block', 'curly_brace_block'
        ];

        self::$rules['Polymorphine/double_line_before_class_definition']     = true;
        self::$rules['Polymorphine/no_trailing_comma_after_multiline_array'] = true;
        self::$rules['Polymorphine/constructors_first']                      = true;
        self::$rules['Polymorphine/aligned_method_chain']                    = true;
        self::$rules['Polymorphine/aligned_assignments']                     = true;
        self::$rules['Polymorphine/aligned_array_values']                    = true;
        self::$rules['Polymorphine/short_conditions_single_line']            = true;

        return PhpCsFixer\Config::create()
            ->setRiskyAllowed(true)
            ->setRules(self::$rules)
            ->setFinder(PhpCsFixer\Finder::create()->in($workingDir))
            ->setUsingCache(false)
            ->registerCustomFixers([
                new Fixer\DoubleLineBeforeClassDefinitionFixer(),
                new Fixer\NoTrailingCommaInMultilineArrayFixer(),
                new Fixer\ConstructorsFirstFixer(),
                new Fixer\AlignedMethodChainFixer(),
                new Fixer\AlignedAssignmentsFixer(),
                new Fixer\AlignedArrayValuesFixer(),
                new Fixer\ShortConditionsSingleLineFixer()
            ]);
    }
}
