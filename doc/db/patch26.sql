alter table events modify event_stub varchar(30) not null, add unique index(event_stub);
