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
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Db\Adapter\Driver\Pdo;

use Zend\Db\Adapter\Driver\StatementInterface,
    Zend\Db\Adapter\ParameterContainer,
    Zend\Db\Adapter\Exception;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Statement implements StatementInterface
{

    /**
     * @var \PDO
     */
    protected $pdo = null;

    /**
     * @var Pdo
     */
    protected $driver = null;

    /**
     *
     * @var string
     */
    protected $sql = '';

    /**
     *
     * @var boolean 
     */
    protected $isQuery = null;

    /**
     *
     * @var ParameterContainer 
     */
    protected $parameterContainer = null;

    /**
     * @var bool
     */
    protected $parametersBound = false;

    /**
     * @var \PDOStatement
     */
    protected $resource = null;

    /**
     *
     * @var boolean
     */
    protected $isPrepared = false;

    /**
     * Set driver
     * 
     * @param  Pdo $driver
     * @return Statement 
     */
    public function setDriver(Pdo $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * Initialize
     * 
     * @param  \PDO $connectionResource
     * @return Statement 
     */
    public function initialize(\PDO $connectionResource)
    {
        $this->pdo = $connectionResource;
        return $this;
    }

    /**
     * Set resource
     * 
     * @param  \PDOStatement $pdoStatement
     * @return Statement 
     */
    public function setResource(\PDOStatement $pdoStatement)
    {
        $this->resource = $pdoStatement;
        return $this;
    }

    /**
     * Get resource
     * 
     * @return mixed 
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set sql
     * 
     * @param string $sql
     */
    public function setSql($sql)
    {
        $this->sql = $sql;
        return $this;
    }

    /**
     * Get sql
     * 
     * @return string 
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @param ParameterContainer $parameterContainer
     */
    public function setParameterContainer(ParameterContainer $parameterContainer)
    {
        $this->parameterContainer = $parameterContainer;
        return $this;
    }

    /**
     * @return ParameterContainer
     */
    public function getParameterContainer()
    {
        return $this->parameterContainer;
    }

    /**
     * @param string $sql
     */
    public function prepare($sql = null)
    {
        if ($this->isPrepared) {
            throw new Exception\RuntimeException('This statement has been prepared already');
        }

        if ($sql == null) {
            $sql = $this->sql;
        }

        $this->resource = $this->pdo->prepare($sql);

        if ($this->resource === false) {
            $error = $this->pdo->errorInfo();
            throw new Exception\RuntimeException($error[2]);
        }

        $this->isPrepared = true;
    }

    /**
     * @return bool
     */
    public function isPrepared()
    {
        return $this->isPrepared;
    }

    /**
     * @param mixed $parameters
     * @return Result
     */
    public function execute()
    {
        if (!$this->isPrepared) {
            $this->prepare();
        }

        if ($this->parameterContainer) {
            $this->bindParametersFromContainer($this->parameterContainer);
        }

        if ($this->resource->execute() === false) {
            $error = $this->resource->errorInfo();
            throw new Exception\InvalidQueryException($error[2]);
        }

        $result = $this->driver->createResult($this->resource, $this);
        return $result;
    }

    /**
     * Bind parameters from container
     * 
     * @param ParameterContainer $container
     */
    protected function bindParametersFromContainer(ParameterContainer $container)
    {
        if ($this->parametersBound) {
            return;
        }

        $parameters = $container->getNamedArray();
        foreach ($parameters as $name => &$value) {
            $type = \PDO::PARAM_STR;
            if ($container->offsetHasErrata($name)) {
                switch ($container->offsetGetErrata($name)) {
                    case ParameterContainer::TYPE_INTEGER:
                        $type = \PDO::PARAM_INT;
                        break;
                    case ParameterContainer::TYPE_NULL:
                        $type = \PDO::PARAM_NULL;
                        break;
                    case ParameterContainer::TYPE_LOB:
                        $type = \PDO::PARAM_LOB;
                        break;
                    case (is_bool($value)):
                        $type = \PDO::PARAM_BOOL;
                        break;
                }
            }

            // parameter is named or positional, value is reference
            $parameter = is_int($name) ? ($name + 1) : $name;
            $this->resource->bindParam($parameter, $value, $type);
        }

    }

    /**
     * Perform a deep clone
     * @return Statement A cloned statement
     */
    public function __clone()
    {
        $this->isPrepared = false;
        $this->parametersBound = false;
        $this->resource = null;
        if ($this->parameterContainer) {
            $this->parameterContainer = clone $this->parameterContainer;
        }

    }

}
