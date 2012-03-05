<?php
namespace Zend\Di\Exception;

use Zend\Di\Exception,
    DomainException;

class AssertionFailedException extends DomainException implements Exception
{
}
