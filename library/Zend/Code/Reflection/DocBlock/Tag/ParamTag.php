<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Code
 */

namespace Zend\Code\Reflection\DocBlock\Tag;

/**
 * @category   Zend
 * @package    Zend_Reflection
 */
class ParamTag implements TagInterface, PhpDocTypedTagInterface
{
    /**
     * @var array
     */
    protected $types = array();

    /**
     * @var string
     */
    protected $variableName = null;

    /**
     * @var string
     */
    protected $description = null;

    /**
     * @return string
     */
    public function getName()
    {
        return 'param';
    }

    /**
     * Initializer
     *
     * @param string $tagDocBlockLine
     */
    public function initialize($tagDocBlockLine)
    {
        $matches = array();
        preg_match('#((?:[\w|\\\]+(?:\[\])*\|?)+)(?:\s+(\$\S+))?(?:\s+(.*))?#s', $tagDocBlockLine, $matches);

        $this->types = explode('|', $matches[1]);

        if (isset($matches[2])) {
            $this->variableName = $matches[2];
        }

        if (isset($matches[3])) {
            $this->description = trim(preg_replace('#\s+#', ' ', $matches[3]));
        }
    }

    /**
     * Get parameter variable type
     *
     * @return string
     * @deprecated 2.0.4 use getTypes instead
     */
    public function getType()
    {
        if (empty($this->types)) {
            return '';
        }

        return $this->types[0];
    }

    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Get parameter name
     *
     * @return string
     */
    public function getVariableName()
    {
        return $this->variableName;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
