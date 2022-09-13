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

use PHPUnit\Runner\BeforeFirstTestHook;
use SebastianBergmann\Comparator\Factory;

final class StrictPHPUnitExtension implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        Factory::getInstance()->register(new StrictScalarComparator());
    }
}
