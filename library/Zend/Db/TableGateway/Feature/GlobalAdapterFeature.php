<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Db
 */

namespace Zend\Db\TableGateway\Feature;

use Zend\Db\Adapter\Adapter,
    Zend\Db\TableGateway\Exception;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage TableGateway
 */
class GlobalAdapterFeature extends AbstractFeature
{

    /**
     * @var Adapter[]
     */
    protected static $staticAdapters = array();

    /**
     * Set static adapter
     *
     * @param Adapter $adapter
     */
    public static function setStaticAdapter(Adapter $adapter)
    {
        $class = get_called_class();

        static::$staticAdapters[$class] = $adapter;
        if ($class === __CLASS__) {
            static::$staticAdapters[__CLASS__] = $adapter;
        }
    }

    /**
     * Get static adapter
     *
     * @return Adapter
     */
    public static function getStaticAdapter()
    {
        $class = get_called_class();

        // class specific adapter
        if (isset(static::$staticAdapters[$class])) {
            return static::$staticAdapters[$class];
        }

        // default adapter
        if (isset(static::$staticAdapters[__CLASS__])) {
            return static::$staticAdapters[__CLASS__];
        }

        throw new Exception\RuntimeException('No database adapter was found in the static registry.');
    }

    /**
     * after initialization, retrieve the original adapter as "master"
     */
    public function preInitialize()
    {
        $this->tableGateway->adapter = self::getStaticAdapter();
    }


}
