<?php
declare(strict_types=1);
namespace PSB\PsbTemplates\ViewHelpers\Bootstrap;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 Daniel Ablass <dn@phantasie-schmiede.de>, PSbits
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use PSB\PsbFoundation\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class ProgessBarViewHelper
 *
 * @package PSB\PsbTemplates\ViewHelpers\Bootstrap
 */
class ProgessBarViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('maxValue', 'int', 'maximum value', false, 100);
        $this->registerArgument('minValue', 'int', 'minimum value', false, 0);
        $this->registerArgument('value', 'int', 'current value', true);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function render(): string
    {
        $maxValue = $this->arguments['maxValue'];
        $minValue = $this->arguments['minValue'];
        $value = $this->arguments['value'];

        $percentage = 100 / ($maxValue - $minValue) * ($value - $minValue);
        $percentage = max(1, round($percentage));

        $classes = 'bg-';

        switch ($percentage) {
            case 74 < $percentage:
                $classes .= 'success';
                break;
            case 24 < $percentage:
                $classes .= 'warning';
                break;
            default:
                $classes .= 'danger';
        }

        $classes .= ' progress-bar';

        if (100 > $percentage) {
            $classes .= ' progress-bar-striped';
        }

        $standaloneView = ObjectUtility::get(StandaloneView::class);
        $templatePath = GeneralUtility::getFileAbsFileName('EXT:psb_templates/Resources/Private/Templates/ViewHelpers/Bootstrap/ProgressBar.html');

        $standaloneView->setFormat('html');
        $standaloneView->setTemplatePathAndFilename($templatePath);
        $standaloneView->assignMultiple([
            'classes'    => $classes,
            'maxValue'   => $maxValue,
            'minValue'   => $minValue,
            'percentage' => $percentage,
            'value'      => $value,
        ]);

        return $standaloneView->render();
    }
}
