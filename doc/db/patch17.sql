# cleaning up errors with patches 15 and 16 which were missing patch history info

# We need a stored procedure for this since MySQL cannot drop column if exists (yet).
DROP PROCEDURE IF EXISTS schema_change;

DELIMITER //

CREATE PROCEDURE schema_change() BEGIN
  IF EXISTS (SELECT * FROM information_schema.columns WHERE table_name = 'talk_speaker' AND column_name = 'rcode') THEN
    ALTER TABLE talk_speaker DROP COLUMN `rcode`;
  END IF;
END//

DELIMITER ;
CALL schema_change();

DROP PROCEDURE IF EXISTS schema_change;


INSERT INTO patch_history SET patch_number = 17;
