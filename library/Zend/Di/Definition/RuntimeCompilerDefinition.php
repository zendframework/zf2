<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Di\Definition;

use ReflectionException;
use Zend\Code\Scanner\AggregateDirectoryScanner;
use Zend\Code\Scanner\DirectoryScanner;
use Zend\Code\Scanner\FileScanner;

class RuntimeCompilerDefinition extends RuntimeDefinition
{
    protected $directoryScanner;
    protected $allowReflectionExceptions = false;

    /**
     * Constructor
     *
     * @param null|IntrospectionStrategy $introspectionStrategy
     */
    public function __construct(IntrospectionStrategy $introspectionStrategy = null)
    {
        parent::__construct($introspectionStrategy);
        $this->setDirectoryScanner(new AggregateDirectoryScanner());
    }

    /**
     * @param DirectoryScanner $directoryScanner
     * @return self
     */
    protected function setDirectoryScanner(DirectoryScanner $directoryScanner)
    {
        $this->directoryScanner = $directoryScanner;
        return $this;
    }

    /**
     * @return DirectoryScanner
     */
    protected function getDirectoryScanner()
    {
        return $this->directoryScanner;
    }

    /**
     * @param bool $allowReflectionExceptions
     * @return self
     */
    public function setAllowReflectionExceptions($allowReflectionExceptions = true)
    {
        $this->allowReflectionExceptions = $allowReflectionExceptions;
        return $this;
    }

    /**
     * @return bool
     */
    protected function allowReflectionExceptions()
    {
        return $this->allowReflectionExceptions;
    }

    /**
     * Add directory
     *
     * @param string $directory
     */
    public function addDirectory($directory)
    {
        $this->addDirectoryScanner(new DirectoryScanner($directory));
    }

    /**
     * Add directory scanner
     *
     * @param DirectoryScanner $directoryScanner
     */
    public function addDirectoryScanner(DirectoryScanner $directoryScanner)
    {
        $this->getDirectoryScanner()->addDirectoryScanner($directoryScanner);
    }

    /**
     * Add code scanner file
     *
     * @param FileScanner $fileScanner
     */
    public function addCodeScannerFile(FileScanner $fileScanner)
    {
        $this->getDirectoryScanner()->addFileScanner(
            $fileScanner
        );
    }

    /**
     * Compile
     */
    public function compile()
    {
        foreach ($this->getDirectoryScanner()->getClassNames() as $class) {
            $this->compileClass($class);
        }
    }

    /**
     * Compile Class
     */
    public function compileClass($class)
    {
        try {
            $this->processClass($class);
        } catch (ReflectionException $exception) {
            if ($this->allowReflectionExceptions()) {
                return;
            }
            throw $exception;
        }
    }

    /**
     * @return ArrayDefinition
     */
    public function toArrayDefinition()
    {
        return new ArrayDefinition($this->classes);
    }
}
