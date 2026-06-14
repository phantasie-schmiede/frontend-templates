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
        '@PSR12'                      => true,
        'array_syntax'                => ['syntax' => 'short'],
        'declare_strict_types'        => true,
        'no_unused_imports'           => true,
        'ordered_imports'             => [
            'imports_order'  => [
                'class',
                'function',
                'const',
            ],
            'sort_algorithm' => 'alpha',
        ],
        'single_quote'                => true,
        'trailing_comma_in_multiline' => [
            'elements' => [
                'arrays',
            ],
        ],
    ])
    ->setFinder($finder);
