<?php

namespace Zend\Code\Generator;

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @author    Daan Biesterbos <daanbiesterbos@gmail.com>
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

use Zend\Code\Generator\Exception\InvalidArgumentException;
use Zend\Code\Reflection\MethodDeclarationReflection;

class MethodDeclarationGenerator extends AbstractMemberGenerator
{
    /**
     * @var DocBlockGenerator
     */
    protected $docBlock = null;

    /**
     * @var ParameterGenerator[]
     */
    protected $parameters = array();

    /**
     * @param MethodDeclarationReflection $reflectionMethod
     *
     * @return MethodGenerator
     */
    public static function fromReflection(MethodDeclarationReflection $reflectionMethod)
    {
        $method = new static();
        if (!$reflectionMethod->isPublic()) {
            throw new InvalidArgumentException('Interfaces can only contain public methods!');
        }

        $method->setSourceDirty(false);
        if ($reflectionMethod->getDocComment() != '') {
            $method->setDocBlock(DocBlockGenerator::fromReflection($reflectionMethod->getDocBlock()));
        }

        $method->setStatic($reflectionMethod->isStatic());
        $method->setName($reflectionMethod->getName());

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $method->setParameter(ParameterGenerator::fromReflection($reflectionParameter));
        }

        return $method;
    }

    /**
     * Generate from array.
     *
     * @configkey name           string        [required] Class Name
     * @configkey docblock       string        The docblock information
     * @configkey flags          int           Flags, one of MethodGenerator::FLAG_ABSTRACT MethodGenerator::FLAG_FINAL
     * @configkey parameters     string        Class which this class is extending
     * @configkey body           string
     * @configkey abstract       bool
     * @configkey final          bool
     * @configkey static         bool
     * @configkey visibility     string
     *
     * @throws InvalidArgumentException
     *
     * @param array $array
     *
     * @return MethodDeclarationGenerator
     */
    public static function fromArray(array $array)
    {
        if (!isset($array['name'])) {
            throw new InvalidArgumentException(
                'Method generator requires that a name is provided for this object'
            );
        }

        $method = new static($array['name']);
        $method->setVisibility(self::VISIBILITY_PUBLIC);
        foreach ($array as $name => $value) {
            // normalize key
            switch (strtolower(str_replace(array('.', '-', '_'), '', $name))) {
                case 'docblock':
                    $docBlock = ($value instanceof DocBlockGenerator) ? $value : DocBlockGenerator::fromArray($value);
                    $method->setDocBlock($docBlock);
                    break;
                case 'flags':
                    $method->setFlags($value);
                    break;
                case 'parameters':
                    $method->setParameters($value);
                    break;
                case 'static':
                    $method->setStatic($value);
                    break;
            }
        }

        return $method;
    }

    /**
     * @param string                   $name
     * @param array                    $parameters
     * @param int                      $flags
     * @param string                   $body
     * @param DocBlockGenerator|string $docBlock
     */
    public function __construct(
        $name = null,
        array $parameters = array(),
        $flags = self::FLAG_PUBLIC,
        $body = null,
        $docBlock = null
    ) {
        if ($name) {
            $this->setName($name);
        }
        if ($parameters) {
            $this->setParameters($parameters);
        }
        if ($flags !== self::FLAG_PUBLIC) {
            $this->setFlags($flags);
        }
        if ($docBlock) {
            $this->setDocBlock($docBlock);
        }
    }

    /**
     * Export pre configured method generator.
     *
     * @return MethodGenerator
     */
    public function getMethodImplementation()
    {
        $generator = new MethodGenerator($this->getName());
        $generator->setBody('// TODO: Implement logic');
        $generator->setParameters($this->getParameters());
        $generator->setIndentation($this->getIndentation());
        $generator->setVisibility(MethodGenerator::VISIBILITY_PUBLIC);
        $generator->setStatic($this->isStatic());
        $generator->setFinal($this->isFinal());
        $generator->setDocBlock($this->getDocBlock());

        return $generator;
    }

    /**
     * @param array $parameters
     *
     * @return $this
     */
    public function setParameters(array $parameters)
    {
        foreach ($parameters as $parameter) {
            $this->setParameter($parameter);
        }

        return $this;
    }

    /**
     * @param ParameterGenerator|array|string $parameter
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setParameter($parameter)
    {
        if (is_string($parameter)) {
            $parameter = new ParameterGenerator($parameter);
        }

        if (is_array($parameter)) {
            $parameter = ParameterGenerator::fromArray($parameter);
        }

        if (!$parameter instanceof ParameterGenerator) {
            throw new InvalidArgumentException(sprintf(
                '%s is expecting either a string, array or an instance of %s\ParameterGenerator',
                __METHOD__,
                __NAMESPACE__
            ));
        }

        $this->parameters[$parameter->getName()] = $parameter;

        return $this;
    }

    /**
     * @return ParameterGenerator[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function generate()
    {
        $output = '';

        $indent = $this->getIndentation();

        if (($docBlock = $this->getDocBlock()) !== null) {
            $docBlock->setIndentation($indent);
            $output .= $docBlock->generate();
        }

        $output .= $indent;

        $output .= self::VISIBILITY_PUBLIC
            .(($this->isStatic()) ? ' static' : '')
            .' function '.$this->getName().'(';

        $parameters = $this->getParameters();
        $parameterOutput = array();
        if (!empty($parameters)) {
            foreach ($parameters as $parameter) {
                $parameterOutput[] = $parameter->generate();
            }

            $output .= implode(', ', $parameterOutput);
        }
        $output .= ');';

        return $output;
    }

    /**
     * @ignore
     * @throws \LogicException
     * @param  bool $isAbstract
     *
     * @return void
     */
    public function setAbstract($isAbstract)
    {
        throw new \LogicException(
            "Abstract methods are not supported. To generate abstract methods use the MethodGenerator. " .
            "The intended use of this generator is to work with interfaces. See method getMethodImplementation() to export a pre configured instance of this method " .
            "declaration that you can use to generate your (abstract) class method."
        );
    }

    /**
     * @ignore
     * @throws \LogicException
     *
     * @return bool
     */
    public function isAbstract()
    {
        throw new \LogicException(
            "Abstract methods are not supported. To generate abstract methods use the MethodGenerator. " .
            "The intended use of this generator is to work with interfaces. See method getMethodImplementation() to export a pre configured instance of this method " .
            "declaration that you can use to generate your (abstract) class method."
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->generate();
    }
}
