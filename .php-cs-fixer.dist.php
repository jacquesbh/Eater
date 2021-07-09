<?php

declare(strict_types=1);

$header = <<<'HEADER'
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

@author Jacques Bodin-Hullin <j.bodinhullin@monsieurbiz.com>
HEADER;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->exclude([
        'Migrations/',
    ]);

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@DoctrineAnnotation' => true,
    '@PHP71Migration' => true,
    '@PHP71Migration:risky' => true,
    '@PHPUnit60Migration:risky' => true,
    '@Symfony' => true,
    '@Symfony:risky' => true,
    'align_multiline_comment' => [
        'comment_type' => 'phpdocs_like',
    ],
    'array_indentation' => true,
    'array_syntax' => [
        'syntax' => 'short',
    ],
    'comment_to_phpdoc' => true,
    'compact_nullable_typehint' => true,
    'concat_space' => [
        'spacing' => 'one',
    ],
    'doctrine_annotation_array_assignment' => [
        'operator' => '=',
    ],
    'doctrine_annotation_spaces' => [
        'after_array_assignments_equals' => false,
        'before_array_assignments_equals' => false,
    ],
    'explicit_indirect_variable' => true,
    'fully_qualified_strict_types' => true,
    'function_declaration' => [
        'closure_function_spacing' => 'none',
    ],
    'header_comment' => [
        'header' => $header,
        'location' => 'after_open',
    ],
    'logical_operators' => true,
    'multiline_comment_opening_closing' => true,
    'multiline_whitespace_before_semicolons' => [
        'strategy' => 'new_line_for_chained_calls',
    ],
    'no_alternative_syntax' => true,
    'no_extra_blank_lines' => [
        'tokens' => [
            'break',
            'continue',
            'curly_brace_block',
            'extra',
            'parenthesis_brace_block',
            'return',
            'square_brace_block',
            'throw',
            'use',
        ],
    ],
    'no_superfluous_elseif' => true,
    'no_superfluous_phpdoc_tags' => false,
    'no_unset_cast' => true,
    'no_unset_on_property' => true,
    'no_useless_else' => true,
    'no_useless_return' => true,
    'ordered_imports' => [
        'imports_order' => [
            'class',
            'function',
            'const',
        ],
        'sort_algorithm' => 'alpha',
    ],
    'php_unit_method_casing' => [
        'case' => 'camel_case',
    ],
    'php_unit_set_up_tear_down_visibility' => true,
    'php_unit_test_annotation' => [
        'style' => 'prefix',
    ],
    'phpdoc_align' => [
        'align' => 'left',
    ],
    'phpdoc_add_missing_param_annotation' => [
        'only_untyped' => true,
    ],
    'phpdoc_order' => true,
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_to_comment' => false,
    'phpdoc_trim_consecutive_blank_line_separation' => true,
    'phpdoc_var_annotation_correct_order' => true,
    'return_assignment' => true,
    'strict_param' => true,
    'visibility_required' => [
        'elements' => [
            'const',
            'method',
            'property',
        ],
    ],
    'void_return' => true,
    ])
    ->setFinder($finder)
    ;
