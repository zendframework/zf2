<?php


namespace Zend\Http\PhpEnvironment;

use Zend\Http\Response as HttpResponse,
    Zend\Stdlib\Parameters;

class Response extends HttpResponse
{
    protected $headersSent = false;

    protected $contentSent = false;

    public function __construct()
    {
    }
    
    public function headersSent()
    {
        return $this->headersSent;
    }
    
    public function contentSent()
    {
        return $this->contentSent;
    }
    
    public function sendHeaders()
    {
        if ($this->headersSent()) {
            return;
        }

        $version = $this->getVersion();
        $code    = $this->getStatusCode();
        $message = $this->getReasonPhrase();
        $status  = sprintf('HTTP/%s %d %s', $version, $code, $message);
        header($status);

        foreach ($this->headers() as $header) {
            header($header->toString());
        }

        $this->headersSent = true;
        return $this;
    }
    
    public function sendContent()
    {
        if ($this->contentSent()) {
            return;
        }
        echo $this->getContent();
        $this->contentSent = true;
        return $this;
    }

    public function send()
    {
        $this->sendHeaders()
             ->sendContent();
        return $this;
    }
    
}
    
