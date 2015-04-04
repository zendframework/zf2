<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager;

/**
 * Aliased abstract plugin manager
 *
 * This plugin manager adds support for alias. This can be useful for some plugin managers where aliases are
 * simpler to use (like view plugin manager). However, it adds some overhead as it needs to resolve aliases, so
 * be sure to choose this one only if absolutely needed
 */
abstract class AbstractAliasedPluginManager extends AbstractPluginManager
{
    /**
     * A list of aliases
     *
     * Should map one alias to a service name, or another alias (aliases are recursively resolved)
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * {@inheritDoc}
     */
    public function get($name, array $options = [])
    {
        return parent::get($this->resolveAlias($name), $options);
    }

    /**
     * {@inheritDoc}
     */
    public function has($name, $checkAbstractFactories = false)
    {
        return parent::has($this->resolveAlias($name), $checkAbstractFactories);
    }

    /**
     * {@inheritDoc}
     */
    protected function configure(array $config)
    {
        parent::configure($config);
        $this->aliases = isset($config['aliases']) ? $config['aliases'] : [];
    }

    /**
     * Recursively resolve an alias name to a service name
     *
     * @param  string $alias
     * @return string
     */
    private function resolveAlias($alias)
    {
        $name = $alias;

        do {
            $canBeResolved = isset($this->aliases[$name]);
            $name          = $canBeResolved ? $this->aliases[$name] : $name;
        } while ($canBeResolved);

        return $name;
    }
}