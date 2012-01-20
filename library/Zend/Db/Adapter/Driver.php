<?php

namespace Zend\Db\Adapter;

interface Driver
{
    const NAME_FORMAT_CAMELCASE = 'camelCase';
    const NAME_FORMAT_NATURAL = 'natural';

    /**
     * @param string $nameFormat
     * @return string
     */
    public function getDatabasePlatformName($nameFormat = self::NAME_FORMAT_CAMELCASE);

    /**
     * @return bool
     */
    public function checkEnvironment();

    /**
     * @return DriverConnection
     */
    public function getConnection();

    /**
     * @return DriverStatement
     */
    public function getStatementPrototype();

    /**
     * @return DriverResult
     */
    public function getResultPrototype();

    /**
     * @return array
     */
    public function getPrepareTypeSupport();

    /**
     * @param $name
     * @return string
     */
    public function formatNamedParameter($name);

}
