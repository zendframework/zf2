<?php
namespace Zend\Cli\Prompt;

use Zend\Cli\Prompt;

class Select extends Char implements Prompt
{
    /**
     * @var string
     */
    protected $promptText = 'Please select an option';

    /**
     * @var bool
     */
    protected $ignoreCase = true;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * Ask the user to select one of pre-defined options
     *
     * @param string    $promptText     The prompt text to display in console
     * @param array     $options        Allowed options
     * @param bool      $allowEmpty     Allow empty (no) selection?
     * @param bool      $echo           True to display selected option?
     */
    public function __construct(
        $promptText = 'Please select one option', $options = array(), $allowEmpty = false, $echo = false
    ){
        if($promptText !== null){
            $this->setPromptText($promptText);
        }

        if(!count($options)){
            throw new \BadMethodCallException(
                'Cannot construct a "select" prompt without any options'
            );
        }

        $this->setOptions($options);

        if($allowEmpty !== null){
            $this->setAllowEmpty($allowEmpty);
        }

        if($echo !== null){
            $this->setEcho($echo);
        }

    }

    /**
     * Show a list of options and prompt the user to select one of them.
     *
     * @return string       Selected option
     */
    public function show()
    {
        /**
         * Show prompt text and available options
         */
        $console = $this->getConsole();
        $console->writeLine($this->promptText);
        foreach($this->options as $k=>$v){
            $console->writeLine('  '.$k.') '.$v);
        }

        /**
         * Ask for selection
         */
        $mask = implode("",array_keys($this->options));
        if($this->allowEmpty){
            $mask .= "\r\n";
        }
        $this->setAllowedChars($mask);
        $oldPrompt = $this->promptText;
        $this->promptText = 'Pick one option: ';
        $response = parent::show();
        $this->promptText = $oldPrompt;

        return $response;
    }

    /**
     * Set allowed options
     *
     * @param array|Traversable $options
     */
    public function setOptions($options)
    {
        if(!is_array($options) && !$options instanceof \Traversable){
            throw new \BadMethodCallException(
                'Please specify an array or Traversable object as options'
            );
        }

        if(!is_array($options)){
            $this->options = array();
            foreach($options as $k => $v){
                $this->options[$k] = $v;
            }
        }else{
            $this->options = $options;
        }
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}