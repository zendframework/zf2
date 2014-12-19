<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Adapter\Platform;

class Oracle extends AbstractSql92BasedPlatform
{
    /**
     * @var bool
     */
    protected $quoteIdentifiers = true;

    /**
     * @param array $options
     */
    public function __construct($options = array())
    {
        if (isset($options['quote_identifiers'])
            && ($options['quote_identifiers'] == false
            || $options['quote_identifiers'] === 'false')
        ) {
            $this->quoteIdentifiers = false;
        }
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'Oracle';
    }

    /**
     * Quote identifier
     *
     * @param  string $identifier
     * @return string
     */
    public function quoteIdentifier($identifier)
    {
        if ($this->quoteIdentifiers === false) {
            return $identifier;
        }
        return parent::quoteIdentifier($identifier);
    }

    /**
     * Quote identifier chain
     *
     * @param string|string[] $identifierChain
     * @return string
     */
    public function quoteIdentifierChain($identifierChain)
    {
        if ($this->quoteIdentifiers === false) {
            return (is_array($identifierChain)) ? implode('.', $identifierChain) : $identifierChain;
        }
        return parent::quoteIdentifierChain($identifierChain);
    }

    /**
     * Quote identifier in fragment
     *
     * @param  string $identifier
     * @param  array $safeWords
     * @return string
     */
    public function quoteIdentifierInFragment($identifier, array $safeWords = array())
    {
        if ($this->quoteIdentifiers === false) {
            return $identifier;
        }
        return parent::quoteIdentifierInFragment($identifier, $safeWords);
    }
}
