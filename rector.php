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

use Rector\Config\RectorConfig;

$applyBaseConfig = require __DIR__.'/../../base-rector-config.php';

return static function (RectorConfig $rectorConfig) use ($applyBaseConfig): void {
    $applyBaseConfig($rectorConfig, __DIR__);

    // Library specific config here.
};
