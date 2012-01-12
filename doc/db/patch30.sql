-- Drop the function if it exists
DROP FUNCTION IF EXISTS get_talk_rating;


-- Create get_talk_rating function that takes into account ratings that should not be added (speaker ratings)
DELIMITER //

CREATE FUNCTION get_talk_rating(talk_id INT) RETURNS int
	READS SQL DATA
BEGIN
	DECLARE rating_out INT;
	DECLARE EXIT HANDLER FOR NOT FOUND RETURN NULL;

	SELECT ROUND(AVG(rating)) INTO rating_out
	FROM talk_comments tc
	WHERE
		tc.talk_id = talk_id AND
		tc.user_id NOT IN
		(
			SELECT ts.speaker_id FROM talk_speaker ts WHERE ts.talk_id = talk_id
		);

	RETURN rating_out;
END//


-- Increase patch count
INSERT INTO patch_history SET patch_number = 30;