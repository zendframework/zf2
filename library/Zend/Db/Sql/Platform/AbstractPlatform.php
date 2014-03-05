<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql\Platform;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Adapter\StatementContainerInterface;
use Zend\Db\Sql\Exception;
use Zend\Db\Sql\PreparableSqlInterface;

class AbstractPlatform
{
    protected $defaultPlatform = 'Zend\Db\Adapter\Platform\Sql92';

    /**
     * @var PlatformDecoratorInterface[]
     */
    protected $decorators = array(
        'mysql'     => array(
            'Zend\Db\Sql\Select' => 'Zend\Db\Sql\Platform\Mysql\SelectDecorator',
            'Zend\Db\Sql\Ddl\CreateTable' => 'Zend\Db\Sql\Platform\Mysql\Ddl\CreateTableDecorator',
        ),
        'sqlserver' => array(
            'Zend\Db\Sql\Select' => 'Zend\Db\Sql\Platform\SqlServer\SelectDecorator'
        ),
        'oracle'    => array(
            'Zend\Db\Sql\Select' => 'Zend\Db\Sql\Platform\Oracle\SelectDecorator'
        ),
    );

    /**
     * Set decorator for specified platform
     *
     * @param string $type
     * @param string|\Zend\Db\Sql\Platform\PlatformDecoratorInterface $decorator
     * @param string|\Zend\Db\Adapter\Adapter|\Zend\Db\Adapter\Platform\PlatformInterface $adapterOrPlatform
     * @return self
     */
    public function setTypeDecorator($type, $decorator, $adapterOrPlatform = null)
    {
        $platformName = is_string($adapterOrPlatform)
                ? $adapterOrPlatform
                : $this->resolvePlatform($adapterOrPlatform)->getName();

        $this->decorators[strtolower($platformName)][$type] = $decorator;
        return $this;
    }

    /**
     * @return array|PlatformDecoratorInterface[]
     */
    public function getDecorators()
    {
        return $this->decorators;
    }

    /**
     * Return expression data for subject by platform
     */
    public function getExpressionData($subject, PlatformInterface $platform = null)
    {
        $decorator = $this->getDecorator($subject, $platform);
        if (!$decorator) {
            return $subject->getExpressionData();
        }
        $expressionData = $decorator->getExpressionData();
        if ($decorator instanceof PlatformDecoratorInterface) {
            $decorator->setSubject(null);
        }
        return $expressionData;
    }

    /**
     * Prepare statement for subject by platform
     *
     * @param  AdapterInterface $adapter
     * @param  StatementContainerInterface $statementContainer
     * @return void
     */
    public function prepareStatement($subject, AdapterInterface $adapter, StatementContainerInterface $statementContainer = null)
    {
        if (!$subject instanceof PreparableSqlInterface) {
            throw new Exception\RuntimeException('The subject does not appear to implement Zend\Db\Sql\PreparableSqlInterface, thus calling prepareStatement() has no effect');
        }
        $decorator = $this->getDecorator($subject, $adapter);
        if (!$decorator) {
            return null;
        }
        $statementContainer = $decorator->prepareStatement($adapter, $statementContainer);
        if ($decorator instanceof PlatformDecoratorInterface) {
            $decorator->setSubject(null);
        }
        return $statementContainer;
    }

    /**
     * Get SQL string for subject by platform
     *
     * @param  null|PlatformInterface $adapterPlatform If null, defaults to Sql92
     * @return string
     */
    public function getSqlString($subject, PlatformInterface $platform = null)
    {
        $decorator = $this->getDecorator($subject, $platform);
        if (!$decorator) {
            return null;
        }
        $sqlString = $decorator->getSqlString($platform);
        if ($decorator instanceof PlatformDecoratorInterface) {
            $decorator->setSubject(null);
        }
        return $sqlString;
    }

    /**
     * Add decorators for specified platform
     * @param array $decorators
     * @return \Zend\Db\Sql\Platform\AbstractPlatform
     */
    public function setDecorators($decorators)
    {
        foreach ($decorators as $platform=>$platformDecorators) {
            foreach ($platformDecorators as $type=>$decorator) {
                $this->setTypeDecorator($type, $decorator, $platform);
            }
        }
        return $this;
    }

    /**
     * Find decorator for subject and platform. If not found - return subject
     *
     * @param mixed $subject
     * @param PlatformInterface|AdapterInterface $adapterOrPlatform
     * @return mixed
     */
    protected function getDecorator($subject, $adapterOrPlatform = null)
    {
        $platformName = strtolower($this->resolvePlatform($adapterOrPlatform)->getName());
        if (isset($this->decorators[$platformName])) {
            foreach ($this->decorators[$platformName] as $type => $decorator) {
                if ($subject instanceof $type && is_a($decorator, $type, true)) {
                    if (is_string($decorator)) {
                        $decorator = $this->decorators[$platformName][$type] = new $decorator();
                    } elseif ($decorator->hasSubject()) {
                        $decorator = clone $decorator;
                    }
                    $decorator->setSubject($subject);
                    return $decorator;
                }
            }
        }
        return null;
    }

    /**
     *
     * @param null|Zend\Db\Adapter\AdapterInterface|Zend\Db\Adapter\Platform\PlatformInterface $adapterOrPlatform
     * @return \Zend\Db\Adapter\Platform\PlatformInterface
     * @throws Exception\InvalidArgumentException
     */
    public function resolvePlatform($adapterOrPlatform)
    {
        if (!$adapterOrPlatform) {
            if (is_string($this->defaultPlatform)) {
                $this->defaultPlatform = new $this->defaultPlatform;
            }
            return $this->defaultPlatform;
        } elseif ($adapterOrPlatform instanceof AdapterInterface) {
            return $adapterOrPlatform->getPlatform();
        } elseif ($adapterOrPlatform instanceof PlatformInterface) {
            return $adapterOrPlatform;
        }
        throw new Exception\InvalidArgumentException(sprintf(
            '$adapterOrPlatform parameter should be %s, %s or %s',
            'null',
            'Zend\Db\Adapter\AdapterInterface',
            'Zend\Db\Adapter\Platform\PlatformInterface'
        ));
    }
}
