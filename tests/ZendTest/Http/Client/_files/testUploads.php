<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      https://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

if (! empty($_FILES)) {
    foreach ($_FILES as $name => $file) {
        if (is_array($file['name'])) {
            foreach ($file['name'] as $k => $v) {
                echo "$name $v {$file['type'][$k]} {$file['size'][$k]}\n";
            }
        } else {
            echo "$name {$file['name']} {$file['type']} {$file['size']}\n";
        }
    }
}
