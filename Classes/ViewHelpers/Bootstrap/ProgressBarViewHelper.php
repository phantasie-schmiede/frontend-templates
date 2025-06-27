<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\ViewHelpers\Bootstrap;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class ProgressBarViewHelper
 *
 * @package PSBits\Templates\ViewHelpers\Bootstrap
 */
class ProgressBarViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('classes', 'string', 'CSS classes');
        $this->registerArgument('maxValue', 'int', 'maximum value', false, 100);
        $this->registerArgument('minValue', 'int', 'minimum value', false, 0);
        $this->registerArgument('value', 'int', 'current value', true);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $classes = $this->arguments['classes'];
        $maxValue = $this->arguments['maxValue'];
        $minValue = $this->arguments['minValue'];
        $value = $this->arguments['value'];

        $percentage = 100 / ($maxValue - $minValue) * ($value - $minValue);
        $percentage = max(1, round($percentage));

        if (null === $classes) {
            $classes = 'bg-';

            $classes .= match ($percentage) {
                74 < $percentage => 'success',
                24 < $percentage => 'warning',
                default => 'danger',
            };

            if (100 > $percentage) {
                $classes .= ' progress-bar-striped';
            }
        }

        $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
        $templatePath = GeneralUtility::getFileAbsFileName('EXT:templates/Resources/Private/Templates/ViewHelpers/Bootstrap/ProgressBar.html');

        $standaloneView->setFormat('html');
        $standaloneView->setTemplatePathAndFilename($templatePath);
        $standaloneView->assignMultiple([
            'classes'    => $classes,
            'content'    => $this->renderChildren(),
            'maxValue'   => $maxValue,
            'minValue'   => $minValue,
            'percentage' => $percentage,
            'value'      => $value,
        ]);

        return $standaloneView->render();
    }
}
