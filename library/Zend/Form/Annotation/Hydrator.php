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
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form\Annotation;

use Zend\Form\Exception;

/**
 * Hydrator annotation
 *
 * Use this annotation to specify a specific hydrator class to use with the form.
 * The value should be a bare string or a JSON-encoded string indicating the
 * fully qualified class name of the hydrator to use.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @Annotation
 */
class Hydrator
{
    /**
     * @var string
     */
    protected $hydrator;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        if (!isset($data['value']) && !is_string($data['value'])) {
            throw new Exception\DomainException(sprintf(
                '%s expects the annotation to define a string; received "%s"',
                __METHOD__,
                gettype($data['value'])
            ));
        }

        $this->hydrator = $data['value'];
    }

    /**
     * Retrieve the hydrator class
     *
     * @return null|string
     */
    public function getHydrator()
    {
        return $this->hydrator;
    }
}
