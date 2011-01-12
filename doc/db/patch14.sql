# Add new cfp url to the eveents table

ALTER TABLE `events` ADD column `event_cfp_url` VARCHAR(200);

INSERT INTO patch_history SET patch_number = 14;