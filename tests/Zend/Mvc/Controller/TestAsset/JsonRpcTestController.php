<?php

namespace ZendTest\Mvc\Controller\TestAsset;

use Zend\Mvc\Controller\JsonRpcController;

class JsonRpcTestController extends JsonRpcController
{
    
    public function registerRpcMethods(){
        return array(
            'add', 
            'concat'
        );
    }
    
    /**
     * @param int $a
     * @param int $b
     * @return int
     */      
    public function add($a, $b)
    {
        return (int) $a + (int) $b;        
    }   
    
    /**
     * @param string $a
     * @param string $b
     * @return string
     */      
    public function concat($a, $b)
    {
        return (string) $a . (string) $b;        
    }  
}

