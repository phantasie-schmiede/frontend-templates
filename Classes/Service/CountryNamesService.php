<?php
declare(strict_types=1);

/*
 * This file is part of PSB Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSB\PsbTemplates\Service;

use Locale;
use PSB\PsbFoundation\Utility\ContextUtility;
use Symfony\Component\Intl\Countries;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;

/**
 * Class CountryNamesService
 *
 * @package PSB\PsbTemplates\Service
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

    public function getAll(): array
    {
        return Countries::getNames();
    }

    public function getFromCode(string $code): string
    {
        return Countries::getName($code);
    }
}
