<?php
namespace rest;

use itdq\DbRecord;
use itdq\Loader;
use itdq\FormClass;

/**
 *
 * @author gb001399
 *
 */
class resourceRequestDiaryRecord extends DbRecord
{
    public $RESOURCE_REFERENCE;
    public $DIARY_REFERENCE;

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