<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

if (version_compare(PHP_VERSION, '5.3.4', 'lt')) {
    class_alias('Zend\Session\Storage\SessionArrayStorage\PhpLegacyCompatibility', 'Zend\Session\Storage\SessionArrayStorage');
} else {
    class_alias('Zend\Session\Storage\SessionArrayStorage\PhpReferenceCompatibility', 'Zend\Session\Storage\SessionArrayStorage');
}

