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

use PhpCsFixer\Finder;
use Webmozarts\CodeStyle\LibraryConfig;

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude(['vendor'])
    ->ignoreDotFiles(false);

$config = new LibraryConfig(
    <<<'EOF'
        This file is part of the Webmozarts StrictPHPUnit package.

        (c) Webmozarts GmbH <office@webmozarts.com>

        For the full copyright and license information, please view the LICENSE
        file that was distributed with this source code.
        EOF,
);
$config->setCacheFile(__DIR__.'/dist/.php-cs-fixer.cache');

return $config->setFinder($finder);
