<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Db
 */

namespace Zend\Db\Adapter;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 */
interface AdapterAwareInterface
{
    public function setDbAdapter(Adapter $adapter);
}
