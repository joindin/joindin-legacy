-- patch to create the database versioning tables

CREATE TABLE `patch_history` ( patch_history_id int primary key auto_increment, patch_number int, date_patched timestamp);

INSERT INTO patch_history SET patch_number = 8;
