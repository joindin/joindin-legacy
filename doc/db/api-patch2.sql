-- create oauth_request_tokens table
CREATE TABLE oauth_request_tokens (id int primary key auto_increment,
consumer_key varchar(30) NOT NULL,
request_token varchar(8) NOT NULL,
request_token_secret varchar(32) NOT NULL,
authorised_user_id int,
created_date timestamp DEFAULT CURRENT_TIMESTAMP);
