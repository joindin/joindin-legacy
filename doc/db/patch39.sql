-- Add duration to talks
ALTER TABLE talks ADD duration INT NOT NULL AFTER date_given;

INSERT INTO patch_history SET patch_number = 39;

