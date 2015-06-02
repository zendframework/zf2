<?php

namespace Zend\Code\Reflection;

/**
 * Zend Framework (http://framework.zend.com/).
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

use ReflectionClass;
use Zend\Code\Annotation\AnnotationCollection;
use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Scanner\AnnotationScanner;
use Zend\Code\Scanner\FileScanner;
use Zend\Code\Exception\ExceptionInterface;

class InterfaceReflection extends ReflectionClass implements ReflectionInterface
{
    /**
     * @var AnnotationScanner
     */
    protected $annotations = null;

    /**
     * @var DocBlockReflection
     */
    protected $docBlock = null;

    /**
     * Return the reflection file of the declaring file.
     *
     * @return FileReflection
     */
    public function getDeclaringFile()
    {
        $instance = new FileReflection($this->getFileName());

        return $instance;
    }

    /**
     * Return the classes DocBlock reflection object.
     *
     * @return DocBlockReflection
     *
     * @throws ExceptionInterface for missing DocBock or invalid reflection class
     */
    public function getDocBlock()
    {
        if (isset($this->docBlock)) {
            return $this->docBlock;
        }

        if ('' == $this->getDocComment()) {
            return false;
        }

        $this->docBlock = new DocBlockReflection($this);

        return $this->docBlock;
    }

    /**
     * @param AnnotationManager $annotationManager
     *
     * @return AnnotationCollection
     */
    public function getAnnotations(AnnotationManager $annotationManager)
    {
        $docComment = $this->getDocComment();

        if ($docComment == '') {
            return false;
        }

        if ($this->annotations) {
            return $this->annotations;
        }

        $fileScanner = $this->createFileScanner($this->getFileName());
        $nameInformation = $fileScanner->getClassNameInformation($this->getName());

        if (!$nameInformation) {
            return false;
        }

        $this->annotations = new AnnotationScanner($annotationManager, $docComment, $nameInformation);

        return $this->annotations;
    }

    /**
     * Return the start line of the class.
     *
     * @param bool $includeDocComment
     *
     * @return int
     */
    public function getStartLine($includeDocComment = false)
    {
        if ($includeDocComment && $this->getDocComment() != '') {
            return $this->getDocBlock()->getStartLine();
        }

        return parent::getStartLine();
    }

    /**
     * Return the contents of the class.
     *
     * @param bool $includeDocBlock
     *
     * @return string
     */
    public function getContents($includeDocBlock = true)
    {
        $fileName = $this->getFileName();

        if (false === $fileName || !file_exists($fileName)) {
            return '';
        }

        $filelines = file($fileName);
        $startnum = $this->getStartLine($includeDocBlock);
        $endnum = $this->getEndLine();

        // Ensure we get between the open and close braces
        $lines = array_slice($filelines, $startnum, $endnum);
        array_unshift($lines, $filelines[$startnum - 1]);

        return strstr(implode('', $lines), '{');
    }

    /**
     * Get all reflection objects of parent interfaces.
     *
     * @return InterfaceReflection[]
     */
    public function getParentInterfaces()
    {
        if (parent::isInterface()) {
            $phpReflections = parent::getInterfaces();
            $zendReflections = array();
            while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
                $instance = new self($phpReflection->getName());
                $zendReflections[] = $instance;
                unset($phpReflection);
            }
            unset($phpReflections);

            return $zendReflections;
        }

        return array();
    }

    /**
     * Return method reflection by name.
     *
     * @param string $name
     *
     * @return MethodDeclarationReflection
     */
    public function getMethod($name)
    {
        $method = new MethodDeclarationReflection($this->getName(), parent::getMethod($name)->getName());

        return $method;
    }

    /**
     * Get reflection objects of all methods.
     *
     * @param int $filter
     *
     * @return MethodDeclarationReflection[]
     */
    public function getMethods($filter = -1)
    {
        $methods = array();
        foreach (parent::getMethods($filter) as $method) {
            $instance = new MethodDeclarationReflection($this->getName(), $method->getName());
            $methods[] = $instance;
        }

        return $methods;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return parent::__toString();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return parent::__toString();
    }

    /**
     * Creates a new FileScanner instance.
     *
     * By having this as a seperate method it allows the method to be overridden
     * if a different FileScanner is needed.
     *
     * @param string $filename
     *
     * @return FileScanner
     */
    protected function createFileScanner($filename)
    {
        return new FileScanner($filename);
    }
}
