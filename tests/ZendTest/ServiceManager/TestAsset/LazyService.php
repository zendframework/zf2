<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace ZendTest\ServiceManager\TestAsset;

class LazyService
{
    /**
     * @var int
     */
    protected $counter = 0;

    /**
     * Increments internal counter by 1
     */
    public function increment()
    {
        $this->counter += 1;
    }

    /**
     * Retrieves current internal counter
     *
     * @return int
     */
    public function count()
    {
        return $this->counter;
    }
}
