<?php

namespace ZendTest\Math\Rand\TestAsset;

use Zend\Math\Rand;

class MockGenerator extends Rand\Generator
{
    public function __construct($algo = 'sha512')
    {
        // init entropy sources
        $this->initSources();

        // create randomness pool
        $this->pool = '';
        $numSources = count($this->sources);
        $length     = (int) ceil(self::POOL_SIZE / $numSources);

        $i = 0;
        while (strlen($this->pool) < self::POOL_SIZE) {
            /* @var $source Closure */
            $source = $this->sources[$i];
            if ($s = $source($length)) {
                $this->pool .= $s;
            }
            $i = (++$i % $numSources);
        }

        $this->pool = substr($this->pool, 0, self::POOL_SIZE);

        $this->hashAlgo = $algo;
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
