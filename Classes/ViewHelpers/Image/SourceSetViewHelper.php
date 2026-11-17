<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\ViewHelpers\Image;

use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Process candidates once per actual width and expose their real dimensions.
 */
class SourceSetViewHelper extends AbstractViewHelper
{
    public function __construct(private readonly ImageService $imageService)
    {
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('cropVariant', 'string', 'Crop variant identifier.', false, 'desktop');
        $this->registerArgument('file', 'mixed', 'TYPO3 image or file reference.', true);
        $this->registerArgument('fileExtension', 'string', 'Image format.', false, 'avif');
        $this->registerArgument('widths', 'array', 'Requested candidate widths.', true);
    }

    /**
     * @return array<int, array{uri: string, width: int}>
     */
    public function render(): array
    {
        $file = $this->imageService->getImage('', $this->arguments['file'], false);
        $crop = CropVariantCollection::create((string)($file->hasProperty('crop') ? $file->getProperty('crop') : ''))
            ->getCropArea($this->arguments['cropVariant']);
        $availableWidth = (int)floor((float)$file->getProperty('width') * $crop->getWidth());
        $widths = array_map(static fn ($width): int => min((int)$width, $availableWidth), $this->arguments['widths']);
        $widths = array_unique(array_filter($widths, static fn (int $width): bool => 0 < $width));
        sort($widths);

        $candidates = [];
        foreach ($widths as $width) {
            $image = $this->imageService->applyProcessingInstructions($file, [
                'crop' => $crop->isEmpty() ? null : $crop->makeAbsoluteBasedOnFile($file),
                'fileExtension' => $this->arguments['fileExtension'],
                'maxWidth' => $width,
            ]);
            $actualWidth = (int)$image->getProperty('width');
            $candidates[$actualWidth] = [
                'uri' => $this->imageService->getImageUri($image),
                'width' => $actualWidth,
            ];
        }

        return array_values($candidates);
    }
}
