<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Session;

if (PHP_VERSION_ID < 50304) {
    class_alias('Zend\Session\AbstractContainer', 'Zend\Session\AbstractBaseContainer');
} else {
    class_alias('Zend\Session\Container\PhpReferenceCompatibility', 'Zend\Session\AbstractBaseContainer');
}

/**
 * Session storage container
 *
 * Allows for interacting with session storage in isolated containers, which
 * may have their own expiries, or even expiries per key in the container.
 * Additionally, expiries may be absolute TTLs or measured in "hops", which
 * are based on how many times the key or container were accessed.
 */
class Container extends AbstractBaseContainer
{
}
