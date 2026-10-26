<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\ViewHelpers\Image;

use PSBits\Templates\Enum\AiStatus;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class AiStatusViewHelper
 *
 * @package PSBits\Templates\ViewHelpers\Image
 */
class AiStatusViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('file', 'mixed', 'TYPO3 file reference to read AI status from.');
    }

    public function render(): ?AiStatus
    {
        $file = $this->arguments['file'];

        if (null === $file) {
            return null;
        }

        if ($file instanceof FileReference) {
            $file = $file->getOriginalResource();
        }

        $aiStatus = $file->getProperty('tx_templates_ai_status');

        if (null === $aiStatus) {
            return null;
        }

        return AiStatus::tryFrom($aiStatus);
    }
}
