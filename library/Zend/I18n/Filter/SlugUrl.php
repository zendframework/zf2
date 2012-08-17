<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace Zend\I18n\Filter;

use Traversable;

/**
 * @category   Zend
 * @package    Zend_Filter
 */
class SlugUrl extends AbstractLocale 
{
    /**
     * Corresponds to the third
     *
     * @var string
     */
    protected $encoding;

    /**
     * Replace white space for some chacacter
     * 
     * @var string
     */
    protected $replaceWhiteSpace;

    /**
     * Allow only Alpha characters and numbers
     * 
     * @var boolean
     */
    protected $onlyAlnum;

    /**
     * Sets the characters that are relevant and keeps the text 
     *
     * @var string|array
     */
    protected $relevantChars;

   /**
     * Sets the characters that are relevant and should be 
     * replaced by the value set in "replaceWhiteSpace"
     *
     * @var string|array
     */
    protected $irrelevantChars;

    /**
     * Constant that holds an empty space
     *
     * string WHITE_SPACE
     */
    const WHITE_SPACE = ' ';

    /**
     * Sets filter options
     *
     * @param  string|array $charset
     * @param  string|array $replaceWhiteSpace
     * @param  boolean|array $onlyAlnum
     * @param  string|array $relevantChars
     * @param  string|array $irrelevantChars
     *
     * @return void
     */
    public function __construct($options = array())
    {

        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (!is_array($options)) {
            $options = func_get_args();
            $temp = array();
            if (isset($options[0])) {
                $temp['charset'] = $options[0];
            }
            
            if (isset($options[1])) {
                $temp['replaceWhiteSpace'] = $options[1];
            }
            
            if (isset($options[2])) {
                $temp['onlyAlnum'] = $options[2];
            }

            if (isset($options[3])) {
                $temp['relevantChars'] = $options[3];
            }

            if (isset($options[4])) {
                $temp['irrelevantChars'] = $options[4];
            }
            $options = $temp;
        }
        
        if (!isset($options['encoding'])) {
            $options['encoding'] = 'utf8';
        }
 
        if (isset($options['charset'])) {
            $options['encoding'] = $options['charset'];
        }

        if (!isset($options['replaceWhiteSpace'])) {
            $options['replaceWhiteSpace'] = ' ';
        }

        if (!isset($options['onlyAlnum'])) {
            $options['onlyAlnum'] = false;
        }

        if (!isset($options['relevantChars'])) {
            $options['relevantChars'] = '\+';
        }

        if (!isset($options['irrelevantChars'])) {
            $options['irrelevantChars'] = '\/'; 
        }
        $this->setEncoding($options['encoding']);
        $this->setReplaceWhiteSpace($options['replaceWhiteSpace']);
        $this->setOnlyAlnum($options['onlyAlnum']);
        $this->setRelevantChars($options['relevantChars']);
        $this->setIrrelevantChars($options['irrelevantChars']);
    }

    /**
     * Set "the character" to replace spaces
     *
     * @param  string $value
     * 
     * @return SlugUrl
     */
    public function setReplaceWhiteSpace($value)
    {
        $this->replaceWhiteSpace = $value;
        return $this;
    }

    /**
     * Get "the character" to replace spaces 
     *
     * @return string
     */
    public function getReplaceWhiteSpace()
    {
        return $this->replaceWhiteSpace;
    }

    /**
     * $value = true  #to consider only alphanumeric characters
     * $value = false #consider any characters
     *
     * @param  string $value
     *
     * @return SlugUrl
     */
    public function setOnlyAlnum($value)
    {
        $this->onlyAlnum = $value;
        return $this;
    }

    /**
     * Get onlyAlnum
     *
     * @return boolean 
     */
    public function getOnlyAlnum()
    {
        return $this->onlyAlnum;
    }

    /**
     * Get encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Set encoding
     *
     * @param  string $value
     * 
     * @return SlugUrl
     */
    public function setEncoding($value)
    {
        $this->encoding = (string) $value;
        return $this;
    }

    /**
     * Get relevant characters
     *
     * @return string|array
     */
    public function getRelevantChars()
    {
        return $this->relevantChars;
    }

    /**
     * Set characters
     *
     * @param  string|array $chars
     * 
     * @return SlugUrl
     */
    public function setRelevantChars($chars)
    {
        $this->relevantChars = $chars;
        return $this;
    }

    /**
     * Get irrelevant characters
     *
     * @return string|array
     */
    public function getIrrelevantChars()
    {
        return $this->irrelevantChars;
    }

    /**
     * Set irrelevantes characters arrow irrelevant that the characters 
     * need to be replaced by the parameter chosen "replaceWhiteSpace"
     *
     * @param  string|array $chars
     * 
     * @return SlugUrl
     */
    public function setIrrelevantChars($chars)
    {
        $this->irrelevantChars = $chars;
        return $this;
    }

    /**
     * Returns the result of filtering $value
     *
     * @param  string $value
     * 
     * @throws Exception\ExtensionNotLoadedException 
     * @return string 
     */
    public function filter($value)
    {
        if (!function_exists('iconv')) {
            throw new Exception\ExtensionNotLoadedException(sprintf(
                '%s requires iconv extension to be loaded',
                get_class($this)
            ));
        }

        //Stores the locale and then put the end
        $localeMemento = $this->getLocale();

        //Get options
        $enc = $this->getEncoding();
        $rws = $this->getReplaceWhiteSpace();
        $oan = $this->getOnlyAlNum();
        $rlc = $this->getRelevantChars();
        $ilc = $this->getIrrelevantChars();
       
        //Set locale
        setlocale(LC_ALL, "en_US.{$enc}");

        //suppress errors @iconv
        $filtered = @iconv($enc, 'ASCII//TRANSLIT', $value);

        $relevantChars   = (is_array($rlc))? implode('', $rlc) : $rlc;
        $irrelevantChars = (is_array($ilc))? implode('', $ilc) : $ilc;

        if (!empty($ilc)) {
            $filtered  = preg_replace("/[{$irrelevantChars}{$rws}]+/", self::WHITE_SPACE, $filtered);
        }
 
        if (true === $oan) {
            $filtered  = preg_replace("/[^a-zA-Z0-9{$irrelevantChars}{$relevantChars}\\{$rws} ]*/", '', trim($filtered));
        }

        if (self::WHITE_SPACE !== $rws) {
            $filtered = preg_replace('/\s\s+/', self::WHITE_SPACE, $filtered);
            $filtered = str_replace(self::WHITE_SPACE, $rws, $filtered);
        }

        //sets the locale option again to avoid conflicts
        $this->setLocale($localeMemento);

        return $filtered;
    }
}
?>
