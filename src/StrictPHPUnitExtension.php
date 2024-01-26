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

use function class_alias;
use function version_compare;

/** @psalm-suppress MissingDependency,UndefinedClass */
class_alias(
    (string) version_compare(
        (string) InstalledVersions::getPrettyVersion('phpunit/phpunit'),
        '10.0',
        '>=',
    )
        ? StrictPHPUnit10Extension::class
        : StrictPHPUnit9Extension::class,
    StrictPHPUnitExtension::class,
);
