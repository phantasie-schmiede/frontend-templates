<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\Service;

use Locale;
use PSBits\Foundation\Utility\ContextUtility;
use Symfony\Component\Intl\Countries;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;

/**
 * Class CountryNamesService
 *
 * @package PSBits\Templates\Service
 */
class CountryNamesService
{
    /**
     * @throws AspectNotFoundException
     */
    public function __construct()
    {
        Locale::setDefault(
            ContextUtility::getCurrentFrontendLanguage()
                ->getLocale()
                ->getLanguageCode()
        );
    }

    /** @return array<string, string> */
    public function getAll(): array
    {
        return Countries::getNames();
    }

    public function getFromCode(string $code): string
    {
        return Countries::getName($code);
    }
}
