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
class resourceRequestHoursRecord extends DbRecord
{

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