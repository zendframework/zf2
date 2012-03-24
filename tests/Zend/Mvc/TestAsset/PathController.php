<?php

namespace ZendTest\Mvc\TestAsset;

use Zend\Stdlib\Dispatchable,
    Zend\Stdlib\Request,
    Zend\Stdlib\Response;

class PathController implements Dispatchable
{
    public function dispatch(Request $request, Response $response = null)
    {
        if (!$response) {
            $response = new HttpResponse();
        }
        $response->setContent(__METHOD__);
        return $response;
    }
}
