<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage ResultSet
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Db\ResultSet;

use ArrayObject;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage ResultSet
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Row extends ArrayObject implements RowObjectInterface
{
    /**
     * Constructor
     */
    public function __construct(array $rowData = array())
    {
        parent::__construct($rowData, ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @param array $rowData
     * @return Row
     */
    public function populate(array $rowData)
    {
        $this->exchangeArray($rowData);
        return $this;
    }
}
