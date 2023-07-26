<?php
namespace rest;

use itdq\DbRecord;
use itdq\Loader;
use itdq\FormClass;
use itdq\DbTable;

/**
 *
 * @author gb001399
 *
 */
class uploadLogRecord extends DbRecord
{
    public $UPLOAD_ID;
    public $UPLOAD_TIMESTAMP;
    public $UPLOAD_INTRANET;
    public $UPLOAD_STATUS;
    public $UPLOAD_FILENAME;
    public $UPLOAD_TABLENAME;

    private $uploadLogTable;

    function __construct($pwd=null,$tableName=null){
        $this->uploadLogTable = empty($tableName) ? new DbTable(allTables::$UPLOAD_LOG) : new DbTable($tableName);
    }

    function logUploadStart($intranet,$filename,$tablename){
        $this->UPLOAD_INTRANET= db2_escape_string(trim($intranet));
        $this->UPLOAD_FILENAME = db2_escape_string(trim($filename));
        $this->UPLOAD_TABLENAME = db2_escape_string(trim($tablename));
        $this->UPLOAD_STATUS = 'Started';

        $insertResult = $this->uploadLogTable->insert($this);
        if($insertResult){
            $this->UPLOAD_ID = $this->uploadLogTable->lastId();
        } else {
            new \Exception('Upload Log insert failed');
        }
    }

    function logUploadCompleted(){
        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." .  $this->uploadLogTable->getTableName();
        $sql .= " SET UPLOAD_STATUS='Completed' ";
        $sql .= " WHERE UPLOAD_ID='" . $this->UPLOAD_ID . "' ";

        $this->uploadLogTable->execute($sql);

    }
}