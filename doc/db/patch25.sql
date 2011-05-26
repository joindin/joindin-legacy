-- create oauth_access_tokens table
CREATE TABLE oauth_access_tokens (id int primary key auto_increment,
consumer_key varchar(30) NOT NULL,
access_token varchar(16) NOT NULL,
access_token_secret varchar(32) NOT NULL,
user_id int,
created_date timestamp DEFAULT CURRENT_TIMESTAMP,
last_used_date datetime);

INSERT INTO patch_history SET patch_number = 25;

