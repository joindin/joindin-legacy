-- -------------------------------------------------------------------------- --
-- Joind.in database structure
-- @since Orange (V3)
-- @author Mattijs Hoitink <mattijshoitink@gmail.com>
-- -------------------------------------------------------------------------- --

CREATE TABLE `attendance` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `event_id` INT(10) NOT NULL,
    `user_id` INT(10) NOT NULL,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 
COMMENT 'Holds user attendance to events.';

-- -------------------------------------------------------------------------- --

CREATE TABLE `blog_comments` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `blog_post_id` INT(10) NOT NULL,
    `user_id` INT(10) NOT NULL,
    `author_name` VARCHAR(200) NULL,
    `comment` TEXT NOT NULL,
    `date` INT(15) NOT NULL,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 
COMMENT 'Holds comments to a particular blog post (`blog_posts`).';

-- -------------------------------------------------------------------------- --

CREATE TABLE `blog_posts` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `user_id` INT(10) NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `content` TEXT NOT NULL,
    `date` INT(15) NOT NULL,
    `views` INT(5) NULL,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 
COMMENT 'Holds blog posts made on the site.';

-- -------------------------------------------------------------------------- --

CREATE TABLE `countries` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(50)NOT NULL,
    `name` VARCHAR(200) NOT NULL,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 
COMMENT 'Holds country information for speakers to choose their home country from.';

-- -------------------------------------------------------------------------- --

CREATE TABLE `events` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `timezone_id` INT(10) NOT NULL,
    `title` VARCHAR(120) NOT NULL,
    `stub` VARCHAR(50) NOT NULL,
    `description` TEXT NOT NULL,
    `start` INT(15) NOT NULL,
    `end` INT(15) NOT NULL,
    `icon` VARCHAR(200) NULL,
    `link` TEXT NULL,
    `hashtag` TEXT NULL,
    `latitude` DECIMAL(10) NOT NULL,
    `longtitude` DECIMAL(10) NOT NULL,
    `location` VARCHAR(200) NOT NULL,
	`contact_name` VARCHAR(200) NULL,
	`contact_email` VARCHAR(200) NOT NULL,
    `active` TINYINT(3) NOT NULL,
    `pending` TINYINT(3) NULL,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 
COMMENT 'Stores information about events being held.';

-- -------------------------------------------------------------------------- --

CREATE TABLE `event_comments` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `user_id` INT(10) NULL,
    `event_id` INT(10) NOT NULL,
    `author_name` VARCHAR(200) NULL,
    `comment` TEXT NOT NULL,
    `date` INT(15) NOT NULL,
    `private` TINYINT(3) NULL,
    `active` TINYINT(3) NULL,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 
COMMENT 'Holds the comments for events.';

-- -------------------------------------------------------------------------- --

CREATE TABLE `event_managers` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `user_id` INT(10) NOT NULL,
    `event_id` INT(10) NOT NULL,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 
COMMENT 'Defines the users that can administrate a praticular event.';

-- -------------------------------------------------------------------------- --

CREATE TABLE `languages` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `short` VARCHAR(20) NOT NULL,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 
COMMENT 'Stores information about languages a session is given in.';

-- -------------------------------------------------------------------------- --

CREATE TABLE `messaging_service_providers` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(200) NOT NULL,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 
COMMENT 'Defines the available messaging services for a speaker to choose from, e.g. GTalk, AOL, IRC etc.';

-- -------------------------------------------------------------------------- --

CREATE TABLE `sessions` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `category_id` INT(10) NOT NULL,
    `event_id` INT(10) NOT NULL,
    `talk_id` INT(10) NULL,
    `language_id` INT(10) NOT NULL,
    `speaker_name` VARCHAR(200) NULL,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT NOT NULL,
    `slides_link` VARCHAR(200) NULL,
    `date` INT(15) NOT NULL,
    `active` TINYINT(3) NOT NULL,
    `claim_token` VARCHAR(25) NULL,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 
COMMENT 'Stores the sessions given at an event (`events`).';

-- -------------------------------------------------------------------------- --

CREATE TABLE `session_categories` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(120) NOT NULL,
    `description` VARCHAR(200) NOT NULL,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- -------------------------------------------------------------------------- --

CREATE TABLE `session_claims` (
  `id` tinyint(3) NOT NULL auto_increment,
  `session_id` int(10) NOT NULL,
  `speaker_profile_id` int(10) NOT NULL,
  `talk_id` int(10) default NULL,
  `date` int(15) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- -------------------------------------------------------------------------- --

CREATE TABLE `session_comments` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `session_id` INT(10) NOT NULL,
    `user_id` INT(10) NOT NULL,
    `rating` INT(5) NOT NULL,
    `comment` TEXT NOT NULL,
    `date` INT(15) NOT NULL,
    `private` TINYINT(2) NOT NULL,
    `active` TINYINT(2) NOT NULL,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 
COMMENT 'Stores the comments for a particular session (`sessions`).';

-- -------------------------------------------------------------------------- --

CREATE TABLE `speaker_messaging_services` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `speaker_profile_id` INT(10) NOT NULL,
    `messaging_service_provider_id` INT(10) NOT NULL,
    `account_name` VARCHAR(200) NOT NULL,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 
COMMENT 'Defines the messaging services a speaker uses (`messaging_service_providers`).';

-- -------------------------------------------------------------------------- --

CREATE TABLE `speaker_profiles` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `user_id` INT(10) NOT NULL,
    `country_id` INT(10) NULL,
    `full_name` VARCHAR(240) NOT NULL,
    `contact_email` VARCHAR(240) NOT NULL,
    `website` VARCHAR(240) NULL,
    `blog` VARCHAR(240) NULL,
    `phone` VARCHAR(120) NULL,
    `city` VARCHAR(200) NULL,
    `zip` VARCHAR(50) NULL,
    `street` VARCHAR(200) NULL,
    `job_title` VARCHAR(200) NULL,
    `bio` TEXT NULL,
    `resume` VARCHAR(200) NULL,
    `picture` VARCHAR(200) NULL,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 
COMMENT 'Contains the profiles for speakers.';

-- -------------------------------------------------------------------------- --

CREATE TABLE `speaker_tokens` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `speaker_profile_id` INT(10) NOT NULL,
    `access_token` VARCHAR(50) NOT NULL,
    `description` VARCHAR(200) NULL,
    `created` INT(15) NOT NULL,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 
COMMENT 'Contains the tokens to access speaker data.';

-- -------------------------------------------------------------------------- --

CREATE TABLE `speaker_token_fields` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `speaker_token_id` INT(10) NOT NULL,
    `field_name` VARCHAR(200) NOT NULL,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 
COMMENT 'Defines the fields that are exported for a speaker token (`speaker_tokens`).';

-- -------------------------------------------------------------------------- --

CREATE TABLE `speaker_web_services` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `speaker_profile_id` INT(10) NOT NULL,
    `web_service_provider_id` INT(10) NOT NULL,
    `url` VARCHAR(200) NOT NULL,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 
COMMENT 'Defines the web services a speaker is using.';

-- -------------------------------------------------------------------------- --

CREATE TABLE `talks` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `speaker_profile_id` INT(10) NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT NOT NULL,
    `abstract` TEXT NOT NULL,
    `active` TINYINT(3) NOT NULL,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 
COMMENT 'Stores the data for a talk given by a speaker.';

-- -------------------------------------------------------------------------- --

CREATE TABLE `talk_tokens` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `talk_id` INT(10) NOT NULL,
    `access_token` VARCHAR(50) NOT NULL,
    `description` VARCHAR(200) NULL,
    `created` INT(15) NOT NULL,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 
COMMENT 'Stores the access tokens for talks data.';

-- -------------------------------------------------------------------------- --

CREATE TABLE `users` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(120) NOT NULL,
    `password` VARCHAR(50) NOT NULL,
    `email` VARCHAR(200) NOT NULL,
    `display_name` VARCHAR(200) NULL,
    `last_login` INT(15) NULL,
    `active` TINYINT(3) NOT NULL,
    `admin` TINYINT(3) NULL,
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 
COMMENT 'Stores the users for the application.';

-- -------------------------------------------------------------------------- --

CREATE TABLE `web_service_providers` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(200),
    PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 
COMMENT 'Defines the available web service provides for a speaker to choose from, e.g. Twitter, Digg, LinkedIn etc.';

-- -------------------------------------------------------------------------- --

