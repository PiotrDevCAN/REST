<?php
namespace rest;

use itdq\DbRecord;
use rest\traits\recordTrait;
use rest\traits\resourceRequestHoursRecordTrait;

/**
 *
 * @author gb001399
 *
 */
class resourceRequestHoursRecord extends DbRecord
{
    use recordTrait, resourceRequestHoursRecordTrait;

    public $RESOURCE_REFERENCE;
    public $DATE;
    public $HOURS;
    public $YEAR;
    public $WEEK_NUMBER;
    public $WEEK_ENDING_FRIDAY;
    public $CLAIM_CUTOFF;
    public $CLAIM_MONTH;
    public $CLAIM_YEAR;
}