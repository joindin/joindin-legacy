# adding in tagging tables for talks and events

# Talk tagging
CREATE TABLE `tags_talks` (
	talk_id INT,
	tag_id INT,
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID)
);

# Event tagging
CREATE TABLE `tags_events` (
	event_id INT,
	tag_id INT,
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID)
);

# Tag source
CREATE TABLE `tags` (
	tag_value VARCHAR(200),
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID)
);