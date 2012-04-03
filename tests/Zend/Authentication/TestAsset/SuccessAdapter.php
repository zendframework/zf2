<?php

namespace ZendTest\Authentication\TestAsset;

use Zend\Authentication\Adapter as AuthenticationAdapter;
use Zend\Authentication\Result as AuthenticationResult;

class SuccessAdapter implements AuthenticationAdapter
{
    public function authenticate()
    {
        return new AuthenticationResult(true, 'someIdentity');
    }
}
