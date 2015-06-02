<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @author    Daan Biesterbos <daanbiesterbos@gmail.com>
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Generator;

use Zend\Code\Generator\MethodDeclarationGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\ValueGenerator;
use Zend\Code\Reflection\MethodDeclarationReflection;

/**
 * @group Zend_Code_Generator
 * @group Zend_Code_Generator_Php
 */
class MethodDeclarationGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testMethodConstructor()
    {
        $methodGenerator = new MethodDeclarationGenerator();
        $this->isInstanceOf($methodGenerator, '\Zend\Code\Generator\PhpMethod');
    }

    public function testMethodParameterAccessors()
    {
        $methodGenerator = new MethodDeclarationGenerator();
        $methodGenerator->setParameters(array('one'));
        $params = $methodGenerator->getParameters();
        $param = array_shift($params);
        $this->assertTrue($param instanceof \Zend\Code\Generator\ParameterGenerator,
            'Failed because $param was not instance of Zend\Code\Generator\ParameterGenerator');
    }


    public function testDocBlock()
    {
        $docblockGenerator = new \Zend\Code\Generator\DocBlockGenerator();

        $method = new MethodDeclarationGenerator();
        $method->setDocBlock($docblockGenerator);
        $this->assertTrue($docblockGenerator === $method->getDocBlock());
    }


    public function testMethodFromReflection()
    {
        $refl = new MethodDeclarationReflection('ZendTest\Code\Generator\TestAsset\InterfaceWithConstants', 'someMethod');

        $methodGenerator = MethodDeclarationGenerator::fromReflection($refl);
        $target = <<<EOS
    /**
     * Enter description here...
     *
     * @return bool
     */
    public function someMethod();
EOS;
        $this->assertEquals($target, (string) $methodGenerator);
    }

    /**
     * @group ZF-6444
     */
    public function testMethodWithStaticModifierIsEmitted()
    {
        $methodGenerator = new MethodDeclarationGenerator();
        $methodGenerator->setName('foo');
        $methodGenerator->setParameters(array('one'));
        $methodGenerator->setStatic(true);

        $expected = <<<EOS
    public static function foo(\$one);
EOS;

        $this->assertEquals($expected, $methodGenerator->generate());
    }

    /**
     * @group ZF-7205
     */
    public function testMethodCanHaveDocBlock()
    {
        $methodGeneratorProperty = new MethodDeclarationGenerator(
            'someFoo',
            array(),
            MethodDeclarationGenerator::FLAG_STATIC | MethodDeclarationGenerator::FLAG_PUBLIC,
            null,
            '@var string $someVal This is some val'
        );

        $expected = <<<EOS
    /**
     * @var string \$someVal This is some val
     */
    public static function someFoo();
EOS;
        $this->assertEquals($expected, $methodGeneratorProperty->generate());
    }

    /**
     * @group ZF-7268
     */
    public function testDefaultValueGenerationDoesNotIncludeTrailingSemicolon()
    {
        $method = new MethodDeclarationGenerator('setOptions');
        $default = new ValueGenerator();
        $default->setValue(array());

        $param   = new ParameterGenerator('options', 'array');
        $param->setDefaultValue($default);

        $method->setParameter($param);
        $generated = $method->generate();
        $this->assertRegexp('/array \$options = array\(\)\)/', $generated, $generated);
    }

    public function testCreateFromArray()
    {
        $methodGenerator = MethodDeclarationGenerator::fromArray(array(
            'name'       => 'SampleMethod',
            'body'       => 'foo',
            'docblock'   => array(
                'shortdescription' => 'foo',
            ),
            'static'     => true,
            'visibility' => MethodDeclarationGenerator::VISIBILITY_PUBLIC,
        ));

        $this->assertEquals('SampleMethod', $methodGenerator->getName());
        $this->assertInstanceOf('Zend\Code\Generator\DocBlockGenerator', $methodGenerator->getDocBlock());
        $this->assertTrue($methodGenerator->isStatic());
        $this->assertEquals(MethodDeclarationGenerator::VISIBILITY_PUBLIC, $methodGenerator->getVisibility());
    }
}
