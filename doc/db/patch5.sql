ALTER TABLE `talk_speaker`
  CHANGE `talk_id` `talk_id` INT( 11 ) NOT NULL ,
  CHANGE `speaker_name` `speaker_name` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;