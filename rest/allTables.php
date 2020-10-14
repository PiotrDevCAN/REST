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
    
    public static $ARCHIVED_RESOURCE_REQUESTS      = 'ARCHIVED_RESOURCE_REQUESTS';
    public static $ARCHIVED_RESOURCE_REQUEST_HOURS = 'ARCHIVED_RESOURCE_REQUEST_HOURS';
    
    public static $LATEST_DIARY_ENTRIES            = 'LATEST_DIARY_ENTRIES';
    
    public static $RESOURCE_REQUESTS      = 'RESOURCE_REQUESTS';
    public static $RESOURCE_REQUEST_DIARY = 'RESOURCE_REQUEST_DIARY';
    public static $RESOURCE_REQUEST_HOURS = 'RESOURCE_REQUEST_HOURS';

    public static $RFS                    = 'RFS';    
    public static $RFS_DATE_RANGE         = 'RFS_DATE_RANGE'; // View
    public static $RFS_PIPELINE           = 'RFS_PIPELINE';  // View

 //   public static $STATIC_BUSINESS_UNIT   = 'STATIC_BUSINESS_UNIT'; Now a field in Value Stream.
    public static $STATIC_VALUE_STREAM    = 'STATIC_VALUE_STREAM';
    public static $STATIC_ORGANISATION    = 'STATIC_ORGANISATION';


}

/*
 *  CREATE VIEW "REST".RFS_DATE_RANGE
		   ( RFS_ID, START_DATE, END_DATE, READY_FOR_LIVE)
		AS SELECT RFS as RFS_ID, MIN(START_DATE) as START_DATE, MAX(END_DATE) as END_DATE,
		    case when MIN(START_DATE) >= CURRENT DATE then 'Yes' else 'No' end as READY_FOR_LIVE
		   FROM REST.RESOURCE_REQUESTS
		   GROUP BY RFS
	       WITH NO ROW MOVEMENT;
 * 
 */