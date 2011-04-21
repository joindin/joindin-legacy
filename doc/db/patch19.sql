# Add  request code field to the user table

ALTER TABLE `user` ADD `request_code` CHAR(8) NULL ;

INSERT INTO patch_history SET patch_number = 19;
