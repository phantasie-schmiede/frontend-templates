<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\Service\GlobalVariableProviders;

use PSBits\Foundation\Service\GlobalVariableProviders\AbstractProvider;
use PSBits\Foundation\Utility\ContextUtility;
use TYPO3\CMS\Frontend\Page\PageInformation;

/**
 * Class CurrentPageProvider
 *
 * @package PSBits\Templates\Service\GlobalVariableProviders
 */
class CurrentPageProvider extends AbstractProvider
{
    /**
     * @return array<string, mixed>
     */
    public function getGlobalVariables(): array
    {
        /** @var PageInformation $pageInformation */
        $pageInformation = ContextUtility::getRequest()
            ->getAttribute('frontend.page.information');

        return $pageInformation->getPageRecord();
    }
}
