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

    public static $INFLIGHT_BASELINE = 'INFLIGHT_BASELINE';
    public static $INFLIGHT_PROJECTS = 'INFLIGHT_PROJECTS';


    public static $RESOURCE_REQUESTS = 'RESOURCE_REQUESTS';
    public static $RESOURCE_REQUEST_HOURS = 'RESOURCE_REQUEST_HOURS';

    public static $RFS = 'RFS';

    public static $STATIC_CIO = 'STATIC_CIO';
    public static $STATIC_CTB_SERVICE = 'STATIC_CTB_SERVICE';

    public static $UPLOAD_LOG = 'UPLOAD_LOG';

}