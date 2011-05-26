# Adding new pending talk claims table

CREATE TABLE `pending_talk_claims` (
	talk_id INT,
	submitted_by INT,
	speaker_id INT,
	date_added INT,
	claim_id INT,
	ID INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(ID)
);

INSERT INTO patch_history SET patch_number = 21;