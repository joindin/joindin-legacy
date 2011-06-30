# adding in tagging tables for talks and events

# Talk tagging
DROP TABLE IF EXISTS tags_talks;
CREATE TABLE `tags_talks` (
	talk_id INT,
	tag_id INT,
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID)
);

# Event tagging
DROP TABLE IF EXISTS tags_events;
CREATE TABLE `tags_events` (
	event_id INT,
	tag_id INT,
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID)
);

# Tag source
DROP TABLE IF EXISTS tags;
CREATE TABLE `tags` (
	tag_value VARCHAR(200),
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID)
);

INSERT INTO patch_history SET patch_number = 27;

