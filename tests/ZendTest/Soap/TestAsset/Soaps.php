<?php
//namespace SampleModule\AssetsSoap;

class Soaps
{
    /**
    * Add Data method
    *
    * @param  Int $bilpertama
    * @param  Int $bilkedua
    * @return Int
    */
    public function add_data($bilpertama,$bilkedua)
    {
        $return =  $bilpertama + $bilkedua;
        return $return;
    }
   
    /**
    * Sub Data method
    *
    * @param  Int $bilketiga
    * @param  Int $bilkeempat
    * @return Int
    */
    public function sub_data($bilketiga, $bilkeempat)
    {
        return $bilketiga - $bilkeempat;
    }
}