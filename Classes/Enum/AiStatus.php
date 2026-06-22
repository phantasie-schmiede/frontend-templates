<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\Enum;

use JsonException;
use PSBits\Foundation\Utility\LocalizationUtility;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;

/**
 * Class AiStatus
 *
 * @package PSBits\Templates\Enum
 */
enum AiStatus: string
{
    public const string FRONTEND_LL = 'LLL:EXT:templates/Resources/Private/Language/Frontend/Enum/aiStatus.xlf:';

    case Created  = 'created';
    case Modified = 'modified';
    case None     = 'none';

    /**
     * @throws AspectNotFoundException
     * @throws ContainerExceptionInterface
     * @throws JsonException
     * @throws NotFoundExceptionInterface
     */
    public function getLabel(): string
    {
        return LocalizationUtility::translate(self::FRONTEND_LL . $this->value);
    }
}
