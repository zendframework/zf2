<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ServiceManager\TestAsset;

use Zend\ServiceManager\AbstractPluginManager;

class OverrideKeysPluginManager extends AbstractPluginManager
{
    /**
     * @var array
     */
    protected $invokableClasses = array(
        'FOO' => 'ZendTest\ServiceManager\TestAsset\Foo',
    );

    /**
     * {@inheritDoc}
     */
    public function validatePlugin($plugin)
    {
        return;
    }
}
