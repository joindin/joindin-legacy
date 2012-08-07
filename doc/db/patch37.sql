-- setting the default value of talks.active to 1 rather than always having to do this manually

ALTER TABLE talks ALTER COLUMN active SET DEFAULT 1;

INSERT INTO patch_history SET patch_number = 37;
