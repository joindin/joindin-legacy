# Adding the new columns to the talk_speakers table for claiming

ALTER TABLE `talk_speaker` CHANGE `speaker_id` `speaker_id` INT( 11 ) NULL;
ALTER TABLE `talk_speaker` CHANGE `status` `status` VARCHAR( 20 ) NULL;

INSERT INTO patch_history SET patch_number = 14;
