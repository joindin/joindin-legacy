-- adding more useful columns to the oauth consumer table
ALTER TABLE oauth_consumers ADD COLUMN user_id int NOT NULL;
ALTER TABLE oauth_consumers ADD COLUMN application varchar(255);
ALTER TABLE oauth_consumers ADD COLUMN description text;

INSERT INTO patch_history SET patch_number = 34;
