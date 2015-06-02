<?php

namespace Zend\Code\Generator;

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @author    Daan Biesterbos <daanbiesterbos@gmail.com>
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 *
 *
 * @method add
 */

use Zend\Code\Reflection\InterfaceReflection;
use Zend\Code\Generator\Exception\InvalidArgumentException;

class InterfaceGenerator extends AbstractGenerator
{
    const OBJECT_TYPE = 'interface';

    /**
     * @var FileGenerator
     */
    protected $containingFileGenerator = null;

    /**
     * @var string
     */
    protected $namespaceName = null;

    /**
     * @var DocBlockGenerator
     */
    protected $docBlock = null;

    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var bool
     */
    protected $flags = 0x00;

    /**
     * @var array Array of string names
     */
    protected $extendedInterfaces = array();

    /**
     * @var PropertyGenerator[] Array of constants
     */
    protected $constants = array();

    /**
     * @var MethodDeclarationGenerator[] Array of methods
     */
    protected $methods = array();

    /**
     * Build a Code Generation Php Object from a Class Reflection.
     *
     * @param InterfaceReflection $reflection
     *
     * @return InterfaceGenerator
     */
    public static function fromReflection(InterfaceReflection $reflection)
    {
        $ig = new static($reflection->getName());
        $ig->setSourceContent($ig->getSourceContent());
        $ig->setSourceDirty(false);

        if ($reflection->getDocComment() != '') {
            $ig->setDocBlock(DocBlockGenerator::fromReflection($reflection->getDocBlock()));
        }

        // set the namespace
        if ($reflection->inNamespace()) {
            $ig->setNamespaceName($reflection->getNamespaceName());
        }

        /* @var \Zend\Code\Reflection\ClassReflection[] $parentInterfaces */
        $parentInterfaces = $reflection->getParentInterfaces();
        $interfaceNames = array();
        if ($parentInterfaces) {
            foreach ($parentInterfaces as $parentInterface) {
                $interfaceNames[] = $parentInterface->getName();
            }
        }

        $ig->setExtendedInterfaces($interfaceNames);

        $constants = array();
        foreach ($reflection->getConstants() as $name => $value) {
            $constants[] = array(
                'name' => $name,
                'value' => $value
            );
        }

        $ig->addConstants($constants);

        $methods = array();
        foreach ($reflection->getMethods() as $reflectionMethod) {
            $className = ($ig->getNamespaceName()) ? $ig->getNamespaceName() . "\\" . $ig->getName() : $ig->getName();
            if ($reflectionMethod->getDeclaringInterface()->getName() == $className) {
                $methods[] = MethodDeclarationGenerator::fromReflection($reflectionMethod);
            }
        }

        $ig->addMethods($methods);

        return $ig;
    }

    /**
     * Generate from array.
     *
     * @configkey name           string        [required] Class Name
     * @configkey filegenerator  FileGenerator File generator that holds this class
     * @configkey namespacename  string        The namespace for this class
     * @configkey docblock       string        The docblock information
     * @configkey flags          int           Flags, one of InterfaceGenerator::FLAG_ABSTRACT InterfaceGenerator::FLAG_FINAL
     * @configkey extendedclass  string        Class which this class is extending
     * @configkey implementedinterfaces
     * @configkey properties
     * @configkey methods
     *
     * @throws InvalidArgumentException
     *
     * @param array $array
     *
     * @return InterfaceGenerator
     */
    public static function fromArray(array $array)
    {
        if (!isset($array['name'])) {
            throw new InvalidArgumentException(
                'Interface generator requires that a name is provided for this interface'
            );
        }

        $cg = new static($array['name']);
        foreach ($array as $name => $value) {
            // normalize key
            switch (strtolower(str_replace(array('.', '-', '_'), '', $name))) {
                case 'containingfile':
                    $cg->setContainingFileGenerator($value);
                    break;
                case 'namespacename':
                    $cg->setNamespaceName($value);
                    break;
                case 'docblock':
                    $docBlock = ($value instanceof DocBlockGenerator) ? $value : DocBlockGenerator::fromArray($value);
                    $cg->setDocBlock($docBlock);
                    break;
                case 'flags':
                    $cg->setFlags($value);
                    break;
                case 'extendedinterfaces':
                    $cg->setExtendedInterfaces($value);
                    break;
                case 'constants':
                    $cg->addConstants($value);
                    break;
                case 'methods':
                    $cg->addMethods($value);
                    break;
            }
        }

        return $cg;
    }

    /**
     * @param string            $name
     * @param string            $namespaceName
     * @param array|string      $flags
     * @param array             $parents
     * @param array             $methods
     * @param DocBlockGenerator $docBlock
     */
    public function __construct(
        $name = null,
        $namespaceName = null,
        $flags = null,
        $parents = array(),
        $methods = array(),
        $docBlock = null
    ) {
        if ($name !== null) {
            $this->setName($name);
        }
        if ($namespaceName !== null) {
            $this->setNamespaceName($namespaceName);
        }
        if ($flags !== null) {
            $this->setFlags($flags);
        }
        if (is_array($parents)) {
            $this->setExtendedInterfaces($parents);
        }
        if ($methods !== array()) {
            $this->addMethods($methods);
        }
        if ($docBlock !== null) {
            $this->setDocBlock($docBlock);
        }
    }

    /**
     * @param string $name
     *
     * @return InterfaceGenerator
     */
    public function setName($name)
    {
        if (strstr($name, '\\')) {
            $namespace = substr($name, 0, strrpos($name, '\\'));
            $name = substr($name, strrpos($name, '\\') + 1);
            $this->setNamespaceName($namespace);
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $namespaceName
     *
     * @return InterfaceGenerator
     */
    public function setNamespaceName($namespaceName)
    {
        $this->namespaceName = $namespaceName;

        return $this;
    }

    /**
     * @return string
     */
    public function getNamespaceName()
    {
        return $this->namespaceName;
    }

    /**
     * @param FileGenerator $fileGenerator
     *
     * @return InterfaceGenerator
     */
    public function setContainingFileGenerator(FileGenerator $fileGenerator)
    {
        $this->containingFileGenerator = $fileGenerator;

        return $this;
    }

    /**
     * @return FileGenerator
     */
    public function getContainingFileGenerator()
    {
        if (!is_object($this->containingFileGenerator)) {
            $this->containingFileGenerator = new FileGenerator();
        }
        return $this->containingFileGenerator;
    }

    /**
     * @param DocBlockGenerator $docBlock
     *
     * @return InterfaceGenerator
     */
    public function setDocBlock(DocBlockGenerator $docBlock)
    {
        $this->docBlock = $docBlock;

        return $this;
    }

    /**
     * @return DocBlockGenerator
     */
    public function getDocBlock()
    {
        return $this->docBlock;
    }

    /**
     * @param array|string $flags
     *
     * @return InterfaceGenerator
     */
    public function setFlags($flags)
    {
        if (is_array($flags)) {
            $flagsArray = $flags;
            $flags = 0x00;
            foreach ($flagsArray as $flag) {
                $flags |= $flag;
            }
        }
        // check that visibility is one of three
        $this->flags = $flags;

        return $this;
    }

    /**
     * @param string $flag
     *
     * @return InterfaceGenerator
     */
    public function addFlag($flag)
    {
        $this->setFlags($this->flags | $flag);

        return $this;
    }

    /**
     * @param string $flag
     *
     * @return InterfaceGenerator
     */
    public function removeFlag($flag)
    {
        $this->setFlags($this->flags & ~$flag);

        return $this;
    }

    /**
     * @param array|string $implementedInterfaces
     *
     * @return InterfaceGenerator
     */
    public function setExtendedInterfaces($implementedInterfaces)
    {
        // Ignore empty parameters...
        if (!empty($implementedInterfaces)) {
            // Convert to array
            if (!is_array($implementedInterfaces)) {
                $implementedInterfaces = array($implementedInterfaces);
            }
            $this->extendedInterfaces = $implementedInterfaces;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getExtendedInterfaces()
    {
        return $this->extendedInterfaces;
    }

    /**
     * @alias setExtendedInterfaces
     * @param array $implementedInterfaces
     *
     * @return InterfaceGenerator
     */
    public function setImplementedInterfaces(array $implementedInterfaces)
    {
        return $this->setExtendedInterfaces($implementedInterfaces);
    }

    /**
     * @alias getExtendedInterfaces
     *
     * @return array
     */
    public function getImplementedInterfaces()
    {
        return $this->getExtendedInterfaces();
    }

    /**
     * @param string $constantName
     *
     * @return PropertyGenerator|false
     */
    public function getConstant($constantName)
    {
        if (isset($this->constants[$constantName])) {
            return $this->constants[$constantName];
        }

        return false;
    }

    /**
     * @return PropertyGenerator[] indexed by constant name
     */
    public function getConstants()
    {
        return $this->constants;
    }

    /**
     * @param string $constantName
     *
     * @return bool
     */
    public function hasConstant($constantName)
    {
        return isset($this->constants[$constantName]);
    }

    /**
     * Add constant from PropertyGenerator.
     *
     * @param PropertyGenerator $constant
     *
     * @throws InvalidArgumentException
     *
     * @return InterfaceGenerator
     */
    public function addConstantFromGenerator(PropertyGenerator $constant)
    {
        $constantName = $constant->getName();

        if (isset($this->constants[$constantName])) {
            throw new InvalidArgumentException(sprintf(
                'A constant by name %s already exists in this class.',
                $constantName
            ));
        }

        if (!$constant->isConst()) {
            throw new InvalidArgumentException(sprintf(
                'The value %s is not defined as a constant.',
                $constantName
            ));
        }

        $this->constants[$constantName] = $constant;

        return $this;
    }

    /**
     * Add Constant.
     *
     * @param string $name
     * @param string $value
     *
     * @throws InvalidArgumentException
     *
     * @return InterfaceGenerator
     */
    public function addConstant($name, $value)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException(sprintf(
                '%s expects string for name',
                __METHOD__
            ));
        }

        if (!is_string($value) && !is_numeric($value)) {
            throw new InvalidArgumentException(sprintf(
                '%s expects value for constant, value must be a string or numeric',
                __METHOD__
            ));
        }

        return $this->addConstantFromGenerator(new PropertyGenerator($name, $value, PropertyGenerator::FLAG_CONSTANT));
    }

    /**
     * @param PropertyGenerator[]|array[] $constants
     *
     * @return InterfaceGenerator
     */
    public function addConstants(array $constants)
    {
        foreach ($constants as $constant) {
            if ($constant instanceof PropertyGenerator) {
                $this->addConstantFromGenerator($constant);
            } else {
                if (is_array($constant)) {
                    call_user_func_array(array($this, 'addConstant'), $constant);
                }
            }
        }

        return $this;
    }

    /**
     * Add method declaration
     *
     * @param array $methods
     *
     * @return InterfaceGenerator
     */
    public function addMethods(array $methods)
    {
        foreach ($methods as $method) {
            if ($method instanceof MethodDeclarationGenerator) {
                $this->addMethodFromGenerator($method);
            } else {
                if (is_string($method)) {
                    $this->addMethod($method);
                } elseif (is_array($method)) {
                    call_user_func_array(array($this, 'addMethod'), $method);
                }
            }
        }

        return $this;
    }

    /**
     * Add method declaration from scalars.
     *
     * @param string $name
     * @param array  $parameters
     * @param int    $flags
     * @param string $body
     * @param string $docBlock
     *
     * @throws InvalidArgumentException
     *
     * @return InterfaceGenerator
     */
    public function addMethod(
        $name = null,
        array $parameters = array(),
        $flags = MethodDeclarationGenerator::FLAG_PUBLIC,
        $body = null,
        $docBlock = null
    ) {
        if (!is_string($name)) {
            throw new InvalidArgumentException(sprintf(
                '%s::%s expects string for name',
                get_class($this),
                __FUNCTION__
            ));
        }

        return $this->addMethodFromGenerator(new MethodDeclarationGenerator($name, $parameters, $flags, $body, $docBlock));
    }

    /**
     * Add Method from MethodDeclarationGenerator.
     *
     * @param MethodDeclarationGenerator $method
     *
     * @throws InvalidArgumentException
     *
     * @return InterfaceGenerator
     */
    public function addMethodFromGenerator(MethodDeclarationGenerator $method)
    {
        $methodName = $method->getName();

        if ($this->hasMethod($methodName)) {
            throw new InvalidArgumentException(sprintf(
                'A method by name %s already exists in this class.',
                $methodName
            ));
        }

        $this->methods[strtolower($methodName)] = $method;

        return $this;
    }

    /**
     * @return MethodDeclarationGenerator[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param string $methodName
     *
     * @return MethodDeclarationGenerator|false
     */
    public function getMethod($methodName)
    {
        return $this->hasMethod($methodName) ? $this->methods[strtolower($methodName)] : false;
    }

    /**
     * @param string $methodName
     *
     * @return InterfaceGenerator
     */
    public function removeMethod($methodName)
    {
        if ($this->hasMethod($methodName)) {
            unset($this->methods[strtolower($methodName)]);
        }

        return $this;
    }

    /**
     * @param string $methodName
     *
     * @return bool
     */
    public function hasMethod($methodName)
    {
        return isset($this->methods[strtolower($methodName)]);
    }

    /**
     * @return bool
     */
    public function isSourceDirty()
    {
        if (($docBlock = $this->getDocBlock()) && $docBlock->isSourceDirty()) {
            return true;
        }

        foreach ($this->getMethods() as $method) {
            if ($method->isSourceDirty()) {
                return true;
            }
        }

        return parent::isSourceDirty();
    }

    /**
     * @inherit Zend\Code\Generator\GeneratorInterface
     */
    public function generate()
    {
        if (!$this->isSourceDirty()) {
            $output = $this->getSourceContent();
            if (!empty($output)) {
                return $output;
            }
        }

        $output = '';

        if (null !== ($namespace = $this->getNamespaceName())) {
            $output .= "namespace {$namespace};".self::LINE_FEED.self::LINE_FEED;
        }

        $uses = $this->getUses();
        if (!empty($uses)) {
            foreach ($uses as $use) {
                $use = (!array($use)) ? array($use) : $use;
                $useClass = "use {$use[0]}";
                if (isset($use[1])) {
                    $useClass .= " as {$use[1]}";
                }
                if (!empty($useClass)) {
                    $output .= $useClass.';'.self::LINE_FEED;
                }
            }
            $output .= self::LINE_FEED;
        }

        if (null !== ($docBlock = $this->getDocBlock())) {
            $docBlock->setIndentation('');
            $output .= $docBlock->generate();
        }

        $output .= "interface {$this->getName()}";

        $implemented = $this->getExtendedInterfaces();
        if (!empty($implemented)) {
            $output .= ' extends '.implode(', ', $implemented);
        }

        $output .= self::LINE_FEED.'{'.self::LINE_FEED.self::LINE_FEED;

        $constants = $this->getConstants();
        foreach ($constants as $constant) {
            $output .= $constant->generate().self::LINE_FEED.self::LINE_FEED;
        }

        $methods = $this->getMethods();
        foreach ($methods as $method) {
            $output .= $method->generate().self::LINE_FEED;
        }

        $output .= self::LINE_FEED.'}'.self::LINE_FEED;

        return $output;
    }

    /**
     * Add "use" class or interface
     *
     * @param string      $use
     * @param string|null $useAlias
     *
     * @return InterfaceGenerator
     */
    public function addUse($use, $useAlias = null)
    {
        $this->getContainingFileGenerator()->setUse($use, $useAlias);
    }

    /**
     * Get "uses" of classes and interfaces
     *
     * @return array
     */
    public function getUses()
    {
        return $this->getContainingFileGenerator()->getUses();
    }
}
