<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Loader;

use Composer\Autoload\ClassLoader;

// Grab SplAutoloader interface
require_once __DIR__ . '/SplAutoloader.php';

/**
 * Adapts Composer's autoloader so that it is used for class loading
 * in the application.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class ComposerAutoloader implements SplAutoloader
{
    const LOAD_NS         = 'namespaces';
    const LOAD_PSR4       = 'psr4';
    const LOAD_CLASS_MAP  = 'classmap';

    /**
     * @var ClassLoader
     */
    protected $composerAutoloader;

    /**
     * Constructor
     *
     * @param  null|array|ClassLoader $options
     */
    public function __construct($options = null)
    {
        if (null !== $options) {
            if (is_array($options) && isset($options['composer_autoloader'])) {
                $options = $options['composer_autoloader'];
            }

            if ($options instanceof ClassLoader) {
                $this->composerAutoloader = $options;
                return;
            }
        }

        throw new Exception\RuntimeException('Composer autloader instance must be supplied');
    }

    /**
     * Configure autoloader
     *
     * Allows specifying both "namespace" and "prefix" pairs, using the
     * following structure:
     * <code>
     * array(
     *     'namespaces' => array(
     *         'Zend'     => '/path/to/Zend/library',
     *         'Doctrine' => '/path/to/Doctrine/library',
     *     ),
     *     'psr4' => array(
     *         'Foo' => '/path/to/module/Foo/src',
     *     ),
     *     'classmap' => array(
     *         'Foo/Service/Bar' => '/path/to/module/Foo/src/Service/Bar.php',
     *     ),
     * )
     * </code>
     *
     * @param  array|\Traversable $options
     * @throws Exception\InvalidArgumentException
     * @return ComposerAutoloader
     */
    public function setOptions($options)
    {
        if (!is_array($options) && !($options instanceof \Traversable)) {
            require_once __DIR__ . '/Exception/InvalidArgumentException.php';
            throw new Exception\InvalidArgumentException('Options must be either an array or Traversable');
        }

        foreach ($options as $type => $pairs) {
            if (!is_array($pairs)) {
                continue;
            }

            switch ($type) {
                case self::LOAD_NS :
                    foreach ($pairs as $namespace => $path) {
                        $this->composerAutoloader->set($namespace, $path);
                    }
                    break;
                case self::LOAD_PSR4 :
                    foreach ($pairs as $namespace => $path) {
                        $this->composerAutoloader->setPsr4($namespace, $path);
                    }
                    break;
                case self::LOAD_CLASS_MAP :
                    $this->composerAutoloader->addClassMap($pairs);
                    break;
                default:
                    // ignore
            }
        }

        return $this;
    }

    /**
     * Does nothing; relies on Composer autoloader.
     *
     * @return void
     */
    public function autoload($class)
    {
        return true;
    }

    /**
     * Does nothing; Composer autoloader has already been registered.
     *
     * @return void
     */
    public function register()
    {
        return;
    }
}
