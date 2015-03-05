<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Scanner;

use Zend\Code\Scanner\AnnotationScanner;
use Zend\Code\NameInformation;
use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Annotation\Parser\GenericAnnotationParser;

class AnnotationScannerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideDocComment
     *
     * @param string $docComment
     */
    public function testScannerWorks($docComment)
    {
        $annotationManager = new AnnotationManager();
        $parser = new GenericAnnotationParser();
        $parser->registerAnnotations([
            $foo = new TestAsset\Annotation\Foo(),
            $bar = new TestAsset\Annotation\Bar()
        ]);
        $annotationManager->attach($parser);

        $nameInfo = new NameInformation();
        $nameInfo->addUse('ZendTest\Code\Scanner\TestAsset\Annotation', 'Test');

        $annotationScanner = new AnnotationScanner($annotationManager, $docComment, $nameInfo);
        $this->assertEquals(get_class($foo), get_class($annotationScanner[0]));
        $this->assertEquals("'anything I want()\n to be'", $annotationScanner[0]->getContent());
        $this->assertEquals(get_class($bar), get_class($annotationScanner[1]));
    }

    /**
     * @return string[][]
     */
    public function provideDocComment()
    {
        return array(
            array(
                'usingTab' => '/**' . "\n"
                . ' * @Test\Foo(\'anything I want()' . "\n" . ' * to be\')' . "\n"
                . "\t" . ' * @Test\Bar' . "\n */"
            ),
            array(
                'usingSpace' => '/**' . "\n"
                . ' * @Test\Foo(\'anything I want()' . "\n" . ' * to be\')' . "\n"
                . ' * @Test\Bar' . "\n */"
            ),
            array(
                'mixedTabAndSpace' => '/**' . "\n"
                    . ' * @Test\Foo(\'anything I want()' . "\n" . ' * to be\')' . "\n"
                    . ' * @Test\Bar' . "\n"
                    . "\t" . ' * @Test\Bar' . "\n */"
            ),
        );
    }
}
