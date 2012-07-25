<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace Zend\I18n\Translator\Loader;

use Zend\I18n\Exception;
use Zend\I18n\Translator\Plural\Rule as PluralRule;
use Zend\I18n\Translator\TextDomain;

/**
 * Tmx loader.
 *
 * @category   Zend
 * @package    Zend_I18n
 * @subpackage Translator
 */
class Tmx implements LoaderInterface
{
    /**
     * load(): defined by LoaderInterface.
     *
     * @see    LoaderInterface::load()
     * @param  string $filename
     * @param  string $locale
     * @return TextDomain
     * @throws Exception\InvalidArgumentException
     */
    public function load($filename, $locale)
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Could not open file %s for reading',
                $filename
            ));
        }

        $textDomain = new TextDomain();

        libxml_use_internal_errors(true);

        $xml = simplexml_load_file($filename);

        if(!$xml) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s is not a valid tmx file',
                $filename
            ));            
        }

        $result = $xml->xpath('/tmx/body/tu/tuv[@xml:lang=\''.$locale.'\']/..');

        foreach($result as $node) {
            $attributes = $node->attributes();
            // Silently skip the nodes that does not have the 'tuid' attribute
            if(isset($attributes['tuid'])) {
                $textDomain[(string) $attributes['tuid']] = (string) $node->tuv->seg;
            }
        }

        return $textDomain;
    }
}
