<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Adapter\Platform;

abstract class AbstractPlatform implements PlatformInterface
{
    /**
     * Quote identifier in fragment
     *
     * @param  string $identifier
     * @param  array $safeWords
     * @return string
     */
    public function quoteIdentifierInFragment($identifier, array $safeWords = array())
    {
        $safeRegex = '';
        foreach($safeWords as $k=>$sWord) {
            unset($safeWords[$k]);
            $sWordQuoted = preg_quote($sWord);
            $safeWords[strtolower($sWord)] = $sWordQuoted;
            $safeRegex .= '|' . $sWordQuoted;
        }

        $parts = preg_split('/([\.\s]' . $safeRegex . ')/i', $identifier, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        $safeWords = array_merge($safeWords, array('*'=>1,  ' '=>1, '.'=>1, 'as'=>1));
        foreach ($parts as $i => $part) {
            if (!isset($safeWords[strtolower($part)])) {
                $parts[$i] = $this->quoteIdentifier($part);
            }
        }

        return implode('', $parts);
    }

    /**
     * Quote value list
     *
     * @param string|string[] $valueList
     * @return string
     */
    public function quoteValueList($valueList)
    {
        if (!is_array($valueList)) {
            return $this->quoteValue($valueList);
        }

        $value = reset($valueList);
        do {
            $valueList[key($valueList)] = $this->quoteValue($value);
        } while ($value = next($valueList));
        return implode(', ', $valueList);
    }
}
