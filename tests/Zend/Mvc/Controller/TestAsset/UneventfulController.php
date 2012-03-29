<?php

namespace ZendTest\Mvc\Controller\TestAsset;

use Zend\Stdlib\Dispatchable,
    Zend\Stdlib\Request,
    Zend\Stdlib\Response;

class UneventfulController implements Dispatchable
{
    public function dispatch(Request $request, Response $response = null)
    {
    }
}
