<?php
/**
 * This file is part of the Yamlizer package.
 *
 * (c) Pierre Waibel <waibelp85@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Clear cache directory
$cacheDir  = __DIR__ . '/cache';
$dirHandle = opendir($cacheDir);
if ($dirHandle) {
    while ($file = readdir($dirHandle)) {
        if (in_array($file, ['.', '..'])) {
            continue;
        }

        @unlink($cacheDir . '/' . $file);
    }

    closedir($dirHandle);
}

// Require vendor + src components including custom classloader for annotations
require_once __DIR__ . '/../vendor/autoload.php';
