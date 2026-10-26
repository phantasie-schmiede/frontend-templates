<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\ViewHelpers\Event;

use InvalidArgumentException;
use PSBits\Templates\Event\AdditionalContentEventInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class AdditionalContentViewHelper
 *
 * @package PSBits\Templates\ViewHelpers\Event
 */
class AdditionalContentViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    public function __construct(
        protected readonly EventDispatcher $eventDispatcher,
    ) {
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument(
            'parameters',
            'array',
            'Pass optional parameters to the event.',
            false,
            []
        );
        $this->registerArgument(
            'eventClass',
            'string',
            'Provide the class name of the event that provides the additional content; it needs to implement ' . AdditionalContentEventInterface::class . '!',
            true
        );
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $eventClass = $this->arguments['eventClass'];

        if (!class_exists($eventClass)) {
            throw new InvalidArgumentException(
                'The provided event class ' . $this->arguments['eventClass'] . ' does not exist!',
                1721043807
            );
        }

        $event = new $eventClass();

        if (!$event instanceof AdditionalContentEventInterface) {
            throw new InvalidArgumentException(
                __CLASS__ . ' requires an instance of ' . AdditionalContentEventInterface::class . ' as argument!',
                1721040401
            );
        }

        $event->setParameters($this->arguments['parameters'])
            ->setRequest($this->getRequest());

        return $this->eventDispatcher->dispatch($event)
            ->getContent();
    }

    private function getRequest(): ?ServerRequestInterface
    {
        if ($this->renderingContext->hasAttribute(ServerRequestInterface::class)) {
            return $this->renderingContext->getAttribute(ServerRequestInterface::class);
        }

        return null;
    }
}
