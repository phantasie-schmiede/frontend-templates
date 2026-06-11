<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use TYPO3Fluid\Fluid\Core\Parser\TemplateParser;

$parser = new TemplateParser();
$directories = [
    'Resources/Private',
    'ContentBlocks',
];

foreach ($directories as $directory) {
    if (!is_dir($directory)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->getExtension() !== 'html') {
            continue;
        }

        $template = file_get_contents($file->getPathname());

        if ($template === false) {
            throw new RuntimeException('Could not read template: ' . $file->getPathname());
        }

        try {
            $parser->parse($template);
        } catch (Throwable $exception) {
            throw new RuntimeException(
                $file->getPathname() . ' -> ' . $exception->getMessage(),
                0,
                $exception
            );
        }
    }
}
