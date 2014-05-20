<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\TestAsset;

use Zend\Db\Sql\Update;
use Zend\Db\Sql\Platform\PlatformDecoratorInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\StatementContainerInterface;
use Zend\Db\Adapter\Platform\PlatformInterface;

class UpdateDecorator extends Update implements PlatformDecoratorInterface
{
    protected $subject;

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    protected function processPrepareStatement(AdapterInterface $adapter, StatementContainerInterface $statementContainer)
    {
        // localize variables
        foreach (get_object_vars($this->subject) as $name => $value) {
            $this->{$name} = $value;
        }
        parent::processPrepareStatement($adapter, $statementContainer);
        $statementContainer->setSql('{decorate}' . $statementContainer->getSql() . '{decorate}');
        return $statementContainer;
    }

    protected function processGetSqlString(AdapterInterface $adapter, PlatformInterface $adapterPlatform)
    {
        // localize variables
        foreach (get_object_vars($this->subject) as $name => $value) {
            $this->{$name} = $value;
        }
        return '{decorate}' . parent::processGetSqlString($adapter, $adapterPlatform) . '{decorate}';
    }
}
