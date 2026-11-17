<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Enum;

use InvalidArgumentException;
use JsonException;
use PSBits\Foundation\Utility\LocalizationUtility;
use Psr\Container\ContainerExceptionInterface;
use ReflectionException;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;

enum AiStatus: string
{
    case Created  = 'created';
    case Modified = 'modified';
    case None     = 'none';

    public const string FRONTEND_LL = 'LLL:EXT:frontend_templates/Resources/Private/Language/Frontend/Enum/aiStatus.xlf:';

    /**
     * @throws AspectNotFoundException
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws ReflectionException
     */
    public function getLabel(): string
    {
        return LocalizationUtility::translate(self::FRONTEND_LL . $this->value) ?? '';
    }
}
