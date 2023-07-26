<?php
namespace rest;

use itdq\DbRecord;
use rest\traits\recordTrait;
use rest\traits\resourceRequestDiaryRecordTrait;

/**
 *
 * @author gb001399
 *
 */
class resourceRequestDiaryRecord extends DbRecord
{
    use recordTrait, resourceRequestDiaryRecordTrait;

    public $RESOURCE_REFERENCE;
    public $DIARY_REFERENCE;
}