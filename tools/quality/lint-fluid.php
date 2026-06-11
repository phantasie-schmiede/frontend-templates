<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use TYPO3Fluid\Fluid\Core\Parser\TemplateParser;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperResolver;

$parser = new TemplateParser();
$renderingContext = new RenderingContext();
$renderingContext->setViewHelperResolver(
    new class () extends ViewHelperResolver {
        public function createViewHelperInstanceFromClassName(string $viewHelperClassName): ViewHelperInterface
        {
            $reflectionClass = new ReflectionClass($viewHelperClassName);
            $constructor = $reflectionClass->getConstructor();

            if (null === $constructor || 0 === $constructor->getNumberOfRequiredParameters()) {
                return parent::createViewHelperInstanceFromClassName($viewHelperClassName);
            }

            return $reflectionClass->newInstanceWithoutConstructor();
        }
    }
);
$parser->setRenderingContext($renderingContext);
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
