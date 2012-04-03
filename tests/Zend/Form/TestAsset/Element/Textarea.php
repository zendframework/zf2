<?php

namespace ZendTest\Form\TestAsset\Element;

use Zend\Form\Element;
use Zend\Config\Config;

class Textarea extends Element
{
    public function __construct($name, $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Config) {
            $this->setConfig($options);
        }
        $this->helper = null;
    }
}
