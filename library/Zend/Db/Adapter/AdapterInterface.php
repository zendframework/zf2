<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Adapter;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 *
 * @property Driver\DriverInterface $driver
 * @property Platform\PlatformInterface $platform
 */
interface AdapterInterface
{
    /**
     * @return Driver\DriverInterface
     */
    public function getDriver();

    /**
     * @return Platform\PlatformInterface
     */
    public function getPlatform();

    /**
     * Create statement
     *
     * @param  string $initialSql
     * @param  ParameterContainer $initialParameters
     * @return Driver\StatementInterface
     */
    public function createStatement($initialSql = null, $initialParameters = null);
}
