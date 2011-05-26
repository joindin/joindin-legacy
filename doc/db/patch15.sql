ALTER TABLE talk_speaker ADD `rcode` varchar(40) default NULL;

INSERT INTO patch_history SET patch_number = 15;

