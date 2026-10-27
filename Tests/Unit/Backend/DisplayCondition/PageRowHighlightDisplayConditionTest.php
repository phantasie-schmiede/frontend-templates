<?php

declare(strict_types=1);

/*
 * This file is part of PSBits Frontend Templates.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace PSBits\FrontendTemplates\Tests\Unit\Backend\DisplayCondition;

use PHPUnit\Framework\Attributes\Test;
use PSBits\FrontendTemplates\Backend\DisplayCondition\PageRowHighlightDisplayCondition;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class PageRowHighlightDisplayConditionTest
 *
 * Only the record-based layout resolution is covered here; the rootline resolution reads page records from the
 * database and is therefore exercised by the functional tests.
 *
 * @package PSBits\FrontendTemplates\Tests\Unit\Backend\DisplayCondition
 */
class PageRowHighlightDisplayConditionTest extends UnitTestCase
{
    private PageRowHighlightDisplayCondition $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new PageRowHighlightDisplayCondition();
    }

    #[Test]
    public function rowsBelowOneNeverMatch(): void
    {
        $parameters = ['record' => ['backend_layout' => ['pagets__twoColumns-2-1']]];

        self::assertFalse($this->conditionMatches(0, $parameters));
        self::assertFalse($this->conditionMatches(-1, $parameters));
    }

    #[Test]
    public function theLayoutOfTheRecordDecidesTheAvailableRows(): void
    {
        $parameters = ['record' => ['backend_layout' => ['pagets__oneColumnTwoColumns-1-2-oneColumn']]];

        self::assertTrue($this->conditionMatches(1, $parameters));
        self::assertTrue($this->conditionMatches(2, $parameters));
        self::assertTrue($this->conditionMatches(3, $parameters));
        self::assertFalse($this->conditionMatches(4, $parameters));
    }

    #[Test]
    public function aTwoRowLayoutOnlyMatchesRowsOneAndTwo(): void
    {
        $parameters = ['record' => ['backend_layout' => ['pagets__oneColumnTwoColumns-1-1']]];

        self::assertTrue($this->conditionMatches(1, $parameters));
        self::assertTrue($this->conditionMatches(2, $parameters));
        self::assertFalse($this->conditionMatches(3, $parameters));
    }

    #[Test]
    public function unknownLayoutsDefaultToOneRow(): void
    {
        $parameters = ['record' => ['backend_layout' => ['pagets__someUnknownLayout']]];

        self::assertTrue($this->conditionMatches(1, $parameters));
        self::assertFalse($this->conditionMatches(2, $parameters));
    }

    #[Test]
    public function withoutALayoutOnlyRowOneMatches(): void
    {
        $parameters = ['record' => ['backend_layout' => ['0'], 'pid' => 0]];

        self::assertTrue($this->conditionMatches(1, $parameters));
        self::assertFalse($this->conditionMatches(2, $parameters));
    }

    #[Test]
    public function aMissingRecordDefaultsToRowOne(): void
    {
        self::assertTrue($this->conditionMatches(1, []));
        self::assertFalse($this->conditionMatches(2, []));
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function conditionMatches(int $row, array $parameters): bool
    {
        $parameters['conditionParameters'] = [$row];

        return $this->subject->matches($parameters);
    }
}
