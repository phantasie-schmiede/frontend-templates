<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/../../Classes',
        __DIR__ . '/../../Configuration',
    ])
    ->name('*.php');

return (new PhpCsFixer\Config())->setRiskyAllowed(true)
    ->setRules([
        '@PSR12'                          => true,
        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'continue',
                'declare',
                'do',
                'exit',
                'for',
                'foreach',
                'goto',
                'if',
                'return',
                'switch',
                'throw',
                'try',
                'while',
            ],
        ],
        'class_attributes_separation'     => [
            'elements' => [
                'case'         => 'none',
                'const'        => 'only_if_meta',
                'method'       => 'one',
                'property'     => 'only_if_meta',
                'trait_import' => 'only_if_meta',
            ],
        ],
        'declare_strict_types'            => true,
        'no_extra_blank_lines'            => [
            'tokens' => [
                'attribute',
                'break',
                'case',
                'comma',
                'continue',
                'curly_brace_block',
                'default',
                'extra',
                'parenthesis_brace_block',
                'return',
                'square_brace_block',
                'switch',
                'throw',
                'use',
            ],
        ],
        'no_unneeded_control_parentheses' => true,
        'ordered_imports'                 => [
            'imports_order'  => [
                'class',
                'function',
                'const',
            ],
            'sort_algorithm' => 'alpha',
        ],
        'single_quote'                    => true,
        'trailing_comma_in_multiline'     => [
            'elements' => [
                'arrays',
            ],
        ],
    ])
    ->setFinder($finder);
