# Removing un-needed column
ALTER TABLE talk_speaker DROP COLUMN `rcode`;

INSERT INTO patch_history SET patch_number = 16;

