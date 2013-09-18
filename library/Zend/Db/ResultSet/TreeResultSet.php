<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\ResultSet;

use Zend\Stdlib\LinearTreeIteratorInterface;

class TreeResultSet extends ResultSet implements LinearTreeIteratorInterface
{
    /**
     * depth field name
     *
     * @var string Depth field name
     */
    protected $depthField = 'depth';

    /**
     * Get value from depth field of current row
     *
     * @return string
     */
    public function getDepth()
    {
        $current = $this->current();
        return (integer)$current[$this->depthField];
    }

    /**
     * Set depth field name
     *
     * @param string $depthField
     * @return self
     */
    public function setDepthField($depthField)
    {
        $this->depthField = $depthField;
        return $this;
    }
}
