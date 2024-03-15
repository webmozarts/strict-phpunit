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

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Comparator\ComparisonFailure;
use stdClass;
use Webmozarts\StrictPHPUnit\StrictScalarComparator;

use function acos;
use function fclose;
use function fopen;
use function is_resource;

use const INF;

/**
 * @internal
 */
#[CoversClass(StrictScalarComparator::class)]
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

        // See https://github.com/sebastianbergmann/comparator/blob/main/tests/NumericComparatorTest.php#L61
        // A few were removed as the original comparator does a cast which is precisely what we do not want
        // to do here.
        yield [1337, 1337];
        yield [0x539, 1337];
        yield [0o2471, 1337];
        yield [INF, INF];
        yield [2.3, 2.3];
        yield [1.2e3, 1200.];
        yield [5.5E+123, 5.5E+123];
        yield [5.5E-123, 5.5E-123];
    }

    #[DataProvider('identicalValueProvider')]
    public function test_it_succeeds_if_scalar_values_are_identical(
        mixed $expected,
        mixed $actual,
        bool $ignoreCase = false,
    ): void {
        self::assertTrue($this->comparator->accepts($expected, $actual));
        self::assertTrue($this->comparator->accepts($actual, $expected));

        $this->comparator->assertEquals($expected, $actual, ignoreCase: $ignoreCase);
        $this->comparator->assertEquals($actual, $expected, ignoreCase: $ignoreCase);
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

        yield '0 integer and 0. float' => [0, 0.];
    }

    #[DataProvider('nonIdenticalValueProvider')]
    public function test_it_fails_if_scalar_values_are_not_identical(
        mixed $expected,
        mixed $actual,
        bool $ignoreCase = true,
    ): void {
        self::assertTrue($this->comparator->accepts($expected, $actual));

        $this->expectException(ComparisonFailure::class);

        $this->comparator->assertEquals($expected, $actual, ignoreCase: $ignoreCase);
    }

    #[DataProvider('nonIdenticalValueProvider')]
    public function test_it_fails_if_scalar_values_are_identical_reverse(
        mixed $actual,
        mixed $expected,
        bool $ignoreCase = true,
    ): void {
        self::assertTrue($this->comparator->accepts($expected, $actual));

        $this->expectException(ComparisonFailure::class);

        $this->comparator->assertEquals($expected, $actual, ignoreCase: $ignoreCase);
    }

    public static function equalValueWithDeltaProvider(): iterable
    {
        yield 'strings' => ['foo', 'foo', 0.];

        yield 'strings with delta' => ['foo', 'foo', 1.];

        yield 'integers' => [12, 12];

        yield 'integers with delta' => [12, 13, 2.];

        yield 'integers with delta at the lower limit' => [12, 10, 2.];

        yield 'integers with delta at the upper limit' => [12, 14, 2.];

        yield 'floats' => [12.5, 12.5];

        yield 'floats with delta' => [12.5, 13.5, 2.];

        yield 'floats with delta at the limit' => [12.5, 14.5, 2.];

        // See https://github.com/sebastianbergmann/comparator/blob/main/tests/NumericComparatorTest.php#L61
        // A few were commented as the original comparator does a cast which is precisely what we do not want
        // to do here.
        yield [1337, 1338, 1];
        yield [2.3, 2.5, 0.5];
        yield [3., 3.05, 0.05];
        yield [1.2e3, 1201., 1];
        /** @psalm-suppress InvalidOperand */
        yield [1 / 3, 1 - 2 / 3, 0.0000000001];
        yield [5.5E+123, 5.6E+123, 0.2E+123];
        yield [5.5E-123, 5.6E-123, 0.2E-123];
    }

    /**
     * @param mixed $expected
     * @param mixed $actual
     */
    #[DataProvider('equalValueWithDeltaProvider')]
    public function test_it_succeeds_if_scalar_values_are_equal_with_delta($expected, $actual, float $delta = 0.): void
    {
        self::assertTrue($this->comparator->accepts($expected, $actual));
        self::assertTrue($this->comparator->accepts($actual, $expected));

        $this->comparator->assertEquals($expected, $actual, $delta);
        $this->comparator->assertEquals($actual, $expected, $delta);
    }

    public static function notEqualValueWithDeltaProvider(): iterable
    {
        yield 'integers' => [12, 13];

        yield 'integers with delta' => [12, 20, 2.];

        yield 'integers with delta at the lower limit' => [12, 9, 2.];

        yield 'integers with delta at the upper limit' => [12, 15, 2.];

        yield 'floats' => [12.5, 12.4];

        yield 'floats with delta' => [12.5, 15.1, 2.];

        // See https://github.com/sebastianbergmann/comparator/blob/main/tests/NumericComparatorTest.php#L90
        yield [1337, 1338];
        yield ['1338', 1337];
        yield [0x539, 1338];
        yield [1337, 1339, 1];
        yield ['1337', 1340, 2];
        yield [2.3, 4.2];
        yield ['2.3', 4.2];
        yield [5.0, '4'];
        yield [5.0, 6];
        yield [1.2e3, 1201];
        yield [2.3, 2.5, 0.2];
        yield [3, 3.05, 0.04];
        yield [3, acos(8)];
        yield [acos(8), 3];
        yield [acos(8), acos(8)];
        /** @psalm-suppress InvalidOperand */
        yield [1 / 3, 1 - 2 / 3];
        yield [5.5E+123, '5.7E+123'];
        yield [5.5E-123, '5.7E-123'];
        yield [5.5E+123, '5.7E+123', 0.1E+123];
        yield [5.5E-123, '5.7E-123', 0.1E-123];
    }

    #[DataProvider('notEqualValueWithDeltaProvider')]
    public function test_it_fails_if_scalar_values_are_not_equal_with_delta(
        mixed $expected,
        mixed $actual,
        float $delta = 0.,
    ): void {
        $this->assertCompactorAcceptsAndNotEqual($expected, $actual, $delta);
        $this->assertCompactorAcceptsAndNotEqual($actual, $expected, $delta);
    }

    private function assertCompactorAcceptsAndNotEqual(mixed $expected, mixed $actual, float $delta): void
    {
        self::assertTrue($this->comparator->accepts($expected, $actual));

        try {
            $this->comparator->assertEquals($expected, $actual, $delta);

            self::fail('Expected an exception to be thrown.');
        } catch (ComparisonFailure) {
            /** @psalm-suppress InternalMethod */
            $this->addToAssertionCount(1);
        }
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
     * @param mixed $value
     */
    #[DataProvider('notAcceptableValuesProvider')]
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
