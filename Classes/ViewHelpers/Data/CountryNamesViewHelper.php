<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\ViewHelpers\Data;

use PSBits\Templates\Service\CountryNamesService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class CountryNamesViewHelper
 *
 * @package PSBits\Templates\ViewHelpers\Data
 */
class CountryNamesViewHelper extends AbstractViewHelper
{
    public function __construct(
        protected readonly CountryNamesService $countryNamesService,
    ) {
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('countryCode', 'string', 'get name for a specific country');
    }

    public function render(): array|string
    {
        if ($this->arguments['countryCode']) {
            return $this->countryNamesService->getFromCode($this->arguments['countryCode']);
        }

        return $this->countryNamesService->getAll();
    }
}
