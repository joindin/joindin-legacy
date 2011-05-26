-- create oauth_consumers table
CREATE TABLE oauth_consumers (id int primary key auto_increment,
consumer_key varchar(30) NOT NULL,
consumer_secret varchar(10) NOT NULL,
created_date timestamp DEFAULT CURRENT_TIMESTAMP);

INSERT INTO patch_history SET patch_number = 23;

