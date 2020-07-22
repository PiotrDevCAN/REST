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
    
    public static $RESOURCE_REQUESTS      = 'RESOURCE_REQUESTS';
    public static $RESOURCE_REQUEST_HOURS = 'RESOURCE_REQUEST_HOURS';

    public static $RFS                    = 'RFS';
    public static $RFS_PIPELINE           = 'RFS_PIPELINE';  // View

    public static $STATIC_VALUE_STREAM    = 'STATIC_VALUE_STREAM';
    public static $STATIC_ORGANISATION    = 'STATIC_ORGANISATION';


}