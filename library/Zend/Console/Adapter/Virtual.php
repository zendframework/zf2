<?php
namespace Zend\Console\Adapter;

use Zend\Console\Adapter;
use Zend\Console\Color;
use Zend\Console\Charset;
use Zend\Console;

/**
 * Virtual buffer adapter
 */
class Virtual extends AbstractAdapter implements Adapter
{
    protected static $hasMBString;

    protected $modeResult;

    /**
     * Determine and return current console width.
     *
     * @return int
     */
    public function getWidth(){
        static $width;
        if($width > 0){
            return $width;
        }

        /**
         * Try to read console size from "mode" command
         */
        if($this->modeResult === null){
            $this->runProbeCommand();
        }

        if(preg_match('/Columns\:\s+(\d+)/',$this->modeResult,$matches)){
            $width = $matches[1];
        }else{
            $width = parent::getWidth();
        }

        return $width;
    }

    /**
     * Determine and return current console height.
     *
     * @return false|int
     */
    public function getHeight(){
        static $height;
        if($height > 0){
            return $height;
        }

        /**
         * Try to read console size from "mode" command
         */
        if($this->modeResult === null){
            $this->runProbeCommand();
        }

        if(preg_match('/Rows\:\s+(\d+)/',$this->modeResult,$matches)){
            $height = $matches[1];
        }else{
            $height = parent::getHeight();
        }

        return $height;
    }

    protected function runProbeCommand(){
        exec('mode',$output,$return);
        if($return || !count($output)){
            $this->modeResult = '';
        }else{
            $this->modeResult = trim(implode('',$output));
        }
    }

    /**
     * Check if console is UTF-8 compatible
     *
     * @return bool
     */
    public function isUtf8(){
        /**
         * Try to read code page info from "mode" command
         */
        if($this->modeResult === null){
            $this->runProbeCommand();
        }

        if(preg_match('/Code page\:\s+(\d+)/',$this->modeResult,$matches)){
            return (int)$matches[1] == 65001;
        }else{
            return false;
        }
    }

    /**
     * Set cursor position
     * @param int   $x
     * @param int   $y
     */
    public function setPos($x, $y){

    }

    /**
     * Return current console window title.
     *
     * @return string
     */
    public function getTitle(){
        /**
         * Try to use powershell to retrieve console window title
         */
        exec('powershell -command "write $Host.UI.RawUI.WindowTitle"',$output,$result);
        if($result || !$output){
            return '';
        }else{
            return trim($output,"\r\n");
        }
    }

    /**
     * Set Console charset to use.
     *
     * @param \Zend\Console\Charset $charset
     */
    public function setCharset(Charset $charset){
        $this->charset = $charset;
    }

    /**
     * Get charset currently in use by this adapter.
     *
     * @return \Zend\Console\Charset $charset
     */
    public function getCharset(){
        if($this->charset === null){
            $this->charset = $this->getDefaultCharset();
        }

        return $this->charset;
    }

    /**
     * @return \Zend\Console\Charset
     */
    public function getDefaultCharset(){
        return new Charset\AsciiExtended;
    }

    protected function switchToUtf8(){
        `mode con cp select=65001`;
    }
}
