<?php
/**
 * This file is part of the Yamlizer package.
 *
 * (c) Pierre Waibel <waibelp85@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\Common\Annotations\AnnotationRegistry;

AnnotationRegistry::registerLoader(
    function ($class) {
        $spaces = explode("\\", $class);
        array_shift($spaces);

        $file = __DIR__ . '/' . implode(DIRECTORY_SEPARATOR, $spaces) . '.php';
        if (file_exists($file)) {
            require $file;

            return true;
        }
    }
);
