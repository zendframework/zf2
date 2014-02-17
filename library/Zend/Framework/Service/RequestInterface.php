<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

interface RequestInterface
{
    /**
     * @return string
     */
    public function alias();

    /**
     * @param callable $factory
     * @param array $options
     * @return mixed
     */
    public function call($factory, array $options = []);

    /**
     * @return bool
     */
    public function shared();
}
