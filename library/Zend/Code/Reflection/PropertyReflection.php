<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Code\Reflection;

use ReflectionProperty as PhpReflectionProperty;
use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Scanner\AnnotationScanner;
use Zend\Code\Scanner\CachingFileScanner;
use Zend\Code\Annotation\AnnotationCollection;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * @todo       implement line numbers
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PropertyReflection extends PhpReflectionProperty implements ReflectionInterface
{
    protected $annotations;

    /**
     * Get declaring class reflection object
     *
     * @return ClassReflection
     */
    public function getDeclaringClass()
    {
        $phpReflection  = parent::getDeclaringClass();
        $zendReflection = new ClassReflection($phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }

    /**
     * Get DocBlock comment
     *
     * @return string|false False if no DocBlock defined
     */
    public function getDocComment()
    {
        return parent::getDocComment();
    }

    /**
     * @return false|DocBlockReflection
     */
    public function getDocBlock()
    {
        if (!($docComment = $this->getDocComment())) {
            return false;
        }
        $r = new DocBlockReflection($docComment);
        return $r;
    }

    /**
     * @param AnnotationManager $annotationManager
     * @return AnnotationCollection
     */
    public function getAnnotations(AnnotationManager $annotationManager)
    {
        if (null !== $this->annotations) {
            return $this->annotations;
        }

        $this->annotations = new AnnotationCollection();
        $reader            = new AnnotationReader();
        $annotations       = $reader->getPropertyAnnotations($this);

        foreach ($annotations as $annotation) {
            if ($annotationManager->hasAnnotation(get_class($annotation))) {
                $this->annotations[] = $annotation;
            }
        }

        return $this->annotations;
    }

    public function toString()
    {
        return $this->__toString();
    }
}
