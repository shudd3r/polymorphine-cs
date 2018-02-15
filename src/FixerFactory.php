<?php

/**
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
        '@PSR2'                                 => true,
        '@Symfony'                              => true,
        'single_line_after_imports'             => false,
        'braces'                                => ['allow_single_line_closure' => true],
        'cast_spaces'                           => true,
        'align_multiline_comment'               => true,
        'array_syntax'                          => ['syntax' => 'short'],
        'blank_line_before_statement'           => true,
        'combine_consecutive_issets'            => true,
        'combine_consecutive_unsets'            => true,
        'compact_nullable_typehint'             => true,
        'escape_implicit_backslashes'           => true,
        'explicit_indirect_variable'            => true,
        'explicit_string_variable'              => true,
        'final_internal_class'                  => true,
        'header_comment'                        => ['commentType' => 'PHPDoc', 'header' => '---HEADER---'],
        'heredoc_to_nowdoc'                     => true,
        'list_syntax'                           => ['syntax' => 'long'],
        'method_chaining_indentation'           => true,
        'method_argument_space'                 => ['ensure_fully_multiline' => true],
        'multiline_comment_opening_closing'     => true,
        'no_extra_blank_lines'                  => [
            'tokens' => [
                'break',
                'continue',
                'extra',
                'return',
                'throw',
                'parenthesis_brace_block',
                'square_brace_block',
                'curly_brace_block'
            ]
        ],
        'no_null_property_initialization'       => true,
        'no_short_echo_tag'                     => true,
        'no_superfluous_elseif'                 => true,
        'no_unneeded_curly_braces'              => true,
        'no_unneeded_final_method'              => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else'                       => true,
        'no_useless_return'                     => true,
        'ordered_class_elements'                => true,
        'ordered_imports'                       => false,
        'php_unit_strict'                       => true,
        'php_unit_test_annotation'              => true,
        'php_unit_test_class_requires_covers'   => true,
        'phpdoc_add_missing_param_annotation'   => true,
        'phpdoc_order'                          => true,
        'phpdoc_types_order'                    => true,
        'semicolon_after_instruction'           => true,
        'single_line_comment_style'             => true,
        'strict_comparison'                     => true,
        'strict_param'                          => true,
        'yoda_style'                            => null,
        'Polymorphine/double_line_after_imports' => true,
        'Polymorphine/brace_after_method'       => true
    ];

    public static function createFor(string $packageName, string $workingDir) {
        self::$rules['header_comment']['header'] = str_replace('{{name}}', $packageName, self::HEADER);

        return PhpCsFixer\Config::create()
            ->setRiskyAllowed(true)
            ->setRules(self::$rules)
            ->setFinder(PhpCsFixer\Finder::create()->in($workingDir))
            ->setUsingCache(false)
            ->registerCustomFixers([
                new DoubleLineAfterImportsFixer(),
                new BraceAfterFunctionFixer()
            ]);
    }
}
