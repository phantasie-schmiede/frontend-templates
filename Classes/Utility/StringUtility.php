<?php
declare(strict_types=1);

/*
 * This file is part of PSB Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSB\PsbTemplates\Utility;

use JsonException;
use PSB\PsbFoundation\Utility\LocalizationUtility;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;

/**
 * Class StringUtility
 *
 * @package PSB\PsbTemplates\Utility
 */
class StringUtility
{
    public const LL = 'LLL:EXT:psb_templates/Resources/Private/Language/Utility/stringUtility.xlf:';

    /**
     * @throws AspectNotFoundException
     * @throws ContainerExceptionInterface
     * @throws InvalidConfigurationTypeException
     * @throws JsonException
     * @throws NotFoundExceptionInterface
     */
    public static function createEnumeration(array $items): string
    {
        $separator = LocalizationUtility::translate(self::LL . 'separator');
        $conjunction = ' ' . LocalizationUtility::translate(self::LL . 'conjunction') . ' ';
        $result = (string)array_shift($items);

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
