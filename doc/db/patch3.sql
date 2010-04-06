-- patch: 3
-- author: kevin
-- date: 3-Apr-2010

alter table events change column event_lat  event_lat  decimal(20,16);
alter table events change column event_long event_long decimal(20,16);

UPDATE `meta_data` SET value="3" WHERE entry = "version";
