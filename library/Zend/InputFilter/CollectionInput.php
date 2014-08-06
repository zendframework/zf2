<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

/**
 * Overrides message handling
 */
class CollectionInput extends ArrayInput
{
    /**
     * Messages
     * @var string[]
     */
    protected $messages = array();

    /**
     * @param mixed $context Extra "context" to provide the validator
     * @return bool
     */
    public function isValid($context = null)
    {
        $this->injectNotEmptyValidator();
        $validator = $this->getValidatorChain();
        $values    = $this->getValue();
        $result    = true;
        foreach ($values as $key => $value) {
            $result = $validator->isValid($value, $context);
            if (!$result) {
                // Store the messages in a custom format
                $this->messages[$key] = $validator->getMessages();
                if ($this->hasFallback()) {
                    $this->setValue($this->getFallbackValue());
                    $result = true;
                }
                break;
            }
        }
        
        return $result;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        if (null !== $this->errorMessage) {
            return (array) $this->errorMessage;
        }

        if ($this->hasFallback()) {
            return array();
        }
        return $this->messages;
    }
}