<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Log\TestAsset;

use Zend\Log\Writer\FirePhp\FirePhpInterface;

class MockFirePhp implements FirePhpInterface
{
    public $calls = array();

    protected $enabled;

    public function __construct($enabled = true)
    {
        $this->enabled = $enabled;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function error($line, $label = null, $options = array())
    {
        $this->calls['error'][] = array('line' => $line, 'label' => $label, 'options' => $options);
    }

    public function warn($line, $label = null, $options = array())
    {
        $this->calls['warn'][] = array('line' => $line, 'label' => $label, 'options' => $options);
    }

    public function info($line, $label = null, $options = array())
    {
        $this->calls['info'][] = array('line' => $line, 'label' => $label, 'options' => $options);
    }

    public function trace($line, $label = null, $options = array())
    {
        $this->calls['trace'][] = array('line' => $line, 'label' => $label, 'options' => $options);
    }

    public function log($line, $label = null, $options = array())
    {
        $this->calls['log'][] = array('line' => $line, 'label' => $label, 'options' => $options);
    }
}
