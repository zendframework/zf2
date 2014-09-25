<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Helper\HeadMeta\Plugin;

use Zend\Filter\Word\CamelCaseToSeparator;
use Zend\View\Exception;

/**
 * Extends HeadMeta API with methods suitable for adding OG-related meta tags.
 *
 * Example usage:
 * $headMeta->setOgTitle('My page title');
 * $headMeta->setOgTitle('Some description');
 * $headMeta->setOgType('article');
 * //Custom type notation (underscore):
 * $headMeta->setOg_ArticleAuthor('http://www.example.com/author/1');
 *
 * Visit http://ogp.me for more details.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class OpenGraph implements PluginInterface
{
    const META_TYPE = 'property';
    const PROPERTY_SEPARATOR = ':';

    const GLOBAL_NAMESPACE = 'og';

    /**
     * Names of valid properties in global namespace.
     *
     * @var array
     */
    private static $validProperties = array(
        'title',
        'type',
        'url',
        'description',
        'determiner',
        'locale',
        'siteName',
        'image',
        'video',
        'audio',
    );

    /**
     * Supported namespaces for custom types.
     *
     * @var array
     */
    private static $validCustomNamespaces = array(
        'music',
        'video',
        'article',
        'book',
        'profile'
    );

    public function handle($method, $args)
    {
        $matches = array();
        if (preg_match(
            '/^(?P<action>set|(pre|ap)pend)Og((?P<customNsFlag>_))?(?P<props>[a-zA-Z0-9_]+)$/',
            $method,
            $matches)
        ) {
            $action = $matches['action'];
            $customNs = !empty($matches['customNsFlag']);
            $properties = $matches['props'];

            if ($customNs) {
                if (!preg_match('/^' . implode('|', self::$validCustomNamespaces) . '/', strtolower($properties))) {
                    throw new Exception\BadMethodCallException('Unsupported custom Open Graph type');
                }
            } else {
                if (!preg_match('/^' . implode('|', self::$validProperties) . '/', strtolower($properties))) {
                    throw new Exception\BadMethodCallException('Unsupported Open Graph property');
                }
                $properties = self::GLOBAL_NAMESPACE . $properties;
            }

            $typeValue = $this->buildPropertyName($properties);

            if (empty($args)) {
                throw new Exception\BadMethodCallException("OpenGraph value is missing for the $typeValue property");
            }

            return array(
                'action'    => $action,
                'type'      => self::META_TYPE,
                'typeValue' => $typeValue,
                'content'   => array_shift($args),
                'modifiers' => (array) array_shift($args),
            );
        }

        return false;
    }

    private function buildPropertyName($propertyName)
    {
        static $cc2UnderscoreFilter = null;
        if (null === $cc2UnderscoreFilter) {
            $cc2UnderscoreFilter = new CamelCaseToSeparator(self::PROPERTY_SEPARATOR);
        }

        $propertyName = $cc2UnderscoreFilter->filter($propertyName);
        $propertyName = strtolower($propertyName);

        return $propertyName;
    }
}
