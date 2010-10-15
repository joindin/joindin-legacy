alter table talk_comments drop index idx_talk, add index idx_talk (talk_id, rating);
alter table talk_cat add index idx_talk_cat (talk_id, cat_id);
alter table talks add index idx_talk_event (event_id);

