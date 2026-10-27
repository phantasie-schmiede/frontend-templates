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
 * Class SettingSourceFixture
 *
 * Setting source with static, test-controlled results for the SettingsProvider tests.
 *
 * @package PSBits\FrontendTemplates\Tests\Unit\Fixtures
 */
class SettingSourceFixture implements SettingSourceInterface
{
    /** @var array<int, mixed> */
    public static array $results     = [];

    /** @var int[] */
    public static array $queriedPids = [];

    public function findByPid(int $pid): mixed
    {
        self::$queriedPids[] = $pid;

        return self::$results[$pid] ?? null;
    }
}
