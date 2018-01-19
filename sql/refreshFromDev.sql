insert into ROB_DEV.BASELINE ( select * from REST_DEV.BASELINE);
insert into ROB_DEV.INFLIGHT_PROJECTS ( select * from REST_DEV.INFLIGHT_PROJECTS);
insert into ROB_DEV.RFS ( select * from REST_DEV.RFS);
insert into ROB_DEV.RESOURCE_REQUESTS ( select * from REST_DEV.RESOURCE_REQUESTS);
insert into ROB_DEV.RESOURCE_REQUEST_HOURS ( select * from REST_DEV.RESOURCE_REQUEST_HOURS);
insert into ROB_DEV.STATIC_CIO ( select * from REST_DEV.STATIC_CIO);
insert into ROB_DEV.STATIC_CURRENT_PLATFORM ( select * from REST_DEV.STATIC_CURRENT_PLATFORM);
insert into ROB_DEV.STATIC_RESOURCE_TYPE ( select * from REST_DEV.STATIC_RESOURCE_TYPE);
insert into ROB_DEV.UPLOAD_LOG ( select * from REST_DEV.UPLOAD_LOG);

CALL SYSPROC.ADMIN_CMD ('REORG TABLE ROB_DEV.BASELINE');
CALL SYSPROC.ADMIN_CMD ('REORG TABLE ROB_DEV.INFLIGHT_PROJECTS');
CALL SYSPROC.ADMIN_CMD ('REORG TABLE ROB_DEV.RFS');
CALL SYSPROC.ADMIN_CMD ('REORG TABLE ROB_DEV.RESOURCE_REQUESTS');
CALL SYSPROC.ADMIN_CMD ('REORG TABLE ROB_DEV.RESOURCE_REQUEST_HOURS');
CALL SYSPROC.ADMIN_CMD ('REORG TABLE ROB_DEV.STATIC_CIO');
CALL SYSPROC.ADMIN_CMD ('REORG TABLE ROB_DEV.STATIC_CURRENT_PLATFORM');
CALL SYSPROC.ADMIN_CMD ('REORG TABLE ROB_DEV.STATIC_RESOURCE_TYPE');
CALL SYSPROC.ADMIN_CMD ('REORG TABLE ROB_DEV.UPLOAD_LOG');



CREATE TABLE "ROB_DEV"."UPLOAD_LOG" (
		"UPLOAD_ID" INTEGER NOT NULL GENERATED BY DEFAULT AS IDENTITY ( START WITH 1 INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 NO CYCLE CACHE 20),
		"UPLOAD_TIMESTAMP" TIMESTAMP NOT NULL DEFAULT CURRENT TIMESTAMP,
		"UPLOAD_INTRANET" CHAR(60) DEFAULT NULL,
		"UPLOAD_STATUS" CHAR(20) DEFAULT NULL,
		"UPLOAD_FILENAME" VARCHAR(256) NOT NULL DEFAULT '',
		"UPLOAD_TABLENAME" CHAR(50) DEFAULT NULL
	)
	DATA CAPTURE NONE;

ALTER TABLE "ROB_DEV"."UPLOAD_LOG" ADD CONSTRAINT "Q_DPULSE_UPLOAD_LOG_UPLOAD_ID_00001" PRIMARY KEY
	("UPLOAD_ID");