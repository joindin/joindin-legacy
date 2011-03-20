# Adding the new columns to the talk_speakers table for claiming

ALTER TABLE `talk_speaker` add `speaker_id` INT;
ALTER TABLE `talk_speaker` add `status` VARCHAR(10);

INSERT INTO patch_history SET patch_number = 14;
