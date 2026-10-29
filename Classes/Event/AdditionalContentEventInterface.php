<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Event;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface AdditionalContentEventInterface
 *
 * @package PSBits\FrontendTemplates\Event
 */
interface AdditionalContentEventInterface
{
    public function getContent(): string;

    /**
     * @param array<string, mixed> $parameters
     */
    public function setParameters(array $parameters): static;

    public function setRequest(?ServerRequestInterface $request): static;
}
