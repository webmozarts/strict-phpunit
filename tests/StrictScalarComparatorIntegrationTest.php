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

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class StrictScalarComparatorIntegrationTest extends TestCase
{
    #[DataProviderExternal(StrictScalarComparatorTest::class, 'identicalValueProvider')]
    public function test_it_succeeds_if_scalar_values_are_identical(
        mixed $expected,
        mixed $actual,
        bool $ignoreCase = false,
    ): void {
        if ($ignoreCase) {
            self::assertEqualsIgnoringCase($expected, $actual);
        } else {
            self::assertEquals($expected, $actual);
        }
    }

    #[DataProviderExternal(StrictScalarComparatorTest::class, 'nonIdenticalValueProvider')]
    public function test_it_fails_if_scalar_values_are_not_identical(
        mixed $expected,
        mixed $actual,
        bool $ignoreCase = true,
    ): void {
        try {
            if ($ignoreCase) {
                self::assertEqualsIgnoringCase($expected, $actual);
            } else {
                self::assertEquals($expected, $actual);
            }

            self::fail('Expected a comparison failure.');
        } catch (AssertionFailedError) {
            /** @psalm-suppress InternalMethod */
            $this->addToAssertionCount(1);
        }
    }
}
