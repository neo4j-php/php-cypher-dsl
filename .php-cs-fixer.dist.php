<?php

/*
 * Cypher DSL
 * Copyright (C) 2021  Wikibase Solutions
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude([
        '.github',
        'vendor'
    ])
    ->name('*php');

$header = <<<'COPYRIGHT'
This file is part of php-cypher-dsl.

Copyright (C) 2021-  Wikibase Solutions

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
COPYRIGHT;

return (new Config())
	->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        'align_multiline_comment' => true,
        'array_indentation' => true,
        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'continue',
                'declare',
                'do',
                'for',
                'foreach',
                'if',
                'include',
                'include_once',
                'require',
                'require_once',
                'return',
                'switch',
                'throw',
                'try',
                'while',
                'yield',
            ],
        ],
		'compact_nullable_typehint' => true,
        'concat_space' => ['spacing' => 'one'],
		'declare_strict_types' => true,
		'declare_equal_normalize' => true,
        'fully_qualified_strict_types' => true,
		'global_namespace_import' => [
			'import_classes' => true,
			'import_constants' => false,
			'import_functions' => false
		],
        'header_comment' => [
            'header' => $header,
			'separate' => 'none'
        ],
        'list_syntax' => [
            'syntax' => 'short',
        ],
		'logical_operators' => true,
		'no_superfluous_phpdoc_tags' => true,
		'no_unset_cast' => true,
        'no_unused_imports' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
		'ordered_interfaces' => true,
		'ordered_traits' => true,
        'phpdoc_align' => [
            'align' => 'left',
        ],
        'phpdoc_scalar' => true,
		'phpdoc_order_by_value' => [
			'annotations' => ['covers'],
		],
		'phpdoc_no_empty_return' => true,
		'phpdoc_order' => true,
		'php_unit_dedicate_assert' => true,
		'php_unit_set_up_tear_down_visibility' => true,
        'php_unit_method_casing' => [
            'case' => 'camel_case',
        ],
		'php_unit_test_annotation' => [
			'style' => 'prefix',
		],
		'php_unit_test_case_static_method_calls' => [
			'call_type' => 'this',
		],
        'return_assignment' => true,
        'self_static_accessor' => true,
		'single_line_throw' => true,
        'simplified_if_return' => true,
		'static_lambda' => true,
		'strict_comparison' => true,
		'strict_param' => true,
        'trailing_comma_in_multiline' => true,
		'yoda_style' => [
			'equal' => false,
			'identical' => false,
			'less_and_greater' => false,
		]
    ])
    ->setFinder($finder);
