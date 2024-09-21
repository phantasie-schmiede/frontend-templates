<?php
declare(strict_types=1);

/*
 * This file is part of PSB Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSB\PsbTemplates\ViewHelpers\Data;

use Locale;
use PSB\PsbFoundation\Utility\ContextUtility;
use Symfony\Component\Intl\Countries;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class CountryNamesViewHelper
 *
 * @package PSB\PsbTemplates\ViewHelpers\Data
 */
class CountryNamesViewHelper extends AbstractViewHelper
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

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('countryCode', 'string', 'get name for a specific country');
    }

    public function render(): array|string
    {
        if ($this->arguments['countryCode']) {
            return Countries::getName($this->arguments['countryCode']);
        }

        return Countries::getNames();
    }
}
