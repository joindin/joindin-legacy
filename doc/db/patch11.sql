# fixing character set of several tables

ALTER TABLE `talk_speaker`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `talk_speaker` CHANGE `speaker_name` `speaker_name` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `event_themes`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `event_themes`
	CHANGE `theme_name` `theme_name` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
	CHANGE `theme_desc` `theme_desc` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
	CHANGE `css_file` `css_file` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `patch_history`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO patch_history SET patch_number = 11;
