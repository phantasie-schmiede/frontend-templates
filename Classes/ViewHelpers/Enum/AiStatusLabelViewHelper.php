<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\ViewHelpers\Enum;

use PSBits\Templates\Enum\AiStatus;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use function is_string;

/**
 * Class AiStatusLabelViewHelper
 *
 * @package PSBits\Templates\ViewHelpers\Enum
 */
class AiStatusLabelViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('status', 'mixed', 'AI status enum or value.', false, null);
    }

    public function render(): string
    {
        $status = $this->arguments['status'];

        if ($status instanceof AiStatus) {
            if (AiStatus::None === $status) {
                return '';
            }

            return $status->getLabel();
        }

        if (!is_string($status) || '' === $status) {
            return '';
        }

        $status = AiStatus::tryFrom($status);
        if (null === $status || AiStatus::None === $status) {
            return '';
        }

        return $status->getLabel();
    }
}
