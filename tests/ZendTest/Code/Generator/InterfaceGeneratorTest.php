<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @author    Daan Biesterbos <daanbiesterbos@gmail.com>
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Generator;

use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\InterfaceGenerator;
use Zend\Code\Generator\MethodDeclarationGenerator;
use Zend\Code\Generator\PropertyGenerator;
use Zend\Code\Reflection\InterfaceReflection;

/**
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class InterfaceGeneratorTest extends \PHPUnit_Framework_TestCase
{

    public function testNameAccessors()
    {
        $interfaceGenerator = new InterfaceGenerator();
        $interfaceGenerator->setName('FooInterface');
        $this->assertEquals($interfaceGenerator->getName(), 'FooInterface');
    }

    public function testClassDocBlockAccessors()
    {
        $docBlockGenerator = new DocBlockGenerator();
        $interfaceGenerator = new InterfaceGenerator();
        $interfaceGenerator->setDocBlock($docBlockGenerator);
        $this->assertSame($docBlockGenerator, $interfaceGenerator->getDocBlock());
    }

    public function testExtendedInterfacesAccessors()
    {
        $interfaceGenerator = new InterfaceGenerator();
        $interfaceGenerator->setExtendedInterfaces(array('FooInterface', 'BarInterface'));
        $this->assertEquals($interfaceGenerator->getExtendedInterfaces(), array('FooInterface', 'BarInterface'));
    }

    public function testMethodAccessors()
    {
        $interfaceGenerator = new InterfaceGenerator();
        $interfaceGenerator->addMethods(array(
            'doSomething',
            new MethodDeclarationGenerator('doSomethingElse')
        ));

        $methods = $interfaceGenerator->getMethods();
        $this->assertEquals(count($methods), 2);
        $this->isInstanceOf(current($methods), '\Zend\Code\Generator\PhpMethod');

        $method = $interfaceGenerator->getMethod('doSomething');
        $this->isInstanceOf($method, '\Zend\Code\Generator\PhpMethod');
        $this->assertEquals($method->getName(), 'doSomething');

        // add a new property
        $interfaceGenerator->addMethod('pretendYouAreATrain');
        $this->assertEquals(count($interfaceGenerator->getMethods()), 3);
    }

    public function testSetMethodNoMethodOrArrayThrowsException()
    {
        $interfaceGenerator = new InterfaceGenerator();

        $this->setExpectedException(
            'Zend\Code\Generator\Exception\ExceptionInterface',
            'Zend\Code\Generator\InterfaceGenerator::addMethod expects string for name'
        );

        $interfaceGenerator->addMethod(true);
    }

    public function testSetMethodNameAlreadyExistsThrowsException()
    {
        $methodA = new MethodDeclarationGenerator();
        $methodA->setName("foo");
        $methodB = new MethodDeclarationGenerator();
        $methodB->setName("foo");

        $interfaceGenerator = new InterfaceGenerator();
        $interfaceGenerator->addMethodFromGenerator($methodA);

        $this->setExpectedException(
            'Zend\Code\Generator\Exception\InvalidArgumentException',
            'A method by name foo already exists in this class.'
        );

        $interfaceGenerator->addMethodFromGenerator($methodB);
    }

    /**
     * @group ZF-7361
     */
    public function testHasMethod()
    {
        $interfaceGenerator = new InterfaceGenerator();
        $interfaceGenerator->addMethod('methodOne');

        $this->assertTrue($interfaceGenerator->hasMethod('methodOne'));
    }

    public function testRemoveMethod()
    {
        $interfaceGenerator = new InterfaceGenerator();
        $interfaceGenerator->addMethod('methodOne');
        $this->assertTrue($interfaceGenerator->hasMethod('methodOne'));

        $interfaceGenerator->removeMethod('methodOne');
        $this->assertFalse($interfaceGenerator->hasMethod('methodOne'));
    }


    public function testToString()
    {
        $interfaceGenerator = InterfaceGenerator::fromArray(array(
            'name' => 'SampleInterface',
            'extendedInterfaces' => array('FooInterface', 'BarInterface'),
            'constants' => array(
                array('FOO', 0),
                array('name' => 'BAR', 'value' => 1)
            ),
            'methods' => array(
                array('name' => 'baz')
            ),
        ));

        $expectedOutput = <<<EOS
interface SampleInterface extends FooInterface, BarInterface
{

    const FOO = 0;

    const BAR = 1;

    public function baz();

}

EOS;

        $output = $interfaceGenerator->generate();
        $this->assertEquals($expectedOutput, $output, $output);
    }

    /**
     * @group 4988
     */
    public function testNonNamespaceInterfaceReturnsAllMethods()
    {
        require_once __DIR__ . '/../TestAsset/NonNamespaceInterface.php';

        $refl = new InterfaceReflection('NonNamespaceInterface');
        $interfaceGenerator = InterfaceGenerator::fromReflection($refl);
        $this->assertCount(1, $interfaceGenerator->getMethods());
    }

    /**
     * @group ZF-9602
     */
    public function testSetextendedclassShouldIgnoreEmptyClassnameOnGenerate()
    {
        $interfaceGenerator = new InterfaceGenerator();
        $interfaceGenerator
            ->setName('MyInterface')
            ->setExtendedInterfaces('');

        $expected = <<<CODE
interface MyInterface
{


}

CODE;
        $this->assertEquals($expected, $interfaceGenerator->generate());
    }

    /**
     * @group namespace
     */
    public function testCodeGenerationShouldTakeIntoAccountNamespacesFromReflection()
    {
        $refl = new InterfaceReflection('ZendTest\Code\Generator\TestAsset\InterfaceWithNamespace');
        $interfaceGenerator = InterfaceGenerator::fromReflection($refl);
        $this->assertEquals('ZendTest\Code\Generator\TestAsset', $interfaceGenerator->getNamespaceName());
        $this->assertEquals('InterfaceWithNamespace', $interfaceGenerator->getName());
        $expected = <<<CODE
namespace ZendTest\Code\Generator\\TestAsset;

interface InterfaceWithNamespace extends Iterator, Traversable, Countable
{


}

CODE;
        $received = $interfaceGenerator->generate();
        $this->assertEquals($expected, $received, $received);
    }

    /**
     * @group namespace
     */
    public function testSetNameShouldDetermineIfNamespaceSegmentIsPresent()
    {
        $interfaceGenerator = new InterfaceGenerator();
        $interfaceGenerator->setName('My\Namespaced\FooInterface');
        $this->assertEquals('My\Namespaced', $interfaceGenerator->getNamespaceName());
    }

    /**
     * @group namespace
     */
    public function testPassingANamespacedNameShouldGenerateANamespaceDeclaration()
    {
        $interfaceGenerator = new InterfaceGenerator();
        $interfaceGenerator->setName('My\Namespaced\FunClass');
        $received = $interfaceGenerator->generate();
        $this->assertContains('namespace My\Namespaced;', $received, $received);
    }

    /**
     * @group namespace
     */
    public function testPassingANamespacedClassnameShouldGenerateAClassnameWithoutItsNamespace()
    {
        $interfaceGenerator = new InterfaceGenerator();
        $interfaceGenerator->setName('My\Namespaced\FooBarInterface');
        $received = $interfaceGenerator->generate();
        $this->assertContains('interface FooBarInterface', $received, $received);
    }

    /**
     * @group ZF2-151
     */
    public function testAddUses()
    {
        $interfaceGenerator = new InterfaceGenerator();
        $interfaceGenerator->setName('My\FooInterface');
        $interfaceGenerator->addUse('My\First\Use\FooInterface');
        $interfaceGenerator->addUse('My\Second\Use\FooInterface', 'MyAlias');
        $generated = $interfaceGenerator->generate();

        $this->assertContains('use My\First\Use\FooInterface;', $generated);
        $this->assertContains('use My\Second\Use\FooInterface as MyAlias;', $generated);
    }

    /**
     * @group 4990
     */
    public function testAddOneUseTwiceOnlyAddsOne()
    {
        $interfaceGenerator = new InterfaceGenerator();
        $interfaceGenerator->setName('My\FooInterface');
        $interfaceGenerator->addUse('My\First\Use\FooInterface');
        $interfaceGenerator->addUse('My\First\Use\FooInterface');
        $generated = $interfaceGenerator->generate();

        $this->assertCount(1, $interfaceGenerator->getUses());

        $this->assertContains('use My\First\Use\FooInterface;', $generated);
    }

    /**
     * @group 4990
     */
    public function testAddOneUseWithAliasTwiceOnlyAddsOne()
    {
        $interfaceGenerator = new InterfaceGenerator();
        $interfaceGenerator->setName('My\FooInterface');
        $interfaceGenerator->addUse('My\First\Use\FooInterface', 'MyAlias');
        $interfaceGenerator->addUse('My\First\Use\FooInterface', 'MyAlias');
        $generated = $interfaceGenerator->generate();

        $this->assertCount(1, $interfaceGenerator->getUses());

        $this->assertContains('use My\First\Use\FooInterface as MyAlias;', $generated);
    }

    public function testCreateFromArrayWithDocBlockFromArray()
    {
        $interfaceGenerator = InterfaceGenerator::fromArray(array(
            'name' => 'SampleInterface',
            'docblock' => array(
                'shortdescription' => 'foo',
            ),
        ));

        $docBlock = $interfaceGenerator->getDocBlock();
        $this->assertInstanceOf('Zend\Code\Generator\DocBlockGenerator', $docBlock);
    }

    public function testCreateFromArrayWithDocBlockInstance()
    {
        $interfaceGenerator = InterfaceGenerator::fromArray(array(
            'name' => 'SampleInterface',
            'docblock' => new DocBlockGenerator('foo'),
        ));

        $docBlock = $interfaceGenerator->getDocBlock();
        $this->assertInstanceOf('Zend\Code\Generator\DocBlockGenerator', $docBlock);
    }

    public function testHasMethodInsensitive()
    {
        $interfaceGenerator = new InterfaceGenerator();
        $interfaceGenerator->addMethod('methodOne');

        $this->assertTrue($interfaceGenerator->hasMethod('methodOne'));
        $this->assertTrue($interfaceGenerator->hasMethod('MethoDonE'));
    }

    public function testRemoveMethodInsensitive()
    {
        $interfaceGenerator = new InterfaceGenerator();
        $interfaceGenerator->addMethod('methodOne');

        $interfaceGenerator->removeMethod('METHODONe');
        $this->assertFalse($interfaceGenerator->hasMethod('methodOne'));
    }

    public function testGenerateInterfaceAndAddMethod()
    {
        $interfaceGenerator = new InterfaceGenerator();
        $interfaceGenerator->setName('MyInterface');
        $interfaceGenerator->addMethod('methodOne');

        $expected = <<<CODE
interface MyInterface
{

    public function methodOne();

}

CODE;

        $output = $interfaceGenerator->generate();
        $this->assertEquals($expected, $output);
    }

    /**
     * @group 6274
     */
    public function testCanAddConstant()
    {
        $interfaceGenerator = new InterfaceGenerator();

        $interfaceGenerator->setName('My\MyInterface');
        $interfaceGenerator->addConstant('X', 'value');

        $this->assertTrue($interfaceGenerator->hasConstant('X'));

        $constant = $interfaceGenerator->getConstant('X');

        $this->assertInstanceOf('Zend\Code\Generator\PropertyGenerator', $constant);
        $this->assertTrue($constant->isConst());
        $this->assertEquals($constant->getDefaultValue()->getValue(), 'value');
    }

    /**
     * @group 6274
     */
    public function testCanAddConstantsWithArrayOfGenerators()
    {
        $interfaceGenerator = new InterfaceGenerator();

        $interfaceGenerator->addConstants(array(
            new PropertyGenerator('X', 'value1', PropertyGenerator::FLAG_CONSTANT),
            new PropertyGenerator('Y', 'value2', PropertyGenerator::FLAG_CONSTANT)
        ));

        $this->assertCount(2, $interfaceGenerator->getConstants());
        $this->assertEquals($interfaceGenerator->getConstant('X')->getDefaultValue()->getValue(), 'value1');
        $this->assertEquals($interfaceGenerator->getConstant('Y')->getDefaultValue()->getValue(), 'value2');
    }

    /**
     * @group 6274
     */
    public function testCanAddConstantsWithArrayOfKeyValues()
    {
        $interfaceGenerator = new InterfaceGenerator();

        $interfaceGenerator->addConstants(array(
            array( 'name'=> 'X', 'value' => 'value1'),
            array('name' => 'Y', 'value' => 'value2')
        ));

        $this->assertCount(2, $interfaceGenerator->getConstants());
        $this->assertEquals($interfaceGenerator->getConstant('X')->getDefaultValue()->getValue(), 'value1');
        $this->assertEquals($interfaceGenerator->getConstant('Y')->getDefaultValue()->getValue(), 'value2');
    }

    /**
     * @group 6274
     */
    public function testAddConstantThrowsExceptionWithInvalidName()
    {
        $this->setExpectedException('InvalidArgumentException');

        $interfaceGenerator = new InterfaceGenerator();

        $interfaceGenerator->addConstant(array(), 'value1');
    }

    /**
     * @group 6274
     */
    public function testAddConstantThrowsExceptionWithInvalidValue()
    {
        $this->setExpectedException('InvalidArgumentException');

        $interfaceGenerator = new InterfaceGenerator();

        $interfaceGenerator->addConstant('X', null);
    }

    /**
     * @group 6274
     */
    public function testAddConstantThrowsExceptionOnDuplicate()
    {
        $this->setExpectedException('InvalidArgumentException');

        $interfaceGenerator = new InterfaceGenerator();

        $interfaceGenerator->addConstant('X', 'value1');
        $interfaceGenerator->addConstant('X', 'value1');
    }

    /**
     * @group 6274
     */
    public function testConstantsAddedFromReflection()
    {
        $reflector = new InterfaceReflection('ZendTest\Code\Generator\TestAsset\InterfaceWithConstants');
        $interfaceGenerator = InterfaceGenerator::fromReflection($reflector);
        $constant  = $interfaceGenerator->getConstant('FOO');
        $this->assertEquals($constant->getDefaultValue()->getValue(), 1);
        $constant  = $interfaceGenerator->getConstant('BAR');
        $this->assertEquals($constant->getDefaultValue()->getValue(), '2');
        $constant  = $interfaceGenerator->getConstant('FOOBAR');
        $this->assertEquals($constant->getDefaultValue()->getValue(), 0x20);
    }

}
