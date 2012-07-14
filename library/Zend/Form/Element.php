<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace Zend\Form;

use Traversable;
use Zend\I18n\Translator\Translator;
use Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Form
 */
class Element implements ElementInterface
{
    /**
     * @var array
     */
    protected $attributes = array();

    /**
     * @var string
     */
    protected $label;

    /**
     * @var array
     */
    protected $labelAttributes;

    /**
     * @var array Validation error messages
     */
    protected $messages = array();

    /**
     * Default translation object for all element labels
     * and default validators.
     * @var Translator
     */
    protected static $defaultTranslator;

    /**
     * Translation object to use for label/validator translation
     * @var Translator
     */
    protected $translator;

    /**
     * Translation text domain to use for translations
     * @var string
     */
    protected $translatorTextDomain = 'default';

    /**
     * Is translation disabled?
     * @var boolean
     */
    protected $translatorDisabled = false;


    /**
     * @param  null|int|string  $name    Optional name for the element
     * @param  array            $options Optional options for the element
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($name = null, $options = array())
    {
        if (null !== $name) {
            $this->setName($name);
        }

        if (!empty($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Set value for name
     *
     * @param  string $name
     * @return Element|ElementInterface
     */
    public function setName($name)
    {
        $this->setAttribute('name', $name);
        return $this;
    }

    /**
     * Get value for name
     *
     * @return string|int
     */
    public function getName()
    {
        return $this->getAttribute('name');
    }

    /**
     * Set options for an element. Accepted options are:
     * - label: label to associate with the element
     * - label_attributes: attributes to use when the label is rendered
     *
     * @param  array|\Traversable $options
     * @return Element|ElementInterface
     * @throws Exception\InvalidArgumentException
     */
    public function setOptions($options)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException(
                'The options parameter must be an array or a Traversable'
            );
        }

        if (isset($options['label'])) {
            $this->setLabel($options['label']);
        }

        if (isset($options['label_attributes'])) {
            $this->setLabelAttributes($options['label_attributes']);
        }

        if (isset($options['translator'])) {
            $this->setTranslator($options['translator']);
        }
        if (isset($options['translator_text_domain'])) {
            $this->setTranslatorTextDomain($options['translator_text_domain']);
        }
        if (isset($options['translator_disabled'])) {
            $this->setTranslatorDisabled($options['translator_disabled']);
        }

        return $this;
    }

    /**
     * Set a single element attribute
     *
     * @param  string $key
     * @param  mixed  $value
     * @return Element|ElementInterface
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * Retrieve a single element attribute
     *
     * @param  $key
     * @return mixed|null
     */
    public function getAttribute($key)
    {
        if (!array_key_exists($key, $this->attributes)) {
            return null;
        }
        return $this->attributes[$key];
    }

    /**
     * Does the element has a specific attribute ?
     *
     * @param  string $key
     * @return bool
     */
    public function hasAttribute($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Set many attributes at once
     *
     * Implementation will decide if this will overwrite or merge.
     *
     * @param  array|Traversable $arrayOrTraversable
     * @return Element|ElementInterface
     * @throws Exception\InvalidArgumentException
     */
    public function setAttributes($arrayOrTraversable)
    {
        if (!is_array($arrayOrTraversable) && !$arrayOrTraversable instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable argument; received "%s"',
                __METHOD__,
                (is_object($arrayOrTraversable) ? get_class($arrayOrTraversable) : gettype($arrayOrTraversable))
            ));
        }
        foreach ($arrayOrTraversable as $key => $value) {
            $this->setAttribute($key, $value);
        }
        return $this;
    }

    /**
     * Retrieve all attributes at once
     *
     * @return array|Traversable
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Clear all attributes
     *
     * @return Element|ElementInterface
     */
    public function clearAttributes()
    {
        $this->attributes = array();
        return $this;
    }

    /**
     * Set the label used for this element
     *
     * @param $label
     * @return Element|ElementInterface
     */
    public function setLabel($label)
    {
        if (is_string($label)) {
            $this->label = $label;
        }

        return $this;
    }

    /**
     * Retrieve the label used for this element
     *
     * @return string
     */
    public function getLabel()
    {
        $translator = $this->getTranslator();
        if (!$translator) {
            return $this->label;
        }

        return $translator->translate(
            $this->label, $this->getTranslatorTextDomain()
        );
    }

    /**
     * Set the attributes to use with the label
     *
     * @param array $labelAttributes
     * @return Element|ElementInterface
     */
    public function setLabelAttributes(array $labelAttributes)
    {
        $this->labelAttributes = $labelAttributes;
        return $this;
    }

    /**
     * Get the attributes to use with the label
     *
     * @return array
     */
    public function getLabelAttributes()
    {
        return $this->labelAttributes;
    }

    /**
     * Set a list of messages to report when validation fails
     *
     * @param  array|Traversable $messages
     * @return Element|ElementInterface
     * @throws Exception\InvalidArgumentException
     */
    public function setMessages($messages)
    {
        if (!is_array($messages) && !$messages instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable object of validation error messages; received "%s"',
                __METHOD__,
                (is_object($messages) ? get_class($messages) : gettype($messages))
            ));
        }

        $this->messages = $messages;
        return $this;
    }

    /**
     * Get validation error messages, if any.
     *
     * Returns a list of validation failure messages, if any.
     *
     * @return array|Traversable
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set translation object
     *
     * @param  Translator|null $translator
     * @return Element
     */
    public function setTranslator(Translator $translator = null)
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * Return translation object
     *
     * @return Translator|null
     */
    public function getTranslator()
    {
        if ($this->isTranslatorDisabled()) {
            return null;
        }

        if (null === $this->translator) {
            return self::getDefaultTranslator();
        }

        return $this->translator;
    }

    /**
     * Does this validator have its own specific translator?
     *
     * @return bool
     */
    public function hasTranslator()
    {
        return (bool)$this->translator;
    }

    /**
     * Set translation text domain
     *
     * @param  string $textDomain
     * @return Element
     */
    public function setTranslatorTextDomain($textDomain = 'default')
    {
        $this->translatorTextDomain = $textDomain;
        return $this;
    }

    /**
     * Return the translation text domain
     *
     * @return string
     */
    public function getTranslatorTextDomain()
    {
        return $this->translatorTextDomain;
    }

    /**
     * Set default translation object for all validate objects
     *
     * @param  Translator|null $translator
     * @return void
     */
    public static function setDefaultTranslator(Translator $translator = null)
    {
        self::$defaultTranslator = $translator;
    }

    /**
     * Get default translation object for all validate objects
     *
     * @return Translator|null
     */
    public static function getDefaultTranslator()
    {
        return self::$defaultTranslator;
    }

    /**
     * Is there a default translation object set?
     *
     * @return boolean
     */
    public static function hasDefaultTranslator()
    {
        return (bool) self::$defaultTranslator;
    }

    /**
     * Indicate whether or not translation should be disabled
     *
     * @param  bool $flag
     * @return Element
     */
    public function setTranslatorDisabled($flag)
    {
        $this->translatorDisabled = (bool) $flag;
        return $this;
    }

    /**
     * Is translation disabled?
     *
     * @return bool
     */
    public function isTranslatorDisabled()
    {
        return $this->translatorDisabled;
    }
}
