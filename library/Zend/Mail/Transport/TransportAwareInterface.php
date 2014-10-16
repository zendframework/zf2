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
 * Interface for TransportInterface injection
 */
interface TransportAwareInterface
{
    /**
     * Set the mail transport.
     *
     * @param \Zend\Mail\Transport\TransportInterface $transport
     *
     * @return TransportAwareInterface
     */
    public function setTransport(TransportInterface $transport);

    /**
     * Get the mail trainsport.
     *
     * @return \Zend\Mail\Transport\TransportInterface
     */
    public function getTransport();
}
