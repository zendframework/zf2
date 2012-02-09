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
 * @package    Zend\Service\WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace Zend\Service\WindowsAzure\Storage;

use Zend\Service\WindowsAzure\Storage\StorageEntityAbstract;

/**
 * @category   Zend
 * @package    Zend\Service\WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @property int  $start   Page range start
 * @property int  $end     Page range end
 */
class PageRegionInstance extends StorageEntityAbstract
{
    /**
     * Constructor
     * 
     * @param int  $start   Page range start
     * @param int  $end     Page range end
     */
    public function __construct($start = 0, $end = 0) 
    {	        
        $this->_data = array(
            'start' => $start,
            'end'   => $end
        );
    }
}
