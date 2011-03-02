# Adding the "pending claims" table

create table `pending_talk_claims` (
	speaker_id INT,
	talk_id INT,
	date_added INT,
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID)
);
