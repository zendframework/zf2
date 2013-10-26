<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      https://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Loader\TestAsset;

/**
 * @group      Loader
 */
class SamplePlugin
{
    public $options;

    public function __construct($options = null)
    {
        $this->options = $options;
    }
}
