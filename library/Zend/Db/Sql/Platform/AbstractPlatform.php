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
use Zend\Db\Sql\SqlInterface;

class AbstractPlatform implements PlatformDecoratorInterface, PreparableSqlInterface, SqlInterface
{
    /**
     * @var object
     */
    protected $subject = null;

    /**
     * Default Adapter platform
     * @var string
     */
    protected $defaultPlatform = 'Zend\Db\Adapter\Platform\Sql92';

    /**
     * @var PlatformDecoratorInterface[]
     */
    protected $decorators = array(
        'mysql' => array(
            'Zend\Db\Sql\Select' => 'Zend\Db\Sql\Platform\Mysql\SelectDecorator',
            'Zend\Db\Sql\Ddl\CreateTable' => 'Zend\Db\Sql\Platform\Mysql\Ddl\CreateTableDecorator',
        ),
        'sqlserver' => array(
            'Zend\Db\Sql\Select' => 'Zend\Db\Sql\Platform\SqlServer\SelectDecorator'
        ),
        'oracle' => array(
            'Zend\Db\Sql\Select' => 'Zend\Db\Sql\Platform\Oracle\SelectDecorator'
        ),
    );

    protected $cloneCounter = array();

    /**
     * @param $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Set decorator for specified platform
     *
     * @param string $type
     * @param string|PlatformDecoratorInterface $decorator
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
     * Add decorators for specified platform
     * @param array $decorators
     * @return self
     */
    public function setDecorators($decorators)
    {
        foreach ($decorators as $platform => $platformDecorators) {
            foreach ($platformDecorators as $type => $decorator) {
                $this->setTypeDecorator($type, $decorator, $platform);
            }
        }
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
     * @param AdapterInterface $adapter
     * @param StatementContainerInterface $statementContainer
     * @throws Exception\RuntimeException
     * @return void
     */
    public function prepareStatement(AdapterInterface $adapter, StatementContainerInterface $statementContainer)
    {
        if ($this->subject instanceof PlatformDecoratorInterface) {
            return null;
        }
        if (!$this->subject instanceof PreparableSqlInterface) {
            throw new Exception\RuntimeException('The subject does not appear to implement Zend\Db\Sql\PreparableSqlInterface, thus calling prepareStatement() has no effect');
        }

        $decorator = $this->getDecorator($adapter);
        if (!$decorator) {
            return null;
        }
        $decorator->setSubject($this->subject);
        $decorator->prepareStatement($adapter, $statementContainer);
        $this->cloneCounter[get_class($decorator)]--;
        return $statementContainer;
    }

    /**
     * @param null|\Zend\Db\Adapter\Platform\PlatformInterface $adapterPlatform
     * @return mixed
     * @throws Exception\RuntimeException
     */
    public function getSqlString(PlatformInterface $adapterPlatform = null)
    {
        if ($this->subject instanceof PlatformDecoratorInterface) {
            return null;
        }
        if (!$this->subject instanceof SqlInterface) {
            throw new Exception\RuntimeException('The subject does not appear to implement Zend\Db\Sql\PreparableSqlInterface, thus calling prepareStatement() has no effect');
        }
        $decorator = $this->getDecorator($adapterPlatform);
        if (!$decorator) {
            return null;
        }

        $decorator->setSubject($this->subject);
        $sql = $decorator->getSqlString($adapterPlatform);
        $this->cloneCounter[get_class($decorator)]--;
        return $sql;
    }

    /**
     * Find decorator for subject and platform. If not found - return subject
     *
     * @param mixed $subject
     * @param PlatformInterface|AdapterInterface $adapterOrPlatform
     * @return mixed
     */
    protected function getDecorator($adapterOrPlatform = null)
    {
        $platformName = strtolower($this->resolvePlatform($adapterOrPlatform)->getName());
        if (!isset($this->decorators[$platformName])) {
            return null;
        }
        foreach ($this->decorators[$platformName] as $type => $decorator) {
            if (!($this->subject instanceof $type && is_a($decorator, $type, true))) {
                continue;
            }
            if (is_string($decorator)) {
                $decorator = $this->decorators[$platformName][$type] = new $decorator();
            }
            $decoratorClass = get_class($decorator);
            $counter = isset($this->cloneCounter[$decoratorClass])
                    ? ++$this->cloneCounter[$decoratorClass]
                    : $this->cloneCounter[$decoratorClass] = 0;

            return $counter === 0
                    ? $decorator
                    : clone $decorator;
        }
        return null;
    }

    /**
     *
     * @param null|Zend\Db\Adapter\AdapterInterface|Zend\Db\Adapter\Platform\PlatformInterface $adapterOrPlatform
     * @return \Zend\Db\Adapter\Platform\PlatformInterface
     * @throws Exception\InvalidArgumentException
     */
    protected function resolvePlatform($adapterOrPlatform)
    {
        if (!$adapterOrPlatform) {
            if (is_string($this->defaultPlatform)) {
                $this->defaultPlatform = new $this->defaultPlatform;
            }
            return $this->defaultPlatform;
        }
        if ($adapterOrPlatform instanceof AdapterInterface) {
            return $adapterOrPlatform->getPlatform();
        }
        if ($adapterOrPlatform instanceof PlatformInterface) {
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
