#Add rating field to event_comments table
ALTER TABLE  `event_comments` ADD  `rating` INT NULL DEFAULT NULL AFTER  `event_id`;

#Add new function to calculate the average rating for an event
DROP FUNCTION IF EXISTS `get_event_rating`;

DELIMITER//

CREATE FUNCTION `get_event_rating`(event_id INT) RETURNS int(11)
  READS SQL DATA
BEGIN
  DECLARE rating_out INT;
  DECLARE EXIT HANDLER FOR NOT FOUND RETURN NULL;
  SELECT IFNULL(ROUND(AVG(rating)), 0) INTO rating_out
  FROM event_comments ec
  WHERE
    ec.event_id = event_id AND
    ec.rating != 0 AND
    ec.user_id NOT IN
    (
      SELECT ua.uid FROM user_admin ua WHERE ua.rid = event_id AND ua.rtype = 'event' and ua.rcode != 'pending'
      UNION
      SELECT 0
    );
  RETURN rating_out;
END//

DELIMITER ;

#Add to patch to patch_history
INSERT INTO patch_history SET patch_number = 34;
