delete from REST_DEV.STATIC_ORGANISATION;


insert into REST_DEV.STATIC_ORGANISATION (ORGANISATION, SERVICE, STATUS ) values
('Managed Services','Application Lifecycle Management','enabled'),
('Architecture','Infrastructure Architect','enabled'),
('Project Services','Cassandra Database Administrator','enabled'),
('Project Services','CMDB Asset Management','enabled'),
('Project Services','Datapower','enabled'),
('Project Services','Filenet','enabled');


insert into REST_DEV.STATIC_ORGANISATION (ORGANISATION, SERVICE, STATUS ) values
('Project Services','Automation Engineering','enabled')
,('Solution Management','Technical Solution Manager','enabled')
,('Project Services','Project Manager','enabled')
,('Project Services','Project Manager (Telephony Site Readiness)','enabled')
,('Managed Services','iSeries Delivery and Projects','enabled')
,('Project Services','Markets Decom','enabled')
,('Project Services','Message Broker','enabled')
,('Project Services','Midrange LUW DB2 Database Administrators (DBA)','enabled')
,('Project Services','MQ','enabled');


insert into REST_DEV.STATIC_ORGANISATION (ORGANISATION, SERVICE, STATUS ) values
('Project Services','Oracle Database Administrators (DBA)','enabled')
,('Architecture','Resiliency Architect','enabled')
,('Architecture','Security Architect','enabled')
,('Project Services','SQL Server Database Administrators (DBA)','enabled')
,('Project Services','Storage Backup','enabled')
,('Project Services','Storage Disk','enabled')
,('Project Services','Storage File Services','enabled')
,('Project Services','Sybase resource','enabled')
,('Project Services','Tivoli Monitoring','enabled')
,('Project Services','Unix Decom','enabled');




select *
FROM REST_DEV.STATIC_ORGANISATION;






