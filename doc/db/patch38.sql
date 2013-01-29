-- remove the table for speaker profile info

DROP TABLE speaker_profile;
DROP TABLE speaker_tokens;
DROP TABLE speaker_token_fields;

INSERT INTO patch_history SET patch_number = 38;
