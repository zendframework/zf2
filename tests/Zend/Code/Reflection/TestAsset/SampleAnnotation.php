<?php

namespace ZendTest\Code\Reflection\TestAsset;

use Zend\Code\Annotation\AnnotationInterface;

/** @Annotation */
class SampleAnnotation implements AnnotationInterface
{
    public $content;

    public function __construct($data)
    {
        $this->content = __CLASS__ . ': ' . $data['content'];
    }

    public function initialize($content)
    {
        // @todo remove
    }
}
