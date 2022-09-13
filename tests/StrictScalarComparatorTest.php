<?php

/*
 * This file is part of the Webmozarts StrictPHPUnit package.
 *
 * (c) Webmozarts GmbH <office@webmozarts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmozarts\StrictPHPUnit\Tests;

use PHPUnit\Framework\TestCase;
use SebastianBergmann\Comparator\ComparisonFailure;
use stdClass;
use Webmozarts\StrictPHPUnit\StrictScalarComparator;

use function fclose;
use function fopen;
use function is_resource;

/**
 * @covers \Webmozarts\StrictPHPUnit\StrictScalarComparator
 *
 * @internal
 */
final class StrictScalarComparatorTest extends TestCase
{
    /**
     * @var false|resource|null
     */
    private static $resource;

    private StrictScalarComparator $comparator;

    public static function tearDownAfterClass(): void
    {
        if (is_resource(self::$resource)) {
            fclose(self::$resource);
        }
    }

    protected function setUp(): void
    {
        $this->comparator = new StrictScalarComparator();
    }

    public static function identicalValueProvider(): iterable
    {
        yield 'strings' => ['foo', 'foo'];

        yield 'strings with ignore case' => ['FOO', 'foo', true];

        yield 'true' => [true, true];

        yield 'false' => [false, false];

        yield 'null' => [null, null];

        yield 'integers' => [12, 12];

        yield 'zero floats' => [0., 0.];

        yield 'floats' => [1.234, 1.234];
    }

    /**
     * @dataProvider identicalValueProvider
     *
     * @param mixed $expected
     * @param mixed $actual
     */
    public function test_it_succeeds_if_scalar_values_are_identical($expected, $actual, bool $ignoreCase = false): void
    {
        self::assertTrue($this->comparator->accepts($expected, $actual));
        self::assertTrue($this->comparator->accepts($actual, $expected));

        $this->comparator->assertEquals($expected, $actual, 0.0, false, $ignoreCase);
        $this->comparator->assertEquals($actual, $expected, 0.0, false, $ignoreCase);
    }

    public static function nonIdenticalValueProvider(): iterable
    {
        yield 'empty string and null' => ['', null];

        yield 'empty string and null with ignore case' => ['', null, true];

        yield 'empty string and 0' => ['', 0];

        yield 'empty string and 0.' => ['', 0.];

        yield 'empty string and false' => ['', false];

        yield 'empty string and true' => ['', true];

        yield '"0" string and null' => ['0', null];

        yield '"0" string and 0' => ['0', 0];

        yield '"0" string and 0.' => ['0', 0.];

        yield '"0" string and false' => ['0', false];

        yield '"0" string and true' => ['0', true];

        yield '"1" string and null' => ['1', null];

        yield '"1" string and 1' => ['1', 1];

        yield '"1" string and 1.' => ['1', 1.];

        yield '"1" string and false' => ['1', false];

        yield '"1" string and true' => ['1', true];

        yield '"null" string and null' => ['null', null];

        yield '"null" string and 0' => ['null', 0];

        yield '"null" string and 0.' => ['null', 0.];

        yield '"null" string and false' => ['null', false];

        yield '"null" string and true' => ['null', true];

        yield '"false" string and null' => ['false', null];

        yield '"false" string and 0' => ['false', 0];

        yield '"false" string and 0.' => ['false', 0.];

        yield '"false" string and false' => ['false', false];

        yield '"false" string and true' => ['false', true];

        yield '"true" string and null' => ['true', null];

        yield '"true" string and 1' => ['true', 1];

        yield '"true" string and 1.' => ['true', 1.];

        yield '"true" string and false' => ['true', false];

        yield '"true" string and true' => ['true', true];

        yield 'null and 0' => [null, 0];

        yield 'null and 0.' => [null, 0.];

        yield 'null and false' => [null, false];

        yield 'null and true' => [null, true];

        yield 'true and 0' => [true, 0];

        yield 'true and 0.' => [true, 0.];

        yield 'true and false' => [true, false];

        yield 'false and 0' => [false, 0];

        yield 'false and 0.' => [false, 0.];

        yield 'false and true' => [false, true];

        yield '0 and 0.' => [0, 0.];
    }

    /**
     * @dataProvider nonIdenticalValueProvider
     *
     * @param mixed $expected
     * @param mixed $actual
     */
    public function test_it_fails_if_scalar_values_are_identical($expected, $actual, bool $ignoreCase = true): void
    {
        self::assertTrue($this->comparator->accepts($expected, $actual));

        $this->expectException(ComparisonFailure::class);

        $this->comparator->assertEquals($expected, $actual, 0.0, false, $ignoreCase);
    }

    /**
     * @dataProvider nonIdenticalValueProvider
     *
     * @param mixed $actual
     * @param mixed $expected
     */
    public function test_it_fails_if_scalar_values_are_identical_reverse($actual, $expected, bool $ignoreCase = true): void
    {
        self::assertTrue($this->comparator->accepts($expected, $actual));

        $this->expectException(ComparisonFailure::class);

        $this->comparator->assertEquals($expected, $actual, 0.0, false, $ignoreCase);
    }

    public static function notAcceptableValuesProvider(): iterable
    {
        self::$resource = fopen(__FILE__, 'r');

        yield 'empty array' => [[]];

        yield 'non-empty array' => [[1, 2]];

        yield 'object' => [new stdClass()];

        yield 'resource' => [self::$resource];
    }

    /**
     * @dataProvider notAcceptableValuesProvider
     *
     * @param mixed $value
     */
    public function test_it_does_not_accept_non_scalar_values($value): void
    {
        self::assertFalse($this->comparator->accepts($value, $value));
        self::assertFalse($this->comparator->accepts('scalar', $value));
        self::assertFalse($this->comparator->accepts($value, 'scalar'));
    }

    public function test_it_reports_a_diff_if_both_values_are_strings(): void
    {
        try {
            $this->comparator->assertEquals('foo', 'bar');
        } catch (ComparisonFailure $exception) {
            self::assertSame('foo', $exception->getExpected());
            self::assertSame('bar', $exception->getActual());
            self::assertSame("'foo'", $exception->getExpectedAsString());
            self::assertSame("'bar'", $exception->getActualAsString());
        }
    }

    public function test_it_does_not_report_a_diff_if_at_least_one_value_is_not_a_string(): void
    {
        try {
            $this->comparator->assertEquals('foo', false);
        } catch (ComparisonFailure $exception) {
            self::assertSame('foo', $exception->getExpected());
            self::assertFalse($exception->getActual());
            self::assertSame('', $exception->getExpectedAsString());
            self::assertSame('', $exception->getActualAsString());
        }
    }
}
