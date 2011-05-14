# Optimizing some indices

SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `blog_posts` ADD INDEX `date_posted` USING BTREE (date_posted);
ALTER TABLE `event_themes` ADD INDEX `idx_event` USING BTREE (active, event_id);
ALTER TABLE `events` ADD INDEX `idx_active` USING BTREE (active, pending);
ALTER TABLE `events` ADD INDEX `idx_cfp_dates` USING BTREE (event_cfp_start, event_cfp_end);
ALTER TABLE `talk_speaker` ADD INDEX `talk_id` USING BTREE (talk_id);
ALTER TABLE `talk_track` ADD INDEX `idx_talktracks` USING BTREE (talk_id, track_id);
ALTER TABLE `talk_track` ADD INDEX `idx_tracks` USING BTREE (track_id);
ALTER TABLE `talks` DROP INDEX `idx_talk_event`;
ALTER TABLE `talks` DROP INDEX `idx_event`;
ALTER TABLE `talks` ADD INDEX `idx_event` USING BTREE (event_id, ID);

SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO patch_history SET patch_number = 22;

