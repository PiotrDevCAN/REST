<?php
namespace rest\archived;

use itdq\DbRecord;
use rest\traits\recordTrait;
use rest\traits\resourceRequestDiaryRecordTrait;

/**
 *
 * @author gb001399
 *
 */
class archivedResourceRequestDiaryRecord extends DbRecord
{
    use recordTrait, resourceRequestDiaryRecordTrait;
    
    public $RESOURCE_REFERENCE;
    public $DIARY_REFERENCE;
}