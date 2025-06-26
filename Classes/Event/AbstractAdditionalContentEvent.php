<?php
declare(strict_types=1);

/*
 * This file is part of PSBits Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\Event;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AbstractAdditionalContentEvent
 *
 * @package PSBits\Templates\Event
 */
abstract class AbstractAdditionalContentEvent implements AdditionalContentEventInterface
{
    protected string                  $content    = '';
    protected array                   $parameters = [];
    protected ?ServerRequestInterface $request    = null;

    public function addContent(string $content): void
    {
        $this->content .= $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getRequest(): ?ServerRequestInterface
    {
        return $this->request;
    }

    public function setParameters(array $parameters): static
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function setRequest(?ServerRequestInterface $request): static
    {
        $this->request = $request;

        return $this;
    }
}
