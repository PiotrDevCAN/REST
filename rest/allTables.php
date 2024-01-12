<?php
namespace rest;

/**
 * Provides a list of public static properties that define the specific table names used in the application.
 *
 * This removes the need to hardcode the table name in the app itself.
 *
 *
 */
class allTables
{
    public static $ARCHIVED_RFS                    = 'ARCHIVED_RFS';
    public static $ARCHIVED_RESOURCE_REQUESTS      = 'ARCHIVED_RESOURCE_REQUESTS';
    public static $ARCHIVED_RESOURCE_REQUEST_HOURS = 'ARCHIVED_RESOURCE_REQUEST_HOURS';
    public static $ARCHIVED_RESOURCE_REQUEST_DIARY = 'ARCHIVED_RESOURCE_REQUEST_DIARY';
    public static $ARCHIVED_DIARY                  = 'ARCHIVED_DIARY';

    public static $BANK_HOLIDAYS                   = 'BANK_HOLIDAYS';
    
//    public static $MQT_MAX_REF                     = 'MQT_MAX_REF';
    public static $LATEST_DIARY_ENTRIES            = 'LATEST_DIARY_ENTRIES';

    public static $RESOURCE_REQUESTS      = 'RESOURCE_REQUESTS';
    public static $RESOURCE_REQUEST_DIARY = 'RESOURCE_REQUEST_DIARY';
    public static $RESOURCE_REQUEST_HOURS = 'RESOURCE_REQUEST_HOURS';

    public static $RFS                    = 'RFS';    
    public static $RFS_DATE_RANGE         = 'RFS_DATE_RANGE'; // View
    public static $RFS_PIPELINE           = 'RFS_PIPELINE';  // View
    public static $RFS_PCR                = 'RFS_PCR'; 

    public static $STATIC_BUSINESS_UNIT                 = 'STATIC_BUSINESS_UNIT';
    public static $STATIC_VALUE_STREAM                  = 'STATIC_VALUE_STREAM';
    public static $STATIC_VALUE_STREAM_BUSINESS_UNIT    = 'STATIC_VALUE_STREAM_BUSINESS_UNIT';

    public static $STATIC_SERVICE                 = 'STATIC_SERVICE';
    public static $STATIC_ORGANISATION            = 'STATIC_ORGANISATION';
    public static $STATIC_ORGANISATION_SERVICE    = 'STATIC_ORGANISATION_SERVICE';

    public static $STATIC_BAND            = 'STATIC_BAND';
    public static $STATIC_PS_BAND         = 'STATIC_PS_BAND';
    public static $STATIC_RESOURCE_TYPE   = 'STATIC_RESOURCE_TYPE';

    public static $RESOURCE_TYPE_RATES    = 'RESOURCE_TYPE_RATES';
    public static $RESOURCE_TRAITS        = 'RESOURCE_TRAITS';

    public static $STATIC_SUBCO           = 'STATIC_SUBCO';
    public static $SUBCO_RATES            = 'SUBCO_RATES';
    public static $BESPOKE_RATES          = 'BESPOKE_RATES';

    public static $ACTIVE_RESOURCE = 'INACTIVE_PERSON';
}