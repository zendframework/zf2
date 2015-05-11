<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\View\Helper;

use ArrayObject;
use Iterator;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\View\Helper\PartialLoop;
use Zend\View\Renderer\PhpRenderer as View;

/**
 * Test class for Zend\View\Helper\PartialLoop.
 *
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class PartialLoopTest extends TestCase
{
    /**
     * @var PartialLoop
     */
    public $helper;

    /**
     * @var string
     */
    public $basePath;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->basePath = __DIR__ . '/_files/modules';
        $this->helper   = new PartialLoop();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper);
    }

    /**
     * @return void
     */
    public function testPartialLoopIteratesOverArray()
    {
        $data = [
            ['message' => 'foo'],
            ['message' => 'bar'],
            ['message' => 'baz'],
            ['message' => 'bat'],
        ];

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        $result = $this->helper->__invoke('partialLoop.phtml', $data);
        foreach ($data as $item) {
            $string = 'This is an iteration: ' . $item['message'];
            $this->assertContains($string, $result);
        }
    }

    /**
     * @return void
     */
    public function testPartialLoopIteratesOverIterator()
    {
        $data = [
            ['message' => 'foo'],
            ['message' => 'bar'],
            ['message' => 'baz'],
            ['message' => 'bat']
        ];
        $o = new IteratorTest($data);

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        $result = $this->helper->__invoke('partialLoop.phtml', $o);
        foreach ($data as $item) {
            $string = 'This is an iteration: ' . $item['message'];
            $this->assertContains($string, $result);
        }
    }

    /**
     * @return void
     */
    public function testPartialLoopIteratesOverRecursiveIterator()
    {
        $rIterator = new RecursiveIteratorTest();
        for ($i = 0; $i < 5; ++$i) {
            $data = [
                'message' => 'foo' . $i,
            ];
            $rIterator->addItem(new IteratorTest($data));
        }

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        $result = $this->helper->__invoke('partialLoop.phtml', $rIterator);
        foreach ($rIterator as $item) {
            foreach ($item as $key => $value) {
                $this->assertContains($value, $result, var_export($value, 1));
            }
        }
    }

    /**
     * @return void
     */
    public function testPartialLoopThrowsExceptionWithBadIterator()
    {
        $data = [
            ['message' => 'foo'],
            ['message' => 'bar'],
            ['message' => 'baz'],
            ['message' => 'bat']
        ];
        $o = new BogusIteratorTest($data);

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        try {
            $result = $this->helper->__invoke('partialLoop.phtml', $o);
            $this->fail('PartialLoop should only work with arrays and iterators');
        } catch (\Exception $e) {
        }
    }

    /**
     * @return void
     */
    public function testPassingNullDataThrowsExcpetion()
    {
        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        $this->setExpectedException('Zend\View\Exception\InvalidArgumentException');
        $result = $this->helper->__invoke('partialLoop.phtml', null);
    }

    public function testPassingNoArgsReturnsHelperInstance()
    {
        $test = $this->helper->__invoke();
        $this->assertSame($this->helper, $test);
    }

    public function testShouldAllowIteratingOverTraversableObjects()
    {
        $data = [
            ['message' => 'foo'],
            ['message' => 'bar'],
            ['message' => 'baz'],
            ['message' => 'bat']
        ];
        $o = new ArrayObject($data);

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        $result = $this->helper->__invoke('partialLoop.phtml', $o);
        foreach ($data as $item) {
            $string = 'This is an iteration: ' . $item['message'];
            $this->assertContains($string, $result);
        }
    }

    public function testShouldAllowIteratingOverObjectsImplementingToArray()
    {
        $data = [
            ['message' => 'foo'],
            ['message' => 'bar'],
            ['message' => 'baz'],
            ['message' => 'bat']
        ];
        $o = new ToArrayTest($data);

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        $result = $this->helper->__invoke('partialLoop.phtml', $o);
        foreach ($data as $item) {
            $string = 'This is an iteration: ' . $item['message'];
            $this->assertContains($string, $result, $result);
        }
    }

    /**
     * @group ZF-3350
     * @group ZF-3352
     */
    public function testShouldNotCastToArrayIfObjectIsTraversable()
    {
        $data = [
            new IteratorWithToArrayTestContainer(['message' => 'foo']),
            new IteratorWithToArrayTestContainer(['message' => 'bar']),
            new IteratorWithToArrayTestContainer(['message' => 'baz']),
            new IteratorWithToArrayTestContainer(['message' => 'bat']),
        ];
        $o = new IteratorWithToArrayTest($data);

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);
        $this->helper->setObjectKey('obj');

        $result = $this->helper->__invoke('partialLoopObject.phtml', $o);
        foreach ($data as $item) {
            $string = 'This is an iteration: ' . $item->message;
            $this->assertContains($string, $result, $result);
        }
    }

    /**
     * @group ZF-3083
     */
    public function testEmptyArrayPassedToPartialLoopShouldNotThrowException()
    {
        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        $this->helper->__invoke('partialLoop.phtml', []);
    }

    /**
     * @group ZF-2737
     */
    public function testPartialLoopIncrementsPartialCounter()
    {
        $data = [
            ['message' => 'foo'],
            ['message' => 'bar'],
            ['message' => 'baz'],
            ['message' => 'bat']
        ];

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        $this->helper->__invoke('partialLoopCouter.phtml', $data);
        $this->assertEquals(4, $this->helper->getPartialCounter());
    }

    /**
     * @group ZF-5174
     */
    public function testPartialLoopPartialCounterResets()
    {
        $data = [
            ['message' => 'foo'],
            ['message' => 'bar'],
            ['message' => 'baz'],
            ['message' => 'bat']
        ];

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        $this->helper->__invoke('partialLoopCouter.phtml', $data);
        $this->assertEquals(4, $this->helper->getPartialCounter());

        $this->helper->__invoke('partialLoopCouter.phtml', $data);
        $this->assertEquals(4, $this->helper->getPartialCounter());
    }

    public function testShouldNotConvertToArrayRecursivelyIfModelIsTraversable()
    {
        $rIterator = new RecursiveIteratorTest();
        for ($i = 0; $i < 5; ++$i) {
            $data = [
                'message' => 'foo' . $i,
            ];
            $rIterator->addItem(new IteratorTest($data));
        }

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);
        $this->helper->setObjectKey('obj');

        $result = $this->helper->__invoke('partialLoopShouldNotConvertToArrayRecursively.phtml', $rIterator);

        foreach ($rIterator as $item) {
            foreach ($item as $key => $value) {
                $this->assertContains('This is an iteration: ' . $value, $result, var_export($value, 1));
            }
        }
    }

    /**
     * @group 7093
     */
    public function testNestedCallsShouldNotOverrideObjectKey()
    {
        $data = [];
        for ($i = 0; $i < 3; $i++) {
            $obj = new \stdClass();
            $obj->helper = $this->helper;
            $obj->objectKey = "foo" . $i;
            $obj->message = "bar";
            $obj->data = [
                $obj
            ];
            $data[] = $obj;
        }

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        $this->helper->setObjectKey('obj');
        $result = $this->helper->__invoke('partialLoopParentObject.phtml', $data);

        foreach ($data as $item) {
            $string = 'This is an iteration with objectKey: ' . $item->objectKey;
            $this->assertContains($string, $result, $result);
        }
    }

    /**
     * @group 7450
     */
    public function testNestedPartialLoopsNestedArray()
    {
        $data = [[
            'obj' => [
                'helper' => $this->helper,
                'message' => 'foo1',
                'data' => [[
                    'message' => 'foo2'
                ]]
            ]
        ]];

        $view = new View();
        $view->resolver()->addPath($this->basePath . '/application/views/scripts');
        $this->helper->setView($view);

        $result = $this->helper->__invoke('partialLoopParentObject.phtml', $data);
        $this->assertContains('foo1', $result, $result);
        $this->assertContains('foo2', $result, $result);
    }

    public function testPartialLoopWithInvalidValuesWillRaiseException()
    {
        $this->setExpectedException(
            'Zend\View\Exception\InvalidArgumentException',
            'PartialLoop helper requires iterable data, string given'
        );

        $this->helper->__invoke('partialLoopParentObject.phtml', 'foo');
    }

    public function testPartialLoopWithInvalidObjectValuesWillRaiseException()
    {
        $this->setExpectedException(
            'Zend\View\Exception\InvalidArgumentException',
            'PartialLoop helper requires iterable data, stdClass given'
        );

        $this->helper->__invoke('partialLoopParentObject.phtml', new \stdClass());
    }
}

class IteratorTest implements Iterator
{
    public $items;

    public function __construct(array $array)
    {
        $this->items = $array;
    }

    public function current()
    {
        return current($this->items);
    }

    public function key()
    {
        return key($this->items);
    }

    public function next()
    {
        return next($this->items);
    }

    public function rewind()
    {
        return reset($this->items);
    }

    public function valid()
    {
        return (current($this->items) !== false);
    }

    public function toArray()
    {
        return $this->items;
    }
}

class RecursiveIteratorTest implements Iterator
{
    public $items;

    public function __construct()
    {
        $this->items = [];
    }

    public function addItem(Iterator $iterator)
    {
        $this->items[] = $iterator;
        return $this;
    }

    public function current()
    {
        return current($this->items);
    }

    public function key()
    {
        return key($this->items);
    }

    public function next()
    {
        return next($this->items);
    }

    public function rewind()
    {
        return reset($this->items);
    }

    public function valid()
    {
        return (current($this->items) !== false);
    }
}

class BogusIteratorTest
{
}

class ToArrayTest
{
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function toArray()
    {
        return $this->data;
    }
}

class IteratorWithToArrayTest implements Iterator
{
    public $items;

    public function __construct(array $array)
    {
        $this->items = $array;
    }

    public function toArray()
    {
        return $this->items;
    }

    public function current()
    {
        return current($this->items);
    }

    public function key()
    {
        return key($this->items);
    }

    public function next()
    {
        return next($this->items);
    }

    public function rewind()
    {
        return reset($this->items);
    }

    public function valid()
    {
        return (current($this->items) !== false);
    }
}

class IteratorWithToArrayTestContainer
{
    protected $_info;

    public function __construct(array $info)
    {
        foreach ($info as $key => $value) {
            $this->$key = $value;
        }
        $this->_info = $info;
    }

    public function toArray()
    {
        return $this->_info;
    }
}
