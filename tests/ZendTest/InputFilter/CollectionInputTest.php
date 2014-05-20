<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\InputFilter;

use Zend\InputFilter\CollectionInput;
use Zend\Filter;
use Zend\Validator;

class CollectionInputTest extends ArrayInputTest
{
    public function setUp()
    {
        $this->input = new CollectionInput('foo');
    }

    public function testGetMessagesReturnsValidationMessages()
    {
        $this->markTestSkipped('Message behaviour changed; skipping this test');
    }

    public function testNotEmptyValidatorAddedWhenIsValidIsCalled()
    {
        $this->markTestSkipped('Message behaviour changed; skipping this test');
    }

    public function testMessagesOverride()
    {
        $data = array(
            'abc',
        );

        $this->input->getValidatorChain()->attach(new Validator\Date());
        $this->input->setValue($data);
        $this->input->isValid();
        $messages = $this->input->getMessages();

        $this->assertCount(1, $messages);
        foreach ($messages as $msgArray) {
            $this->assertTrue(is_array($msgArray));
        }
    }

}