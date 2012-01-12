-- Drop the function if it exists
DROP FUNCTION IF EXISTS get_talk_rating;


-- Create get_talk_rating function that takes into account ratings that should not be added (speaker ratings)
-- + IFNULL() fix for making sure this works on non-claimed talks (jthijssen)
DELIMITER //

CREATE FUNCTION get_talk_rating(talk_id INT) RETURNS int
	READS SQL DATA
BEGIN
	DECLARE rating_out INT;
	DECLARE EXIT HANDLER FOR NOT FOUND RETURN NULL;

	SELECT IFNULL(ROUND(AVG(rating)), 0) INTO rating_out
	FROM talk_comments tc
	WHERE
		tc.talk_id = talk_id AND
		tc.user_id NOT IN
		(
			SELECT IFNULL(ts.speaker_id,0) FROM talk_speaker ts WHERE ts.talk_id = talk_id
			UNION
			SELECT 0
		);

	RETURN rating_out;
END//


-- Increase patch count
INSERT INTO patch_history SET patch_number = 31;
