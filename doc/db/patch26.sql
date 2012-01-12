-- patch removed as it causes problems where stubs are emtpy, which is permitted
-- alter table events modify event_stub varchar(30) not null, add unique index(event_stub);

INSERT INTO patch_history SET patch_number = 26;

