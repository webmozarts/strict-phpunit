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

use Rector\Configuration\RectorConfigBuilder;

/** @var callable(string):RectorConfigBuilder $createConfig */
$createConfig = require __DIR__.'/../../create-base-rector-config.php';

return $createConfig(__DIR__);
