<?php
namespace Zend\Di\Exception;

use Zend\Di\Exception;
use DomainException;

class UndefinedReferenceException extends DomainException implements Exception
{
}
