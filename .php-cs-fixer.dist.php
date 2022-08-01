<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
	->in([__DIR__ . '/src', __DIR__ . '/tests'])
	->name('*php');

return (new Config())
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
		'concat_space' => ['spacing' => 'one'],
		'fully_qualified_strict_types' => true,
		'list_syntax' => [
			'syntax' => 'short',
		],
		'no_unused_imports' => true,
		'no_useless_else' => true,
		'no_useless_return' => true,
		'nullable_type_declaration_for_default_null_value' => true,
		'ordered_class_elements' => true,
		'ordered_imports' => true,
		'phpdoc_align' => [
			'align' => 'left',
		],
		'phpdoc_scalar' => true,
		'php_unit_method_casing' => [
			'case' => 'snake_case',
		],
		'phpdoc_order_by_value' => [
			'annotations' => ['covers'],
		],
		'phpdoc_no_empty_return' => true,
		'phpdoc_order' => true,
		'return_assignment' => true,
		'self_static_accessor' => true,
		'simplified_if_return' => true,
		'trailing_comma_in_multiline' => true
	])
	->setFinder($finder)
	->setCacheFile(__DIR__ . '/.php-cs-fixer.cache')
	->setUsingCache(true);