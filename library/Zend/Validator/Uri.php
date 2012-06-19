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
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Validator;

use Traversable,
    Zend\Uri\Uri as UriHandler,
    Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Uri extends AbstractValidator
{
    const INVALID = 'uriInvalid';
    const NOT_URI = 'notUri';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID => "Invalid type given. String expected",
        self::NOT_URI => "'%value%' does not appear to be a valid Uri",
    );

    /**
     * @var \Zend\Uri\Uri
     */
    protected $uriHandler;

    /**
     * @var boolean
     */
    protected $allowRelative = true;

    /**
     * @var boolean
     */
    protected $allowAbsolute = true;


    /**
     * Sets default option values for this instance
     *
     * @param array|\Traversable $options
     */
    public function __construct($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            $options = func_get_args();
            $temp['uriHandler'] = array_shift($options);
            if (!empty($options)) {
                $temp['allowRelative'] = array_shift($options);
            }
            if (!empty($options)) {
                $temp['allowAbsolute'] = array_shift($options);
            }

            $options = $temp;
        }

        if (isset($options['uriHandler'])) {
            $this->setUriHandler($options['uriHandler']);
        }
        if (isset($options['allowRelative'])) {
            $this->setAllowRelative($options['allowRelative']);
        }
        if (isset($options['allowAbsolute'])) {
            $this->setAllowAbsolute($options['allowAbsolute']);
        }

        parent::__construct($options);
    }

    /**
     * @return \Zend\Uri\Uri
     */
    public function getUriHandler()
    {
        if (null === $this->uriHandler) {
            // Lazy load the base Uri handler
            $this->uriHandler = new UriHandler();
        }
        return $this->uriHandler;
    }

    /**
     * @param \Zend\Uri\Uri $uriHandler
     * @return \Zend\Validator\Uri
     */
    public function setUriHandler($uriHandler)
    {
        $this->uriHandler = $uriHandler;
        return $this;
    }

    /**
     * Returns the allowAbsolute option
     *
     * @return boolean
     */
    public function getAllowAbsolute()
    {
        return $this->allowAbsolute;
    }

    /**
     * Sets the allowAbsolute option
     *
     * @param boolean $allowWhiteSpace
     * @return \Zend\Validator\Uri
     */
    public function setAllowAbsolute($allowAbsolute)
    {
        $this->allowAbsolute = (boolean) $allowAbsolute;
        return $this;
    }

    /**
     * Returns the allowRelative option
     *
     * @return boolean
     */
    public function getAllowRelative()
    {
        return $this->allowRelative;
    }

    /**
     * Sets the allowRelative option
     *
     * @param boolean $allowRelative
     * @return \Zend\Validator\Uri
     */
    public function setAllowRelative($allowRelative)
    {
        $this->allowRelative = (boolean) $allowRelative;
        return $this;
    }

    /**
     * Returns true if and only if $value validates as a Uri
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $uriHandler = $this->getUriHandler();
        try {
            $uriHandler->parse($value);
            if ($uriHandler->isValid()) {
                if ($this->allowRelative && $this->allowAbsolute) {
                    return true;
                }

                // settings must match result
                if (($this->allowAbsolute && $uriHandler->isAbsolute())
                    || ($this->allowRelative && $uriHandler->isValidRelative())
                ) {
                    return true;
                }
            }
        } catch (\Zend\Uri\Exception\ExceptionInterface $ex) {
            // Error parsing URI, it must be invalid
        }

        $this->error(self::NOT_URI);
        return false;
    }
}
