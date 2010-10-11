# adding fields contact_full_name and contact_email

ALTER TABLE `events`
	ADD `event_contact_name` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
	ADD `event_contact_email` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;

INSERT INTO patch_history SET patch_number = 12;
