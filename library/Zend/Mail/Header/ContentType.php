<?php

namespace Zend\Mail\Header;

class ContentType implements HeaderDescription
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * Factory: create Content-Type header object from string
     * 
     * @param  string $headerLine 
     * @return ContentType
     */
    public static function fromString($headerLine)
    {
        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'content-type') {
            throw new Exception\InvalidArgumentException('Invalid header line for Content-Type string');
        }

        $value  = str_replace("\r\n ", " ", $value)
        $values = preg_split('#\s*;\s*#', $value);
        $type   = array_shift($values);

        $header = new static();
        $header->setType($type);

        if (count($values)) {
            foreach ($values as $keyValuePair) {
                list($key, $value) = preg_split('/=/', $keyValuePair);
                $value = trim($value, "\"\' \t\n\r\0\x0B");
                $header->addParameter($key, $value);
            }
        }
        
        return $header;
    }

    /**
     * Get header name
     * 
     * @return string
     */
    public function getFieldName()
    {
        return 'Content-Type';
    }

    /**
     * Get header value
     * 
     * @return string
     */
    public function getFieldValue()
    {
        $prepared = $this->type;
        if (empty($this->parameters)) {
            return $prepared;
        }

        $values = array($prepared);
        foreach ($this->parameters as $attribute => $value) {
            $values[] = sprintf('%s="%s"', $attribute, $value);
        }
        $value = implode(";\r\n ", $values);
        return $value;
    }

    /**
     * Serialize header to string
     * 
     * @return string
     */
    public function toString()
    {
        return 'Content-Type: ' . $this->getFieldValue() . "\r\n";
    }

    /**
     * Set the content type
     * 
     * @param  string $type 
     * @return ContentType
     */
    public function setType($type)
    {
        if (!preg_match('/^[a-z_-]+\/[a-z_-]+$/i', $type)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a value in the format "type/subtype"; received "%s"',
                __METHOD__,
                (string) $type
            ));
        }
        $this->type = $type;
        return $this;
    }

    /**
     * Retrieve the content type
     * 
     * @return void
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add a parameter pair
     * 
     * @param  string $name 
     * @param  string $value 
     * @return ContentType
     */
    public function addParameter($name, $value)
    {
        $name = strtolower($name);
        $this->parameters[$name] = (string) $value;
        return $this;
    }

    /**
     * Get all parameters
     * 
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get a parameter by name
     * 
     * @param  string $name 
     * @return null|string
     */
    public function getParameter($name)
    {
        $name = strtolower($name);
        if (isset($this->parameters[$name])) {
            return $this->parameters[$name];
        }
        return null;
    }

    /**
     * Remove a named parameter
     * 
     * @param  string $name 
     * @return bool
     */
    public function removeParameter($name)
    {
        $name = strtolower($name);
        if (isset($this->parameters[$name])) {
            unset($this->parameters[$name]);
            return true;
        }
        return false;
    }
}
