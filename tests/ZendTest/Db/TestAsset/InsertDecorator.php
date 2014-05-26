<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\TestAsset;

use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Platform\PlatformDecoratorInterface;
use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\StatementContainerInterface;

class InsertDecorator extends Insert implements PlatformDecoratorInterface
{
    protected $subject;

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function prepareStatement(AdapterInterface $adapter, StatementContainerInterface $statementContainer)
    {
        // localize variables
        foreach (get_object_vars($this->subject) as $name => $value) {
            $this->{$name} = $value;
        }
        parent::prepareStatement($adapter, $statementContainer);
        $statementContainer->setSql('{decorate}' . $statementContainer->getSql() . '{decorate}');
        return $statementContainer;
    }

    public function getSqlString(PlatformInterface $adapterPlatform = null)
    {
        // localize variables
        foreach (get_object_vars($this->subject) as $name => $value) {
            $this->{$name} = $value;
        }
        return '{decorate}' . parent::getSqlString($adapterPlatform) . '{decorate}';
    }
}
