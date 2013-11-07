<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Navigation\TestAsset;

class LinearTreeIterator extends \ArrayIterator implements \Zend\Stdlib\LinearTreeIteratorInterface
{
    protected $depthField = 'depth';

    public function getDepth()
    {
        $current = $this->current();
        return (integer)$current[$this->depthField];
    }

    public function setDepthField($depthField)
    {
        $this->depthField = $depthField;
        return $this;
    }
}
