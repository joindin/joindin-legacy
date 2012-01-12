-- no tagging for talks, so remove this unused table
DROP TABLE IF EXISTS tags_talks;

INSERT INTO patch_history SET patch_number = 28;

