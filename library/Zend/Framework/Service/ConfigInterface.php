<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Serializable;

interface ConfigInterface
    extends IteratorAggregate, ArrayAccess, Serializable, Countable
{
    /**
     * @param string $name
     * @param string $service
     * @return self
     */
    public function configure($name, $service);
}
