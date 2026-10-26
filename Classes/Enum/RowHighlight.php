<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\Enum;

/**
 * Class AiStatus
 *
 * @package PSBits\Templates\Enum
 */
enum RowHighlight: string
{
    public const string BACKEND_LL = 'LLL:EXT:frontend_templates/Resources/Private/Language/Backend/Enum/rowHighlight.xlf:';

    case Emphasis = 'emphasis';
    case None     = 'none';
    case Subtle   = 'subtle';

    public function getBackendLabel(): string
    {
        return self::BACKEND_LL . $this->value;
    }
}
