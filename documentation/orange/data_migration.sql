-- -------------------------------------------------------------------------- --
-- Data migration for version orange
-- 
-- @since orange
-- @author Mattijs Hoitink <mattijshoitink@gmail.com>
-- -------------------------------------------------------------------------- --

-- -------------------------------------------------------------------------- --
-- Migrate User data
-- @todo

-- -------------------------------------------------------------------------- --
-- Migrate Events data
INSERT INTO `joindin_rewrite`.`events` (`id`, `timezone_id`, `title`, `description`, `stub`, `start`, `end`, `latitude`, `longtitude`, `location`, `active`, `pending`, `icon`, `hashtag`, `link`)
SELECT `ID`, `event_tz`, `event_name`, `event_desc`, `event_stub`, `event_start`, `event_end`, `event_lat`, `event_long`, `event_loc`, `active`, `pending`, `event_icon`, `event_hashtag`, `event_href` FROM `joindin`.`events`;

-- -------------------------------------------------------------------------- --
-- Migrate Event Comments
INSERT INTO `joindin_rewrite`.`event_comments` (`id`, `author_name`, `user_id`, `event_id`, `comment`, `date`, `active`) 
SELECT `ID`, `cname`, `user_id`, `event_id`, `comment`, `date_made`, `active` FROM `joindin`.`event_comments`;

-- -------------------------------------------------------------------------- --
-- Migrate Category data
INSERT INTO `joindin_rewrite`.`categories` (`id`, `title`, `description`) 
SELECT `ID`, `cat_title`, `cat_desc` FROM `joindin`.`categories`;

-- -------------------------------------------------------------------------- --
-- Migrate Language data
INSERT INTO `joindin_rewrite`.`languages` (`id`, `name`, `short`) 
SELECT `ID`, `lang_name`, `lang_abbr` FROM `joindin`.`lang`;

-- -------------------------------------------------------------------------- --
-- Migrate Sessions data
INSERT INTO `joindin_rewrite`.`sessions` (`id`, `event_id`, `language_id`, `speaker_name`, `title`, `description`, `date`, `active`) 
SELECT `ID`, `event_id`, `lang`, `speaker`, `talk_title`, `talk_desc`, `date_given`, `active` FROM `joindin`.`talks`;

-- -------------------------------------------------------------------------- --
-- Migrate Session Comments
INSERT INTO `joindin_rewrite`.`session_comments` (`id`, `session_id`, `user_id`, `rating`, `comment`, `date`, `private`, `active`) 
SELECT `ID`, `talk_id`, `user_id`, `rating`, `comment`, `date_made`, `private`, `active` FROM `joindin`.`talk_comments`;
-- Update the `author_name` field for comments without a `user_id` set to '0'
UPDATE `joindin_rewrite`.`session_comments` SET `author_name` = 'anonymous' WHERE `user_id` = 0;

-- -------------------------------------------------------------------------- --
-- Migrate Blog Posts
INSERT INTO `joindin_rewrite`.`blog_posts` (`id`, `user_id`, `title`, `content`, `date`, `views`) 
SELECT `ID`, `author_id`, `title`, `content`, `date_posted`, `views` FROM `joindin`.`blog_posts`;

-- -------------------------------------------------------------------------- --
-- Migrate Blog Comments
INSERT INTO `joindin_rewrite`.`blog_comments` (`id`, `blog_post_id`, `user_id`, `author_name`, `comment`, `date`) 
SELECT `ID`, `blog_post_id`, `author_id`, `author_name`, `content`, `date_posted` FROM `joindin`.`blog_comments`;

-- -------------------------------------------------------------------------- --
-- Migrate Users
INSERT INTO `joindin_rewrite`.`users` (`id`, `username`, `password`, `email`, `display_name`, `last_login`, `active`, `admin`) 
SELECT `ID`, `username`, `password`, `email`, `full_name`, `last_login`, `active`, `admin` FROM `joindin`.`user`;


