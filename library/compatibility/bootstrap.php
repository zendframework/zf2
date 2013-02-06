<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

/**
 * This file bootstraps in compatibility components to allow for backwards
 * compatibility with versions of PHP.
 */

if (version_compare(PHP_VERSION, '5.3.3') <= 0) {
    /**
     * For PHP <= 5.3.3, we need to load a compatibility version of the 
     * Stdlib ArrayObject class. This overrides the normal ArrayObject version
     * provided in library
     */
    require_once __DIR__ . '/Zend/Stdlib/ArrayObject.php';
}