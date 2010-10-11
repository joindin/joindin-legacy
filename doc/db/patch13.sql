# adding the twitter username column to the user info

ALTER TABLE `user` add `twitter_username` VARCHAR(20);

INSERT INTO patch_history SET patch_number = 13;
