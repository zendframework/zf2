<?php
/**
 * @namespace
 */
namespace ZendX\Db\ActiveRecord;
/**
 * @uses       \Zend\Db\Exception
 * @category   ZendX
 * @package    ZendX_Db
 * @subpackage ActiveRecord
 */
class Exception extends \Zend\Db\Exception
{
    protected $_chainedException = null;

    public function __construct($message = '', $code = 0, \Exception $e = null)
    {
        if ($e && (0 === $code)) {
            $code = $e->getCode();
        }
        parent::__construct($message, (int) $code, $e);
    }

    public function hasChainedException()
    {
        return ($this->_previous !== null);
    }

    public function getChainedException()
    {
        return $this->getPrevious();
    }

}
