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
     * @var PlatformDecoratorInterface[]
     */
    protected $decorators = array();

    /**
     * @var array
     */
    protected $cloneCounter = array();

    /**
     * @param $subject
     * @return self
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param $type
     * @param PlatformDecoratorInterface $decorator
     */
    public function setTypeDecorator($type, PlatformDecoratorInterface $decorator)
    {
        $this->decorators[$type] = $decorator;
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
        if (!$this->subject instanceof PreparableSqlInterface) {
            throw new Exception\RuntimeException('The subject does not appear to implement Zend\Db\Sql\PreparableSqlInterface, thus calling prepareStatement() has no effect');
        }

        if ($decoratorForType = $this->getDecoratorForType()) {
            $decoratorForType->setSubject($this->subject);
            $decoratorForType->prepareStatement($adapter, $statementContainer);
            $this->cloneCounter[get_class($decoratorForType)]--;
        } else {
            $this->subject->prepareStatement($adapter, $statementContainer);
        }
    }

    /**
     * @param null|AdapterInterface|\Zend\Db\Adapter\Platform\PlatformInterface $adapterPlatform
     * @return mixed
     * @throws Exception\RuntimeException
     */
    public function getSqlString($adapterPlatform = null)
    {
        if (!$this->subject instanceof SqlInterface) {
            throw new Exception\RuntimeException('The subject does not appear to implement Zend\Db\Sql\PreparableSqlInterface, thus calling prepareStatement() has no effect');
        }

        if ($decoratorForType = $this->getDecoratorForType()) {
            $decoratorForType->setSubject($this->subject);
            $sql = $decoratorForType->getSqlString($adapterPlatform);
            $this->cloneCounter[get_class($decoratorForType)]--;
            return $sql;
        }

        return $this->subject->getSqlString($adapterPlatform);
    }

    /**
     * @return null|SqlInterface|PlatformDecoratorInterface
     */
    protected function getDecoratorForType()
    {
        foreach ($this->decorators as $type => $decorator) {
            if ($this->subject instanceof $type && is_a($decorator, $type, true)) {
                $decoratorClass = get_class($decorator);
                $counter = isset($this->cloneCounter[$decoratorClass])
                        ? ++$this->cloneCounter[$decoratorClass]
                        : $this->cloneCounter[$decoratorClass] = 0;
                return $counter === 0
                        ? $decorator
                        : clone $decorator;
            }
        }
        return null;
    }
}
