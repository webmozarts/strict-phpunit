<?php

/*
 * This file is part of the Webmozarts Strict PHPUnit package.
 *
 * (c) Webmozarts GmbH <office@webmozarts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmozarts\StrictPHPUnit;

use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\ScalarComparator;
use function is_string;
use function mb_strtolower;
use function sprintf;

/**
 * A comparator that always compares scalar values in a type-safe way.
 *
 * @internal
 */
final class StrictScalarComparator extends ScalarComparator
{
    public function assertEquals(
        $expected,
        $actual,
        $delta = 0.0,
        $canonicalize = false,
        $ignoreCase = false
    ): void {
        $expectedToCompare = $expected;
        $actualToCompare = $actual;

        if ($ignoreCase && is_string($expectedToCompare)) {
            $expectedToCompare = mb_strtolower($expectedToCompare);
        }

        if ($ignoreCase && is_string($actualToCompare)) {
            $actualToCompare = mb_strtolower($actualToCompare);
        }

        if ($expectedToCompare === $actualToCompare) {
            return;
        }

        if (is_string($expected) && is_string($actual)) {
            throw new ComparisonFailure(
                $expected,
                $actual,
                // add diff
                $this->exporter->export($expected),
                $this->exporter->export($actual),
                false,
                'Failed asserting that two strings are equal.'
            );
        }

        throw new ComparisonFailure(
            $expected,
            $actual,
            // no diff is required
            '',
            '',
            false,
            sprintf(
                'Failed asserting that %s matches expected %s.',
                $this->exporter->export($actual),
                $this->exporter->export($expected)
            )
        );
    }
}
