<?php
namespace Zend\Http\Client;

use Zend\Stdlib\AbstractOptions;
use Zend\Http\Request;

class ClientOptions extends AbstractOptions
{
    /**
     * Maximum number of redirects to follow
     *
     * @var int
     */
    protected $maxRedirects = 5;
}
