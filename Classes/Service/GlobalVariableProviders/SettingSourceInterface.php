<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\Templates\Service\GlobalVariableProviders;

/**
 * Interface SettingSourceInterface
 *
 * @package PSBits\Templates\Service\GlobalVariableProviders
 */
interface SettingSourceInterface
{
    public function findByPid(int $pid): mixed;
}
