<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\Enum;

use PSBits\Foundation\Utility\LocalizationUtility;

/**
 * Class AiStatus
 *
 * @package PSBits\Templates\Enum
 */
enum AiStatus: string
{
    public const FRONTEND_LL = 'LLL:EXT:templates/Resources/Private/Language/Frontend/Enum/aiStatus.xlf:';

    case None = 'none';
    case Modified = 'modified';
    case Created = 'created';

    public function getFrontendLabel(): string
    {
        return LocalizationUtility::translate(self::FRONTEND_LL . $this->value);
    }

    public function getLabel(): string
    {
        return $this->getFrontendLabel();
    }
}
