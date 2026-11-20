<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Unit\ViewHelpers\Image;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PSBits\FrontendTemplates\ViewHelpers\Image\AspectRatioViewHelper;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileReference as CoreFileReference;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;

class AspectRatioViewHelperTest extends TestCase
{
    #[Test]
    public function cropGeometryAndExtbaseReferencesAreSupported(): void
    {
        $file = $this->createMock(CoreFileReference::class);
        $file->method('getProperty')->willReturnMap([['width', 1600], ['height', 800]]);
        $reference = $this->createMock(FileReference::class);
        $reference->method('getOriginalResource')->willReturn($file);

        self::assertSame(1.0, $this->render([
            'crop'        => ['mobile' => ['cropArea' => ['height' => 1, 'width' => 0.5, 'x' => 0, 'y' => 0]]],
            'cropVariant' => 'mobile',
            'file'        => $reference,
        ]));
    }

    #[Test]
    public function missingDimensionsAndFilesUseTheFallback(): void
    {
        self::assertSame(1.5, $this->render(['fallback' => 1.5]));
        $file = $this->createMock(File::class);
        $file->method('getProperty')->willReturn(0);
        self::assertSame(1.5, $this->render(['fallback' => 1.5, 'file' => $file]));
    }

    #[Test]
    public function rawFileDimensionsAreUsedWithoutImageProcessing(): void
    {
        $file = $this->createMock(File::class);
        $file->method('getProperty')->willReturnMap([['width', 1920], ['height', 1080]]);

        self::assertEqualsWithDelta(16 / 9, $this->render(['file' => $file]), 0.00001);
    }

    private function render(array $arguments): float
    {
        $subject = new AspectRatioViewHelper();
        $subject->initializeArguments();
        $subject->setArguments($arguments + ['crop' => null, 'cropVariant' => 'default', 'fallback' => 1.0, 'file' => null]);

        return $subject->render();
    }
}
