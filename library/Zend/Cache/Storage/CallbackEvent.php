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
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Storage;

use ArrayObject,
    Exception;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class CallbackEvent extends PostEvent
{

    /**
     * Additional information
     * @var ArrayObject
     */
    protected $info;

    /**
     * Constructor
     *
     * Accept a target and its parameters.
     *
     * @param  string           $name     Event name
     * @param  Adapter          $storage
     * @param  ArrayObject      $params
     * @param  mixed            $result
     * @param  null|Exception   $error
     * @param  null|ArrayObject $info
     * @return void
     */
    public function __construct($name, Adapter $storage, ArrayObject $params, &$result, ArrayObject $info)
    {
        parent::__construct($name, $storage, $params, $result);
        $this->setInfo($info);
    }

    public function setInfo(ArrayObject $info = null)
    {
        $this->info = $info;
        return $this;
    }

    public function getInfo()
    {
        return $this->info;
    }
}
