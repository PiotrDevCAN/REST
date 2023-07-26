<?php
namespace rest;

use itdq\DbRecord;
use itdq\Loader;
use itdq\FormClass;
use rest\traits\recordTrait;

/**
 *
 * @author gb001399
 *
 */
class diaryRecord extends DbRecord
{
    use recordTrait;

    public $DIARY_REFERENCE;
    public $ENTRY;
    public $CREATOR;
    public $CREATED;
}