<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mail\Transport;

/**
 * A trait for objects that provide a TransportInterface
 */
trait TransportAwareTrait
{
    protected $mailTransport;

    /**
     * Set the mail transport.
     *
     * @param \Zend\Mail\Transport\TransportInterface $transport
     *
     * @return self
     */
    public function setTransport(TransportInterface $transport)
    {
        $this->mailTransport = $transport;
        return $this;
    }

    /**
     * Get the mail trainsport.
     *
     * @return \Zend\Mail\Transport\TransportInterface
     */
    public function getTransport()
    {
        return $this->mailTransport;
    }
}
