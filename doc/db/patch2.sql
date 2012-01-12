-- patch: 2
-- author: lornajane
-- date: 21-Mar-2010

alter table event_comments add index idx_event (event_id);
alter table user_attend add index idx_event (eid);
alter table events add index idx_dates (event_start, event_end);
alter table user_admin add index idx_event (rid);
alter table talk_comments add index idx_talk (talk_id);
alter table talk_cat add index idx_talk (talk_id);
alter table talks add index idx_event (event_id);
alter table event_track add index idx_event(event_id);
alter table blog_comments add index idx_post(blog_post_id);

UPDATE `meta_data` SET value="2" WHERE entry = "version";
