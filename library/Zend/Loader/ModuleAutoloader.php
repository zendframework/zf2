<?php

namespace Zend\Loader;

use SplFileInfo,
    Traversable;

class ModuleAutoloader implements SplAutoloader
{
    /**
     * @var array An array of module paths to scan
     */
    protected $paths = array();

    /**
     * @var array An array of modulename => path 
     */
    protected $explicitPaths = array();

    /**
     * Constructor
     *
     * Allow configuration of the autoloader via the constructor.
     * 
     * @param  null|array|Traversable $options 
     * @return void
     */
    public function __construct($options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Configure the autoloader
     *
     * In most cases, $options should be either an associative array or 
     * Traversable object.
     * 
     * @param  array|Traversable $options 
     * @return SplAutoloader
     */
    public function setOptions($options)
    {
        $this->registerPaths($options);
        return $this;
    }

    /**
     * Autoload a class
     *
     * @param   $class
     * @return  mixed
     *          False [if unable to load $class]
     *          get_class($class) [if $class is successfully loaded]
     */
    public function autoload($class)
    {
        // Limit scope of this autoloader
        if (substr($class, -7) !== '\Module') {
            return false;
        }
        $moduleName = substr($class, 0, -7);
        if (isset($this->explicitPaths[$moduleName])) {
            if ($classLoaded = $this->loadModuleFromDir($this->explicitPaths[$moduleName], $class)) {
                return $classLoaded;
            } elseif ($classLoaded = $this->loadModuleFromPhar($this->explicitPaths[$moduleName], $class)) {
                return $classLoaded;
            }
        }

        $moduleClassPath = str_replace('\\', DIRECTORY_SEPARATOR, $moduleName);

        foreach ($this->paths as $path) {
            $path = $path . $moduleClassPath;
            if ($classLoaded = $this->loadModuleFromDir($path, $class)) {
                return $classLoaded;
            } 
            // No directory with Module.php, searching for phars
            //$moduleName = substr($class, 0, strpos($class, '\\'));

            // Find executable phars
            $matches = glob($path . '.{phar,phar.gz,phar.bz2,phar.tar,phar.tar.gz,phar.tar.bz2,phar.zip,tar,tar.gz,tar.bz2,zip}', GLOB_BRACE);
            if (count($matches) == 0) {
                return false;
            }
            foreach ($matches as $phar) {
                if ($classLoaded = $this->loadModuleFromPhar($phar, $class)) {
                    return $classLoaded;
                }
            }
        }
        return false;
    }

    /**
     * loadModuleFromDir 
     * 
     * @param string $dirPath 
     * @param string $class 
     * @return  mixed
     *          False [if unable to load $class]
     *          get_class($class) [if $class is successfully loaded]
     */
    protected function loadModuleFromDir($dirPath, $class)
    {
        $file = new SplFileInfo($dirPath . '/Module.php');
        if ($file->isReadable() && $file->isFile()) {
            // Found directory with Module.php in it
            require_once $file->getRealPath();
            if (class_exists($class)) {
                return $class;
            }
        }
        return false;
    }

    /**
     * loadModuleFromPhar 
     * 
     * @param string $pharPath 
     * @param string $class 
     * @return  mixed
     *          False [if unable to load $class]
     *          get_class($class) [if $class is successfully loaded]
     */
    protected function loadModuleFromPhar($pharPath, $class)
    {
        $file = new SplFileInfo($pharPath);
        if (!$file->isReadable() || !$file->isFile()) {
            return false;
        }
        if (strpos($file->getRealPath(), '.phar') !== false) {
            // First see if the stub makes the Module class available
            require_once $file->getRealPath();
            if (class_exists($class)) {
                return $class;
            }
        }
        // No stub, or stub did not provide Module class; try Module.php directly
        $moduleClassFile = 'phar://' . $file->getRealPath() . '/Module.php';
        $file = new SplFileInfo($moduleClassFile);
        if ($file->isReadable() && $file->isFile()) {
            require_once $moduleClassFile;
            if (class_exists($class)) {
                return $class;
            }
        }
        return false;
    }

    /**
     * Register the autoloader with spl_autoload registry
     * 
     * @return void
     */
    public function register()
    {
        spl_autoload_register(array($this, 'autoload'));
    }

    /**
     * Unregister the autoloader with spl_autoload registry
     * 
     * @return void
     */
    public function unregister()
    {
        $test = spl_autoload_unregister(array($this, 'autoload'));
    }

    /**
     * registerPaths 
     * 
     * @param array|Traversable $paths 
     * @return ModuleLoader
     */
    public function registerPaths($paths)
    {
        if (is_array($paths) || $paths instanceof Traversable) {
            foreach ($paths as $module => $path) {
                if (is_string($module)) {
                    $this->registerPath($path, $module);
                } else {
                    $this->registerPath($path);
                }
            } 
        } else {
            throw new \InvalidArgumentException(
                'Parameter to \\Zend\\Loader\\ModuleAutoloader\'s '
                . 'registerPaths method must be an array or '
                . 'implement the \\Traversable interface'
            );
        }
        return $this;
    }

    /**
     * registerPath 
     * 
     * @param string $path 
     * @param string $moduleName 
     * @return ModuleLoader
     */
    public function registerPath($path, $moduleName = false)
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid path provided; must be a string, received %s',
                gettype($path)
            ));
        }
        if ($moduleName) {
            $this->explicitPaths[$moduleName] = static::normalizePath($path);
        } else {
            $this->paths[] = static::normalizePath($path);
        }
        return $this;
    }

    /**
     * getPaths 
     *
     * This is primarily for unit testing, but could have other uses.
     * 
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * Normalize a path for insertion in the stack
     * 
     * @param  string $path 
     * @return string
     */
    public static function normalizePath($path)
    {
        $path = rtrim($path, '/');
        $path = rtrim($path, '\\');
        $path .= DIRECTORY_SEPARATOR;
        return $path;
    }
}
