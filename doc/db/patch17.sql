# cleaning up errors with patches 15 and 16 which were missing patch history info

# if this fails, that is fine
ALTER TABLE talk_speaker DROP COLUMN `rcode`;

INSERT INTO patch_history SET patch_number = 17;
