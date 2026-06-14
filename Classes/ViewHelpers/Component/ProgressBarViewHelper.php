<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\ViewHelpers\Component;

use InvalidArgumentException;
use JsonException;
use PSBits\Foundation\Utility\ContextUtility;
use PSBits\Templates\Data\ExtensionInformation;
use PSBits\Templates\Service\ViewService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class ProgressBarViewHelper
 *
 * @package PSBits\Templates\ViewHelpers\Component
 */
class ProgressBarViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    public function __construct(
        protected readonly ExtensionInformation $extensionInformation,
        protected readonly ViewService          $viewService,
    ) {
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('classes', 'string', 'CSS classes', false, '');
        $this->registerArgument('maxValue', 'int', 'maximum value', false, 100);
        $this->registerArgument('minValue', 'int', 'minimum value', false, 0);
        $this->registerArgument('value', 'int', 'current value', true);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws JsonException
     * @throws NotFoundExceptionInterface
     */
    public function render(): string
    {
        $classes = $this->arguments['classes'];
        $maxValue = $this->arguments['maxValue'];
        $minValue = $this->arguments['minValue'];
        $value = $this->arguments['value'];

        if ($maxValue <= $minValue) {
            throw new InvalidArgumentException(__CLASS__ . ': maxValue must be higher than minValue!', 1758896064);
        }

        $percentage = 100 / ($maxValue - $minValue) * ($value - $minValue);
        $percentage = max(1, round($percentage, 0, PHP_ROUND_HALF_UP));

        if ('' === $classes) {
            $classes = 'bg-';

            $classes .= match (true) {
                74 < $percentage => 'success',
                24 < $percentage => 'warning',
                default          => 'danger',
            };

            if (100 > $percentage) {
                $classes .= ' progress-bar-striped';
            }
        }

        $view = $this->viewService->createStandaloneView(
            $this->extensionInformation,
            ContextUtility::getRequest(),
            'ViewHelpers/Component/ProgressBar'
        );
        $view->assignMultiple([
            'classes'    => $classes,
            'content'    => $this->renderChildren(),
            'maxValue'   => $maxValue,
            'minValue'   => $minValue,
            'percentage' => $percentage,
            'value'      => $value,
        ]);

        return $view->render();
    }
}
