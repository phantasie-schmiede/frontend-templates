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
use PSBits\FrontendTemplates\ViewHelpers\Image\SourceSetViewHelper;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Extbase\Service\ImageService;

class SourceSetViewHelperTest extends TestCase
{
    #[Test]
    public function candidatesAreLimitedToTheCropAndUseProcessedWidths(): void
    {
        $file = $this->createMock(File::class);
        $file->method('hasProperty')->willReturn(true);
        $file->method('getProperty')->willReturnMap([
            ['crop', json_encode(['mobile' => ['cropArea' => ['x' => 0, 'y' => 0, 'width' => 0.5, 'height' => 1]]])],
            ['width', 800],
            ['height', 600],
        ]);
        $processed = $this->createMock(ProcessedFile::class);
        $processed->method('getProperty')->with('width')->willReturn(399);
        $service = $this->createMock(ImageService::class);
        $service->method('getImage')->willReturn($file);
        $service->expects(self::once())->method('applyProcessingInstructions')
            ->with($file, self::callback(static fn(array $instructions): bool => 400 === $instructions['maxWidth'] && null !== $instructions['crop']))
            ->willReturn($processed);
        $service->method('getImageUri')->willReturn('/processed.avif');

        $subject = new SourceSetViewHelper($service);
        $subject->initializeArguments();
        $subject->setArguments(['cropVariant' => 'mobile', 'file' => $file, 'fileExtension' => 'avif', 'widths' => [800, 400, 1200]]);

        self::assertSame([['uri' => '/processed.avif', 'width' => 399]], $subject->render());
    }
}
