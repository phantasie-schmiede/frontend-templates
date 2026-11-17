<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\ViewHelpers\Image;

use JsonException;
use TYPO3\CMS\Core\Imaging\ImageManipulation\Area;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

use function is_array;
use function json_encode;
use function method_exists;

class AspectRatioViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument(
            'crop',
            'mixed',
            'Crop data as JSON string or array. If not provided, crop data from the image will be used if available.'
        );
        $this->registerArgument('cropVariant', 'string', 'Crop variant identifier.', false, 'default');
        $this->registerArgument('fallback', 'float', 'Fallback ratio when no ratio can be resolved.', false, 1.0);
        $this->registerArgument('file', 'mixed', 'TYPO3 file reference to read crop data from.');
    }

    /**
     * @throws JsonException
     */
    public function render(): float
    {
        $file = $this->normalizeFileReference($this->arguments['file']);

        if (null === $file) {
            return $this->arguments['fallback'];
        }

        $cropArea = $this->getCropArea($file);
        $height   = (float)$file->getProperty('height') * $cropArea->getHeight();
        $width    = (float)$file->getProperty('width') * $cropArea->getWidth();

        if (0 >= $height || 0 >= $width) {
            return $this->arguments['fallback'];
        }

        return $width / $height;
    }

    /**
     * @throws JsonException
     */
    private function getCropArea(FileInterface $file): Area
    {
        $crop = $this->arguments['crop'];

        if (
            null === $crop
            && $file->hasProperty('crop')
            && $file->getProperty('crop')
        ) {
            $crop = $file->getProperty('crop');
        }

        if (is_array($crop)) {
            $crop = json_encode($crop, JSON_THROW_ON_ERROR);
        }

        $cropVariantCollection = CropVariantCollection::create((string)$crop);
        $cropVariant           = $this->arguments['cropVariant'] ?: 'default';

        return $cropVariantCollection->getCropArea($cropVariant);
    }

    private function normalizeFileReference(mixed $file): ?FileInterface
    {
        if (null === $file) {
            return null;
        }

        if (method_exists($file, 'getOriginalResource')) {
            $originalResource = $file->getOriginalResource();

            if (null !== $originalResource) {
                return $originalResource;
            }
        }

        return $file;
    }
}
