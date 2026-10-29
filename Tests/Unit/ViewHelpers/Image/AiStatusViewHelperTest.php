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
use PHPUnit\Framework\MockObject\MockObject;
use PSBits\FrontendTemplates\Enum\AiStatus;
use PSBits\FrontendTemplates\ViewHelpers\Image\AiStatusViewHelper;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileReference as CoreFileReference;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class AiStatusViewHelperTest
 *
 * @package PSBits\FrontendTemplates\Tests\Unit\ViewHelpers\Image
 */
class AiStatusViewHelperTest extends UnitTestCase
{
    private AiStatusViewHelper            $subject;
    private RenderingContext&MockObject   $renderingContext;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject          = new AiStatusViewHelper();
        $this->renderingContext = $this->createMock(RenderingContext::class);
        $this->subject->setRenderingContext($this->renderingContext);
        $this->subject->initializeArguments();
    }

    #[Test]
    public function aMissingFileYieldsNoStatus(): void
    {
        self::assertNull($this->render([]));
    }

    #[Test]
    public function theStatusIsReadFromTheOriginalResourceOfAFileReference(): void
    {
        $file = $this->createMock(CoreFileReference::class);
        $file->method('getProperty')
            ->with('tx_templates_ai_status')
            ->willReturn('created');

        $fileReference = $this->createMock(FileReference::class);
        $fileReference->method('getOriginalResource')
            ->willReturn($file);

        self::assertSame(AiStatus::Created, $this->render(['file' => $fileReference]));
    }

    #[Test]
    public function rawFilesAreAcceptedAsWell(): void
    {
        $file = $this->createMock(File::class);
        $file->method('getProperty')
            ->with('tx_templates_ai_status')
            ->willReturn('modified');

        self::assertSame(AiStatus::Modified, $this->render(['file' => $file]));
    }

    #[Test]
    public function anUnsetStatusYieldsNull(): void
    {
        $file = $this->createMock(File::class);
        $file->method('getProperty')
            ->with('tx_templates_ai_status')
            ->willReturn(null);

        self::assertNull($this->render(['file' => $file]));
    }

    #[Test]
    public function anUnknownStatusYieldsNull(): void
    {
        $file = $this->createMock(File::class);
        $file->method('getProperty')
            ->with('tx_templates_ai_status')
            ->willReturn('broken');

        self::assertNull($this->render(['file' => $file]));
    }

    /**
     * @param array<string, mixed> $arguments
     */
    private function render(array $arguments): mixed
    {
        $defaults = [];

        foreach ($this->subject->prepareArguments() as $name => $definition) {
            $defaults[$name] = $definition->getDefaultValue();
        }

        $this->subject->setArguments($arguments + $defaults);
        $this->subject->setRenderChildrenClosure(static fn() => null);

        return $this->subject->render();
    }
}
