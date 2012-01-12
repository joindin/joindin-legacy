-- patch: 1
-- author: lornajane
-- date: 21-Mar-2010

CREATE TABLE `meta_data` (entry varchar(255) PRIMARY KEY, value varchar(255));

INSERT INTO `meta_data` SET entry="version", value="1";
