<?php

namespace Zend\Db\Adapter\Platform;

class SqlServer implements \Zend\Db\Adapter\Platform
{
    public function getName()
    {
        return 'SQLServer';
    }

    public function getQuoteIdentifierSymbol()
    {
        return array('[', ']');
    }

    public function quoteIdentifier($identifier)
    {
        return '[' . $identifier . ']';
    }

    public function getQuoteValueSymbol()
    {
        return '\'';
    }

    public function quoteValue($value)
    {
        return '\'' . str_replace($value, '\'', '\'\'') . '\'';
    }

    public function getIdentifierSeparator()
    {
        return '.';
    }
}