<?php

namespace Zend\Module\Consumer;

interface AutoloaderProviderInterface
{
    /**
     * Return an array for passing to Zend\Loader\AutoloaderFactory.
     *
     * @return array
     */
    public function getAutoloaderConfig();
}
