-- oauth2 doesn't need request tokens, we just store access tokens 
-- with no verifier

DROP TABLE IF EXISTS oauth_request_tokens;

INSERT INTO patch_history SET patch_number = 35;
