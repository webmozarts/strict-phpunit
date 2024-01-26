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

namespace Webmozarts\StrictPHPUnit;

use Composer\InstalledVersions;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\ScalarComparator;
use SebastianBergmann\Exporter\Exporter;

use function is_string;
use function mb_strtolower;
use function sprintf;
use function str_starts_with;

/**
 * A comparator that always compares scalar values in a type-safe way.
 *
 * @internal
 */
final class StrictScalarComparator extends ScalarComparator
{
    /**
     * TODO: add types as PHPUnit 9 support is dropped.
     *
     * @param mixed $expected
     * @param mixed $actual
     * @param float $delta
     * @param bool  $canonicalize
     * @param bool  $ignoreCase
     */
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
            $this->failComparison(
                $expected,
                $actual,
                true,
                'Failed asserting that two strings are equal.',
            );
        }

        $this->failComparison(
            $expected,
            $actual,
            false,
            'Failed asserting that %2$s matches expected %1$s.',
        );
    }

    /**
     * @throws ComparisonFailure
     */
    public function failComparison(
        mixed $expected,
        mixed $actual,
        bool $diff,
        string $message,
    ): never {
        $exporter = new Exporter();
        $expectedAsString = $exporter->export($expected);
        $actualAsString = $exporter->export($actual);

        if (self::isPhpUnit9()) {
            /** @psalm-suppress InvalidArgument,TooManyArguments */
            throw new ComparisonFailure(
                $expected,
                $actual,
                $diff ? $expectedAsString : '',
                $diff ? $actualAsString : '',
                false,
                sprintf(
                    $message,
                    $expectedAsString,
                    $actualAsString,
                ),
            );
        }

        throw new ComparisonFailure(
            $expected,
            $actual,
            $diff ? $expectedAsString : '',
            $diff ? $actualAsString : '',
            sprintf(
                $message,
                $expectedAsString,
                $actualAsString,
            ),
        );
    }

    private static function isPhpUnit9(): bool
    {
        static $phpunit9;

        if (isset($phpunit9)) {
            return $phpunit9;
        }

        $phpunit9 = str_starts_with(
            (string) InstalledVersions::getPrettyVersion('phpunit/phpunit'),
            '9.',
        );

        return $phpunit9;
    }
}
