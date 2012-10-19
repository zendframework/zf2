<?php
//namespace SampleModule\AssetsSoap;

class Soaps
{
    /**
    * Add Data method
    *
    * @param  Int $first
    * @param  Int $second
    * @return Int
    */
    public function add_data($first, $second)
    {
        $return =  $first + $second;
        return $return;
    }
   
    /**
    * Sub Data method
    *
    * @param  Int $first
    * @param  Int $second
    * @return Int
    */
    public function sub_data($first, $second)
    {
        return $first - $second;
    }
}