-- adding the callback_url column, for a simple check on implicit oauth2

alter table oauth_consumers add column callback_url varchar(500);

INSERT INTO patch_history SET patch_number = 36;
