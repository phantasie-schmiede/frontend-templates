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
 * Interface AdditionalContentEventInterface
 *
 * @package PSBits\Templates\Event
 */
interface AdditionalContentEventInterface
{
    public function getContent(): string;

    public function setParameters(array $parameters): static;

    public function setRequest(?ServerRequestInterface $request): static;
}
