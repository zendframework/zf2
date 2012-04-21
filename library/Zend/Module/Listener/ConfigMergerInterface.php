<?php

namespace Zend\Module\Listener;

interface ConfigMergerInterface
{
    /**
     * getMergedConfig
     *
     * @param bool $returnConfigAsObject
     * @return mixed
     */
    public function getMergedConfig($returnConfigAsObject = true);

    /**
     * setMergedConfig
     *
     * @param array $config
     * @return ConfigMergerInterface
     */
    public function setMergedConfig(array $config);
}
