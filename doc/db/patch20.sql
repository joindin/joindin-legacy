# Add source field to comments

ALTER TABLE `event_comments` ADD `source` VARCHAR( 25 ) NULL DEFAULT NULL;
ALTER TABLE `talk_comments` ADD `source` VARCHAR( 25 ) NULL DEFAULT NULL;

INSERT INTO patch_history SET patch_number = 20;
