<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Bootstrap;

use Traversable;
use Zend\Config\Config;
use Zend\Config\Factory as ConfigFactory;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\Glob;

/**
 * Config listener
 */
class ConfigManager implements
    ConfigMergerInterface
{
    const STATIC_PATH = 'static_path';
    const GLOB_PATH   = 'glob_path';

    /**
     * @var ConfigOptions
     */
    protected $options;

    /**
     * @var array
     */
    protected $configs = array();

    /**
     * @var array
     */
    protected $mergedConfig = array();

    /**
     * @var Config
     */
    protected $mergedConfigObject;

    /**
     * @var array
     */
    protected $paths = array();

    /**
     * @param ConfigOptions $options
     */
    public function __construct(ConfigOptions $options = null)
    {
        if (null === $options) {
            $this->setOptions(new ConfigOptions);
        } else {
            $this->setOptions($options);
        }
    }

    /**
     * @return array
     */
    public function loadMergedConfig()
    {
        if ($this->hasCachedConfig()) {
            $this->setMergedConfig($this->getCachedConfig());
            return $this->mergedConfig;
        } else {
            $this->addConfigGlobPaths($this->getOptions()->getConfigGlobPaths());
            $this->addConfigStaticPaths($this->getOptions()->getConfigStaticPaths());
        }

        // Load the config files
        foreach ($this->paths as $path) {
            $this->addConfigByPath($path['path'], $path['type']);
        }

        // Merge all of the collected configs
        $this->mergedConfig = $this->getOptions()->getExtraConfig() ?: array();
        foreach ($this->configs as $config) {
            $this->mergedConfig = ArrayUtils::merge($this->mergedConfig, $config);
        }

        $configFile = $this->getOptions()->getConfigCacheFile();
        $this->writeArrayToFile($configFile, $this->getMergedConfig(false));

        return $this->mergedConfig;
    }

    /**
     * getMergedConfig
     *
     * @param  bool $returnConfigAsObject
     * @return mixed
     */
    public function getMergedConfig($returnConfigAsObject = true)
    {
        if ($returnConfigAsObject === true) {
            if ($this->mergedConfigObject === null) {
                $this->mergedConfigObject = new Config($this->mergedConfig);
            }
            return $this->mergedConfigObject;
        }

        return $this->mergedConfig;
    }

    /**
     * setMergedConfig
     *
     * @param  array $config
     * @return ConfigManager
     */
    public function setMergedConfig(array $config)
    {
        $this->mergedConfig = $config;
        $this->mergedConfigObject = null;
        return $this;
    }

    /**
     * Add an array of glob paths of config files to merge after loading modules
     *
     * @param  array|Traversable $globPaths
     * @return ConfigManager
     */
    public function addConfigGlobPaths($globPaths)
    {
        $this->addConfigPaths($globPaths, self::GLOB_PATH);
        return $this;
    }

    /**
     * Add a glob path of config files to merge after loading modules
     *
     * @param  string $globPath
     * @return ConfigManager
     */
    public function addConfigGlobPath($globPath)
    {
        $this->addConfigPath($globPath, self::GLOB_PATH);
        return $this;
    }

    /**
     * Add an array of static paths of config files to merge after loading modules
     *
     * @param  array|Traversable $staticPaths
     * @return ConfigManager
     */
    public function addConfigStaticPaths($staticPaths)
    {
        $this->addConfigPaths($staticPaths, self::STATIC_PATH);
        return $this;
    }

    /**
     * Add a static path of config files to merge after loading modules
     *
     * @param  string $staticPath
     * @return ConfigManager
     */
    public function addConfigStaticPath($staticPath)
    {
        $this->addConfigPath($staticPath, self::STATIC_PATH);
        return $this;
    }

    /**
     * Add an array of paths of config files to merge after loading modules
     *
     * @param  Traversable|array $paths
     * @param string $type
     * @throws Exception\InvalidArgumentException
     * @return ConfigManager
     */
    protected function addConfigPaths($paths, $type)
    {
        if ($paths instanceof Traversable) {
            $paths = ArrayUtils::iteratorToArray($paths);
        }

        if (!is_array($paths)) {
            throw new Exception\InvalidArgumentException(
                sprintf('Argument passed to %::%s() must be an array, '
                . 'implement the Traversable interface, or be an '
                . 'instance of Zend\Config\Config. %s given.',
                __CLASS__, __METHOD__, gettype($paths))
            );
        }

        foreach ($paths as $path) {
            $this->addConfigPath($path, $type);
        }
    }

    /**
     * Add a path of config files to load and merge after loading modules
     *
     * @param  string $path
     * @param  string $type
     * @throws Exception\InvalidArgumentException
     * @return ConfigManager
     */
    protected function addConfigPath($path, $type)
    {
        if (!is_string($path)) {
            throw new Exception\InvalidArgumentException(
                sprintf('Parameter to %s::%s() must be a string; %s given.',
                __CLASS__, __METHOD__, gettype($path))
            );
        }
        $this->paths[] = array('type' => $type, 'path' => $path);
        return $this;
    }

    /**
     * @param string $key
     * @param array|Traversable $config
     * @throws Exception\InvalidArgumentException
     * @return ConfigManager
     */
    protected function addConfig($key, $config)
    {
        if ($config instanceof Traversable) {
            $config = ArrayUtils::iteratorToArray($config);
        }

        if (!is_array($config)) {
            throw new Exception\InvalidArgumentException(
                sprintf('Config being merged must be an array, '
                . 'implement the Traversable interface, or be an '
                . 'instance of Zend\Config\Config. %s given.', gettype($config))
            );
        }

        $this->configs[$key] = $config;

        return $this;
    }

    /**
     * Given a path (glob or static), fetch the config and add it to the array
     * of configs to merge.
     *
     * @param string $path
     * @param string $type
     * @return ConfigManager
     */
    protected function addConfigByPath($path, $type)
    {
        switch ($type) {
            case self::STATIC_PATH:
                $this->addConfig($path, ConfigFactory::fromFile($path));
                break;

            case self::GLOB_PATH:
                // We want to keep track of where each value came from so we don't
                // use ConfigFactory::fromFiles() since it does merging internally.
                foreach (Glob::glob($path, Glob::GLOB_BRACE) as $file) {
                    $this->addConfig($file, ConfigFactory::fromFile($file));
                }
                break;
        }

        return $this;
    }

    /**
     * @return bool
     */
    protected function hasCachedConfig()
    {
        if (($this->getOptions()->getConfigCacheEnabled())
            && (file_exists($this->getOptions()->getConfigCacheFile()))
        ) {
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    protected function getCachedConfig()
    {
        return include $this->getOptions()->getConfigCacheFile();
    }

    /**
     * Get options.
     *
     * @return ConfigOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set options.
     *
     * @param ConfigOptions $options the value to be set
     * @return ConfigManager
     */
    public function setOptions(ConfigOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Write a simple array of scalars to a file
     *
     * @param  string $filePath
     * @param  array $array
     * @return ConfigManager
     */
    protected function writeArrayToFile($filePath, $array)
    {
        $content = "<?php\nreturn " . var_export($array, 1) . ';';
        file_put_contents($filePath, $content);
        return $this;
    }
}
