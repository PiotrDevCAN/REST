--<ScriptOptions statementTerminator=";"/>

CREATE TABLE "REST_V1"."ARCHIVED_RESOURCE_REQUESTS" (
		"RESOURCE_REFERENCE" INTEGER NOT NULL,
		"RFS" CHAR(20) DEFAULT NULL,
		"PHASE" CHAR(50) DEFAULT NULL,
		"CURRENT_PLATFORM" CHAR(150) DEFAULT NULL,
		"RESOURCE_TYPE" CHAR(200) DEFAULT NULL,
		"DESCRIPTION" VARCHAR(2048) DEFAULT NULL,
		"START_DATE" DATE DEFAULT NULL,
		"END_DATE" DATE DEFAULT NULL,
		"HRS_PER_WEEK" INTEGER DEFAULT NULL,
		"RESOURCE_NAME" CHAR(150) DEFAULT NULL,
		"RR_CREATOR" CHAR(120) DEFAULT NULL,
		"RR_CREATED_TIMESTAMP" TIMESTAMP NOT NULL DEFAULT CURRENT TIMESTAMP,
		"PARENT_BWO" INTEGER DEFAULT NULL,
		"CLONED_FROM" INTEGER DEFAULT NULL,
		"DRAWN_DOWN_FOR_PRN" CHAR(10) DEFAULT NULL,
		"DRAWN_DOWN_FOR_PROJECT_CODE" CHAR(12) DEFAULT NULL,
		"STATUS" CHAR(40) DEFAULT NULL
	)
	DATA CAPTURE NONE;

CREATE TABLE "REST_V1"."ARCHIVED_RESOURCE_REQUEST_HOURS" (
		"RESOURCE_REFERENCE" INTEGER NOT NULL,
		"DATE" DATE NOT NULL,
		"HOURS" DECIMAL(3 , 1) NOT NULL,
		"YEAR" INTEGER DEFAULT NULL,
		"WEEK_NUMBER" INTEGER DEFAULT NULL,
		"WEEK_ENDING_FRIDAY" DATE DEFAULT NULL,
		"CLAIM_CUTOFF" DATE DEFAULT NULL,
		"CLAIM_MONTH" INTEGER DEFAULT NULL,
		"CLAIM_YEAR" INTEGER DEFAULT NULL
	)
	DATA CAPTURE NONE 
	COMPRESS YES
	VALUE COMPRESSION;

CREATE TABLE "REST_V1"."AUDIT" (
		"TIMESTAMP" TIMESTAMP DEFAULT CURRENT TIMESTAMP,
		"EMAIL_ADDRESS" CHAR(60),
		"DATA" CLOB(1048576),
		"TYPE" CHAR(10)
	)
	DATA CAPTURE NONE;

CREATE TABLE "REST_V1"."DB2_ERRORS" (
		"USERID" CHAR(50),
		"PAGE" VARCHAR(200),
		"DB2_ERROR" CHAR(10),
		"DB2_MESSAGE" CHAR(200),
		"BACKTRACE" VARCHAR(1024),
		"REQUEST" VARCHAR(1024),
		"TIMESTAMP" TIMESTAMP
	)
	DATA CAPTURE NONE;

CREATE TABLE "REST_V1"."EMAIL_LOG" (
		"RECORD_ID" INTEGER NOT NULL,
		"TO" VARCHAR(512) DEFAULT NULL,
		"SUBJECT" VARCHAR(200) DEFAULT NULL,
		"MESSAGE" CLOB(512000) DEFAULT NULL,
		"DATA_JSON" CLOB(5242880) DEFAULT NULL,
		"RESPONSE" CLOB(10240) DEFAULT NULL,
		"LAST_STATUS" CLOB(10240) DEFAULT NULL,
		"SENT_TIMESTAMP" TIMESTAMP DEFAULT CURRENT TIMESTAMP,
		"STATUS_TIMESTAMP" TIMESTAMP DEFAULT NULL
	)
	DATA CAPTURE NONE;

CREATE TABLE "REST_V1"."INFLIGHT_BASELINE" (
		"CIO" CHAR(46),
		"PRN" CHAR(11),
		"PROJECTNAME" CHAR(80),
		"PROJECTNUMBER" CHAR(11),
		"CURRENTPLATFORM" CHAR(66),
		"RESOURCETYPE" CHAR(19),
		"RESOURCENAME" CHAR(120),
		"SEP_IBM" DECIMAL(8 , 3),
		"OCT_IBM" DECIMAL(8 , 3),
		"NOV_IBM" DECIMAL(8 , 3),
		"DEC_IBM" DECIMAL(8 , 3),
		"SEP_TO_DEC_TOTAL_FORECAST" DECIMAL(12 , 3),
		"SEP_TO_DEC_IBM" DECIMAL(12 , 3),
		"REMAININGYEARTOTALCY" DECIMAL(12 , 3),
		"TAXONOMY" CHAR(32),
		"DIVISION" CHAR(35),
		"ITDELIVERYDIRECTOR" CHAR(21),
		"PRNNAME" CHAR(69),
		"GCMREFERENCE" CHAR(7),
		"PROJECTPRIORITISATION" CHAR(21),
		"COMPLEXITYRATING" CHAR(10),
		"ITPM" CHAR(35),
		"CLARITY_PM_OWNER" CHAR(32),
		"DIRECTORATE" CHAR(37),
		"HOF" CHAR(49),
		"EMPLOYEETYPE" CHAR(29),
		"VAT_REDUCTION_APPLIED" CHAR(33),
		"EMPLOYEENUMBER" CHAR(8),
		"NEW_FILE_ID" CHAR(7),
		"AGENCY" CHAR(18),
		"JOBNAME" CHAR(62),
		"ALLOCATION" CHAR(16)
	)
	DATA CAPTURE NONE;

CREATE TABLE "REST_V1"."INFLIGHT_PROJECTS" (
		"CIO" CHAR(46) DEFAULT NULL,
		"PRN" CHAR(11) DEFAULT NULL,
		"PROJECTNAME" CHAR(80) DEFAULT NULL,
		"PROJECTNUMBER" CHAR(11) DEFAULT NULL,
		"CURRENTPLATFORM" CHAR(66) DEFAULT NULL,
		"RESOURCETYPE" CHAR(19) DEFAULT NULL,
		"RESOURCENAME" CHAR(120) DEFAULT NULL,
		"SEP_IBM" DECIMAL(8 , 3) DEFAULT NULL,
		"OCT_IBM" DECIMAL(8 , 3) DEFAULT NULL,
		"NOV_IBM" DECIMAL(8 , 3) DEFAULT NULL,
		"DEC_IBM" DECIMAL(8 , 3) DEFAULT NULL,
		"SEP_TO_DEC_TOTAL_FORECAST" DECIMAL(12 , 3) DEFAULT NULL,
		"SEP_TO_DEC_IBM" DECIMAL(12 , 3) DEFAULT NULL,
		"REMAININGYEARTOTALCY" DECIMAL(12 , 3) DEFAULT NULL,
		"TAXONOMY" CHAR(32) DEFAULT NULL,
		"DIVISION" CHAR(35) DEFAULT NULL,
		"ITDELIVERYDIRECTOR" CHAR(21) DEFAULT NULL,
		"PRNNAME" CHAR(69) DEFAULT NULL,
		"GCMREFERENCE" CHAR(7) DEFAULT NULL,
		"PROJECTPRIORITISATION" CHAR(21) DEFAULT NULL,
		"COMPLEXITYRATING" CHAR(10) DEFAULT NULL,
		"ITPM" CHAR(35) DEFAULT NULL,
		"CLARITY_PM_OWNER" CHAR(32) DEFAULT NULL,
		"DIRECTORATE" CHAR(37) DEFAULT NULL,
		"HOF" CHAR(49) DEFAULT NULL,
		"EMPLOYEETYPE" CHAR(29) DEFAULT NULL,
		"VAT_REDUCTION_APPLIED" CHAR(33) DEFAULT NULL,
		"EMPLOYEENUMBER" CHAR(8) DEFAULT NULL,
		"NEW_FILE_ID" CHAR(7) DEFAULT NULL,
		"AGENCY" CHAR(18) DEFAULT NULL,
		"JOBNAME" CHAR(62) DEFAULT NULL,
		"ALLOCATION" CHAR(16) DEFAULT NULL
	)
	DATA CAPTURE NONE;

CREATE TABLE "REST_V1"."ORIG_RESOURCE_REQUESTS" (
		"RESOURCE_REFERENCE" INTEGER NOT NULL GENERATED BY DEFAULT AS IDENTITY ( START WITH 1 INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 NO CYCLE CACHE 20),
		"RFS" CHAR(20) DEFAULT NULL,
		"PHASE" CHAR(50) DEFAULT NULL,
		"CURRENT_PLATFORM" CHAR(150) DEFAULT NULL,
		"RESOURCE_TYPE" CHAR(200) DEFAULT NULL,
		"DESCRIPTION" VARCHAR(500) DEFAULT NULL,
		"START_DATE" DATE DEFAULT NULL,
		"END_DATE" DATE DEFAULT NULL,
		"HRS_PER_WEEK" INTEGER DEFAULT NULL,
		"RESOURCE_NAME" CHAR(150) DEFAULT NULL,
		"RR_CREATOR" CHAR(120) DEFAULT NULL,
		"RR_CREATED_TIMESTAMP" TIMESTAMP NOT NULL DEFAULT CURRENT TIMESTAMP,
		"PARENT_BWO" INTEGER DEFAULT NULL,
		"CLONED_FROM" INTEGER DEFAULT NULL,
		"DRAWN_DOWN_FOR_PRN" CHAR(10) DEFAULT NULL,
		"DRAWN_DOWN_FOR_PROJECT_CODE" CHAR(12) DEFAULT NULL
	)
	DATA CAPTURE NONE;

CREATE TABLE "REST_V1"."PLATFORM_TEAMS" (
		"OLD_NAME" CHAR(200) NOT NULL,
		"NEW_NAME" CHAR(200) NOT NULL,
		"PRIMARY_RESOURCE_MANAGER" CHAR(75) NOT NULL,
		"RESOURCE_TYPE" CHAR(200) NOT NULL
	)
	DATA CAPTURE NONE;

CREATE TABLE "REST_V1"."RESOURCE_REQUESTS" (
		"RESOURCE_REFERENCE" INTEGER NOT NULL GENERATED BY DEFAULT AS IDENTITY ( START WITH 1 INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 NO CYCLE CACHE 20),
		"RFS" CHAR(20) DEFAULT NULL,
		"PHASE" CHAR(50) DEFAULT NULL,
		"CURRENT_PLATFORM" CHAR(150) DEFAULT NULL,
		"RESOURCE_TYPE" CHAR(200) DEFAULT NULL,
		"DESCRIPTION" VARCHAR(2048) DEFAULT NULL,
		"START_DATE" DATE DEFAULT NULL,
		"END_DATE" DATE DEFAULT NULL,
		"HRS_PER_WEEK" INTEGER DEFAULT NULL,
		"RESOURCE_NAME" CHAR(150) DEFAULT NULL,
		"RR_CREATOR" CHAR(120) DEFAULT NULL,
		"RR_CREATED_TIMESTAMP" TIMESTAMP NOT NULL DEFAULT CURRENT TIMESTAMP,
		"PARENT_BWO" INTEGER DEFAULT NULL,
		"CLONED_FROM" INTEGER DEFAULT NULL,
		"DRAWN_DOWN_FOR_PRN" CHAR(10) DEFAULT NULL,
		"DRAWN_DOWN_FOR_PROJECT_CODE" CHAR(12) DEFAULT NULL,
		"STATUS" CHAR(40) DEFAULT NULL,
		"SYS_START" TIMESTAMP NOT NULL,
		"SYS_END" TIMESTAMP NOT NULL,
		"TRANS_ID" TIMESTAMP
	)
	DATA CAPTURE NONE;

CREATE TABLE "REST_V1"."RESOURCE_REQUESTS_HISTORY" (
		"RESOURCE_REFERENCE" INTEGER NOT NULL,
		"RFS" CHAR(20) DEFAULT NULL,
		"PHASE" CHAR(50) DEFAULT NULL,
		"CURRENT_PLATFORM" CHAR(150) DEFAULT NULL,
		"RESOURCE_TYPE" CHAR(200) DEFAULT NULL,
		"DESCRIPTION" VARCHAR(2048) DEFAULT NULL,
		"START_DATE" DATE DEFAULT NULL,
		"END_DATE" DATE DEFAULT NULL,
		"HRS_PER_WEEK" INTEGER DEFAULT NULL,
		"RESOURCE_NAME" CHAR(150) DEFAULT NULL,
		"RR_CREATOR" CHAR(120) DEFAULT NULL,
		"RR_CREATED_TIMESTAMP" TIMESTAMP NOT NULL DEFAULT CURRENT TIMESTAMP,
		"PARENT_BWO" INTEGER DEFAULT NULL,
		"CLONED_FROM" INTEGER DEFAULT NULL,
		"DRAWN_DOWN_FOR_PRN" CHAR(10) DEFAULT NULL,
		"DRAWN_DOWN_FOR_PROJECT_CODE" CHAR(12) DEFAULT NULL,
		"STATUS" CHAR(40) DEFAULT NULL,
		"SYS_START" TIMESTAMP NOT NULL,
		"SYS_END" TIMESTAMP NOT NULL,
		"TRANS_ID" TIMESTAMP
	)
	DATA CAPTURE NONE;

CREATE TABLE "REST_V1"."RESOURCE_REQUEST_HOURS" (
		"RESOURCE_REFERENCE" INTEGER NOT NULL,
		"DATE" DATE NOT NULL,
		"HOURS" DECIMAL(3 , 1) NOT NULL,
		"YEAR" INTEGER DEFAULT NULL,
		"WEEK_NUMBER" INTEGER DEFAULT NULL,
		"WEEK_ENDING_FRIDAY" DATE DEFAULT NULL,
		"CLAIM_CUTOFF" DATE DEFAULT NULL,
		"CLAIM_MONTH" INTEGER DEFAULT NULL,
		"CLAIM_YEAR" INTEGER DEFAULT NULL
	)
	DATA CAPTURE NONE;

CREATE TABLE "REST_V1"."RESOURCE_REQUEST_MONTHLY_HOURS" (
		"RESOURCE_REFERENCE" INTEGER NOT NULL,
		"DATE" DATE NOT NULL,
		"HOURS" DECIMAL(4 , 1) NOT NULL
	)
	DATA CAPTURE NONE;

CREATE TABLE "REST_V1"."RESOURCE_TYPE_MAPPING" (
		"RESOURCE_TYPE" CHAR(200) NOT NULL,
		"RESOURCE_NOTESID" CHAR(75) NOT NULL
	)
	DATA CAPTURE NONE;

CREATE TABLE "REST_V1"."RFS" (
		"RFS_ID" CHAR(20) NOT NULL,
		"PRN" CHAR(24) DEFAULT NULL,
		"PROJECT_TITLE" CHAR(120) DEFAULT NULL,
		"PROJECT_CODE" CHAR(24) DEFAULT NULL,
		"REQUESTOR_NAME" CHAR(120) DEFAULT NULL,
		"REQUESTOR_EMAIL" CHAR(200) DEFAULT NULL,
		"CIO" CHAR(120) DEFAULT NULL,
		"LINK_TO_PGMP" VARCHAR(500) DEFAULT NULL,
		"RFS_CREATOR" CHAR(120) DEFAULT NULL,
		"RFS_CREATED_TIMESTAMP" TIMESTAMP NOT NULL DEFAULT CURRENT TIMESTAMP,
		"ARCHIVE" DATE,
		"SYS_START" TIMESTAMP NOT NULL,
		"SYS_END" TIMESTAMP NOT NULL,
		"TRANS_ID" TIMESTAMP
	)
	DATA CAPTURE NONE;

CREATE TABLE "REST_V1"."RFS_HISTORY" (
		"RFS_ID" CHAR(20) NOT NULL,
		"PRN" CHAR(24) DEFAULT NULL,
		"PROJECT_TITLE" CHAR(120) DEFAULT NULL,
		"PROJECT_CODE" CHAR(24) DEFAULT NULL,
		"REQUESTOR_NAME" CHAR(120) DEFAULT NULL,
		"REQUESTOR_EMAIL" CHAR(200) DEFAULT NULL,
		"CIO" CHAR(120) DEFAULT NULL,
		"LINK_TO_PGMP" VARCHAR(500) DEFAULT NULL,
		"RFS_CREATOR" CHAR(120) DEFAULT NULL,
		"RFS_CREATED_TIMESTAMP" TIMESTAMP NOT NULL DEFAULT CURRENT TIMESTAMP,
		"ARCHIVE" DATE,
		"SYS_START" TIMESTAMP NOT NULL,
		"SYS_END" TIMESTAMP NOT NULL,
		"TRANS_ID" TIMESTAMP
	)
	DATA CAPTURE NONE;

CREATE TABLE "REST_V1"."STATIC_CIO" (
		"CIO" CHAR(100) NOT NULL
	)
	DATA CAPTURE NONE;

CREATE TABLE "REST_V1"."STATIC_CURRENT_PLATFORM" (
		"CURRENT_PLATFORM" CHAR(150) NOT NULL
	)
	DATA CAPTURE NONE;

CREATE TABLE "REST_V1"."STATIC_RESOURCE_TYPE" (
		"RESOURCE_TYPE" CHAR(200) NOT NULL
	)
	DATA CAPTURE NONE;

CREATE TABLE "REST_V1"."TEMP" (
		"RFS" CHAR(20)
	)
	DATA CAPTURE NONE;

CREATE TABLE "REST_V1"."TRACE" (
		"LOG_ENTRY" VARCHAR(32000) NOT NULL,
		"LASTUPDATER" CHAR(50) NOT NULL,
		"LASTUPDATED" TIMESTAMP NOT NULL DEFAULT CURRENT TIMESTAMP,
		"CLASS" CHAR(50) DEFAULT NULL,
		"METHOD" CHAR(50) DEFAULT NULL,
		"PAGE" VARCHAR(200) DEFAULT NULL,
		"ELAPSED" DOUBLE DEFAULT NULL
	)
	DATA CAPTURE NONE;

CREATE TABLE "REST_V1"."TRACE_CONTROL" (
		"TRACE_CONTROL_TYPE" CHAR(20) DEFAULT NULL,
		"TRACE_CONTROL_VALUE" CHAR(40) DEFAULT NULL
	)
	DATA CAPTURE NONE;

CREATE TABLE "REST_V1"."UPLOAD_LOG" (
		"UPLOAD_ID" INTEGER NOT NULL GENERATED BY DEFAULT AS IDENTITY ( START WITH 1 INCREMENT BY 1 MINVALUE 1 MAXVALUE 2147483647 NO CYCLE CACHE 20),
		"UPLOAD_TIMESTAMP" TIMESTAMP NOT NULL DEFAULT CURRENT TIMESTAMP,
		"UPLOAD_INTRANET" CHAR(60) DEFAULT NULL,
		"UPLOAD_STATUS" CHAR(20) DEFAULT NULL,
		"UPLOAD_FILENAME" VARCHAR(256) NOT NULL DEFAULT '',
		"UPLOAD_TABLENAME" CHAR(50) DEFAULT NULL
	)
	DATA CAPTURE NONE;

CREATE INDEX "REST_V1"."Archive2"
	ON "REST_V1"."RFS"
	("RFS_ID"		ASC,
	  "ARCHIVE"		ASC) PCTFREE 10
ALLOW REVERSE SCANS;

CREATE INDEX "REST_V1"."RFS"
	ON "REST_V1"."RESOURCE_REQUESTS"
	("RFS"		ASC) PCTFREE 10
ALLOW REVERSE SCANS;

CREATE INDEX "REST_V1"."RFS_ARCHIVE"
	ON "REST_V1"."RFS"
	("ARCHIVE"		ASC,
	  "RFS_ID"		ASC) PCTFREE 10
ALLOW REVERSE SCANS;

CREATE INDEX "REST_V1"."RFS_RR"
	ON "REST_V1"."RESOURCE_REQUESTS"
	("RFS"		ASC,
	  "RESOURCE_REFERENCE"		ASC) PCTFREE 10
ALLOW REVERSE SCANS;

CREATE INDEX "REST_V1"."claimMonthsYearRef"
	ON "REST_V1"."RESOURCE_REQUEST_HOURS"
	("CLAIM_MONTH"		ASC,
	  "CLAIM_YEAR"		ASC,
	  "RESOURCE_REFERENCE"		ASC,
	  "CLAIM_CUTOFF"		ASC) PCTFREE 10
ALLOW REVERSE SCANS;

ALTER TABLE "REST_V1"."ORIG_RESOURCE_REQUESTS" ADD CONSTRAINT "Q_REST_RESOU00001_RESOU00001_00001" PRIMARY KEY
	("RESOURCE_REFERENCE");

ALTER TABLE "REST_V1"."PLATFORM_TEAMS" ADD CONSTRAINT "IX01" PRIMARY KEY
	("OLD_NAME");

ALTER TABLE "REST_V1"."RESOURCE_REQUESTS" ADD CONSTRAINT "Q_REST_RESOU00001_RESOU00001_00001" PRIMARY KEY
	("RESOURCE_REFERENCE");

ALTER TABLE "REST_V1"."RESOURCE_REQUEST_HOURS" ADD CONSTRAINT "Q_REST_RESOU00002_RESOU00001_00002" PRIMARY KEY
	("RESOURCE_REFERENCE",
	 "DATE");

ALTER TABLE "REST_V1"."RFS" ADD CONSTRAINT "Q_REST_RFS_RFS_ID_00001" PRIMARY KEY
	("RFS_ID");

ALTER TABLE "REST_V1"."STATIC_CIO" ADD CONSTRAINT "Q_REST_CIO_CIO_00001" PRIMARY KEY
	("CIO");

ALTER TABLE "REST_V1"."STATIC_CURRENT_PLATFORM" ADD CONSTRAINT "Q_REST_STATI00001_CURRE00001_00001" PRIMARY KEY
	("CURRENT_PLATFORM");

ALTER TABLE "REST_V1"."STATIC_RESOURCE_TYPE" ADD CONSTRAINT "Q_REST_STATI00002_RESOU00001_00001" PRIMARY KEY
	("RESOURCE_TYPE");

ALTER TABLE "REST_V1"."UPLOAD_LOG" ADD CONSTRAINT "Q_DPULSE_UPLOAD_LOG_UPLOAD_ID_00001" PRIMARY KEY
	("UPLOAD_ID");

GRANT ALTER ON TABLE "REST_V1"."AUDIT" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT ALTER ON TABLE "REST_V1"."RESOURCE_REQUESTS_HISTORY" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT ALTER ON TABLE "REST_V1"."RESOURCE_REQUEST_MONTHLY_HOURS" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT ALTER ON TABLE "REST_V1"."RFS_HISTORY" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT CONTROL ON TABLE "REST_V1"."AUDIT" TO USER "ROBDANIEL";

GRANT CONTROL ON TABLE "REST_V1"."RESOURCE_REQUESTS_HISTORY" TO USER "ROBDANIEL";

GRANT CONTROL ON TABLE "REST_V1"."RESOURCE_REQUEST_MONTHLY_HOURS" TO USER "ROBDANIEL";

GRANT CONTROL ON TABLE "REST_V1"."RFS_HISTORY" TO USER "ROBDANIEL";

GRANT DELETE ON TABLE "REST_V1"."AUDIT" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT DELETE ON TABLE "REST_V1"."RESOURCE_REQUESTS_HISTORY" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT DELETE ON TABLE "REST_V1"."RESOURCE_REQUEST_MONTHLY_HOURS" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT DELETE ON TABLE "REST_V1"."RFS_HISTORY" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT INDEX ON TABLE "REST_V1"."AUDIT" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT INDEX ON TABLE "REST_V1"."RESOURCE_REQUESTS_HISTORY" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT INDEX ON TABLE "REST_V1"."RESOURCE_REQUEST_MONTHLY_HOURS" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT INDEX ON TABLE "REST_V1"."RFS_HISTORY" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT INSERT ON TABLE "REST_V1"."AUDIT" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT INSERT ON TABLE "REST_V1"."RESOURCE_REQUESTS_HISTORY" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT INSERT ON TABLE "REST_V1"."RESOURCE_REQUEST_MONTHLY_HOURS" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT INSERT ON TABLE "REST_V1"."RFS_HISTORY" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT REFERENCES ON TABLE "REST_V1"."AUDIT" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT REFERENCES ON TABLE "REST_V1"."RESOURCE_REQUESTS_HISTORY" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT REFERENCES ON TABLE "REST_V1"."RESOURCE_REQUEST_MONTHLY_HOURS" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT REFERENCES ON TABLE "REST_V1"."RFS_HISTORY" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT SELECT ON TABLE "REST_V1"."AUDIT" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT SELECT ON TABLE "REST_V1"."RESOURCE_REQUESTS_HISTORY" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT SELECT ON TABLE "REST_V1"."RESOURCE_REQUEST_MONTHLY_HOURS" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT SELECT ON TABLE "REST_V1"."RFS_HISTORY" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT UPDATE ON TABLE "REST_V1"."AUDIT" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT UPDATE ON TABLE "REST_V1"."RESOURCE_REQUESTS_HISTORY" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT UPDATE ON TABLE "REST_V1"."RESOURCE_REQUEST_MONTHLY_HOURS" TO USER "ROBDANIEL" WITH GRANT OPTION;

GRANT UPDATE ON TABLE "REST_V1"."RFS_HISTORY" TO USER "ROBDANIEL" WITH GRANT OPTION;

