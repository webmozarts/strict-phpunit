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

use PHPUnit\Event\TestRunner\ExtensionBootstrapped;
use PHPUnit\Event\TestRunner\ExtensionBootstrappedSubscriber;
use SebastianBergmann\Comparator\Factory as ComparatorFactory;

final class RegisterStrictScalarComparatorSubscriber implements ExtensionBootstrappedSubscriber
{
    public function notify(ExtensionBootstrapped $event): void
    {
        ComparatorFactory::getInstance()->register(new StrictScalarComparator());
    }
}
