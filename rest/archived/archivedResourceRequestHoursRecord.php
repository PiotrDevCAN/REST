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
class archivedResourceRequestHoursRecord extends DbRecord
{
    public $RESOURCE_REFERENCE;
    public $DATE;
    public $HOURS;   //  ALTER TABLE "REST_UT"."RESOURCE_REQUEST_HOURS" ALTER COLUMN "HOURS" SET DATA TYPE DECIMAL(4,2);    
    public $YEAR;
    public $WEEK_NUMBER;
    public $WEEK_ENDING_FRIDAY;
    public $CLAIM_CUTOFF;
    public $CLAIM_MONTH;
    public $CLAIM_YEAR;
}