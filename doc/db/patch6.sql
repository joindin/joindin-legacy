/* Add the event_themes table */
create table event_themes (
	theme_name VARCHAR(200),
	theme_desc TEXT,
	active INT,
	event_id INT,
	css_file VARCHAR(200),
	created_by INT,
	created_at INT,
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID)
);