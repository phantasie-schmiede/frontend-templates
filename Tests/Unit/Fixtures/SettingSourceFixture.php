<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Unit\Fixtures;

use PSBits\FrontendTemplates\Service\GlobalVariableProviders\SettingSourceInterface;

/**
 * Setting source with static, test-controlled results for the SettingsProvider tests.
 */
class SettingSourceFixture implements SettingSourceInterface
{
    /** @var int[] */
    public static array $queriedPids = [];

    /** @var array<int, mixed> */
    public static array $results = [];

    public function findByPid(int $pid): mixed
    {
        self::$queriedPids[] = $pid;

        return self::$results[$pid] ?? null;
    }
}
