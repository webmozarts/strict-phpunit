<?php declare(strict_types=1);

/*
 * This file is part of the Webmozarts Strict PHPUnit package.
 *
 * (c) Webmozarts GmbH <office@webmozarts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PhpCsFixer\Finder;

require __DIR__.'/../../base-php-cs.php';

return createConfig(
    Finder::create()
        ->in(__DIR__),
    [],
    'Strict PHPUnit'
);
