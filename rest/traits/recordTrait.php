<?php

namespace rest\traits;

trait recordTrait
{
    function get($field){
        return empty($this->$field) ? null : trim($this->$field);
    }

    function set($field,$value){
        if(!property_exists(__CLASS__,$field)){
            return false;
        } else {
            $this->$field = trim($value);
        }
    }
}