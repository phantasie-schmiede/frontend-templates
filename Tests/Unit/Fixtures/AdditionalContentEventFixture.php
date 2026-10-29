<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Unit\Fixtures;

use PSBits\FrontendTemplates\Event\AbstractAdditionalContentEvent;

/**
 * Class AdditionalContentEventFixture
 *
 * Concrete additional content event for the AdditionalContentViewHelper tests.
 *
 * @package PSBits\FrontendTemplates\Tests\Unit\Fixtures
 */
class AdditionalContentEventFixture extends AbstractAdditionalContentEvent
{
    /**
     * @return array<string, mixed>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
