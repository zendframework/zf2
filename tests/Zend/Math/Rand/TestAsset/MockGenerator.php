<?php

namespace ZendTest\Math\Rand\TestAsset;

use Zend\Math\Rand;

class MockGenerator extends Rand\Generator
{
    public function __construct()
    {
        parent::__construct();
        $this->mixingHashAlgo = 'sha512';
    }

    protected function initSources()
    {
        $this->sources = array();

        $this->sources[] = function ($length) {
            return str_repeat(chr(0), $length);
        };
        $this->sources[] = function ($length) {
            return str_repeat(chr(0), $length);
        };
        $this->sources[] = function ($length) {
            return str_repeat(chr(0), $length);
        };
        $this->sources[] = function ($length) {
            return str_repeat(chr(0), $length);
        };
    }
}
