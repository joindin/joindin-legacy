# Adding feature management tables

CREATE TABLE `event_feature` (
	event_id INT,
	feature_id INT,
	date_added INT,
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID)
);

CREATE TABLE `features` (
	feature_name VARCHAR(200),
	feature_desc TEXT,
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID)
);

INSERT INTO patch_history SET patch_number = 9;
