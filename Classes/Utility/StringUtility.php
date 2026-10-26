<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\Utility;

use JsonException;
use PSBits\Foundation\Utility\LocalizationUtility;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;

/**
 * Class StringUtility
 *
 * @package PSBits\Templates\Utility
 */
class StringUtility
{
    public const LL = 'LLL:EXT:frontend_templates/Resources/Private/Language/Utility/stringUtility.xlf:';

    /**
     * @param string[] $items
     *
     * @throws AspectNotFoundException
     * @throws ContainerExceptionInterface
     * @throws JsonException
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public static function createEnumeration(array $items): string
    {
        $separator   = LocalizationUtility::translate(self::LL . 'separator');
        $conjunction = ' ' . LocalizationUtility::translate(self::LL . 'conjunction') . ' ';
        $result      = (string)array_shift($items);

        // Returns empty string on empty array or first item of an array with only one item
        if (empty($items)) {
            return $result;
        }

        $lastItem = array_pop($items);

        // Returns the last item of an array with only two items
        if (empty($items)) {
            $result .= $conjunction;
        } else {
            $result .= $separator . implode($separator, $items) . $conjunction;
        }

        return $result . $lastItem;
    }
}
