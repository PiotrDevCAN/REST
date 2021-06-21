<?php
namespace rest\archived;

use itdq\DbRecord;
use itdq\Loader;
use itdq\FormClass;

/**
 *
 * @author gb001399
 *
 */
class archivedDiaryRecord extends DbRecord
{
    public $DIARY_REFERENCE;
    public $ENTRY;
    public $CREATOR;
    public $CREATED;

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