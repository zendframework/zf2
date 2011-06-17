<?php

namespace Zend\Db;

interface AdapterAware
{
    public function setDbAdapter(Adapter $adapter);
}