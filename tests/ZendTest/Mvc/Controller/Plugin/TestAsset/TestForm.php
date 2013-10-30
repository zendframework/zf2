<?php

namespace ZendTest\Mvc\Controller\Plugin\TestAsset;

use Zend\Form\Form;

class TestForm extends Form
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->add(array(
            'name' => 'collection',
            'type' => 'collection',
            'options' => array(
                'target_element' => new TestFieldset('target'),
                'count' => 2,
            )
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
        ));
    }

    // Helper for setPost in Test
    public function getPost1()
    {
        return array(
            'collection' => array(
                0 => array(
                    'text' => 'testvalue1',
                ),
            )
        );
    }

    // Helper for setFiles in test
    public function getFiles1()
    {
        return array(
            'collection' => array(
                0 => array(
                    'file' => array(
                        'name' => 'test.jpg',
                        'type' => 'image/jpeg',
                        'size' => 20480,
                        'tmp_name' => __DIR__ . '/TestAsset/nullfile_copy1',
                        'error' => 0
                    ),
                ),
            )
        );
    }

    // Which messages are expected
    public function getErrorMessages1()
    {
        return array(
            'collection' => array(
                0 => array(
                ),
                1 => array(
                    'text' => array(
                        'isEmpty' => 'Value is required and can\'t be empty'
                    ),
                    'file' => array(
                        'isEmpty' => 'Value is required and can\'t be empty',
                    ),
                ),
            ),
        );
    }

    // Which result is expected
    public function getExpectedResult1()
    {
        return array(
            'collection' => array(
                0 => array(
                    'file' => array(
                        'name' => 'test.jpg',
                        'type' => 'image/jpeg',
                        'size' => 20480,
                        'tmp_name' => __DIR__ . '/TestAsset/nullfile_copy1',
                        'error' => 0
                    ),
                ),
            )
        );
    }
}
