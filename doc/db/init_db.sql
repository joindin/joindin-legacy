-- MySQL dump 10.11
--
-- Host: localhost    Database: confs
-- ------------------------------------------------------
-- Server version	5.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `blog_cats`
--

DROP TABLE IF EXISTS `blog_cats`;
CREATE TABLE `blog_cats` (
  `name` varchar(100) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `blog_comments`
--

DROP TABLE IF EXISTS `blog_comments`;
CREATE TABLE `blog_comments` (
  `title` varchar(100) default NULL,
  `author_id` int(11) default NULL,
  `content` text,
  `ID` int(11) NOT NULL auto_increment,
  `blog_post_id` int(11) default NULL,
  `author_name` varchar(100) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `blog_post_cat`
--

DROP TABLE IF EXISTS `blog_post_cat`;
CREATE TABLE `blog_post_cat` (
  `post_id` int(11) default NULL,
  `cat_id` int(11) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `blog_posts`
--

DROP TABLE IF EXISTS `blog_posts`;
CREATE TABLE `blog_posts` (
  `title` varchar(250) default NULL,
  `content` text,
  `date_posted` int(11) default NULL,
  `author_id` int(11) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  `views` int(11) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `cat_title` varchar(200) default NULL,
  `cat_desc` text,
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `name` varchar(50) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `event_blog_posts`
--

DROP TABLE IF EXISTS `event_blog_posts`;
CREATE TABLE `event_blog_posts` (
  `title` varchar(300) default NULL,
  `content` text,
  `date_posted` int(11) default NULL,
  `author_id` int(11) default NULL,
  `event_id` int(11) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `event_comments`
--

DROP TABLE IF EXISTS `event_comments`;
CREATE TABLE `event_comments` (
  `event_id` int(11) default NULL,
  `comment` text,
  `date_made` int(11) default NULL,
  `user_id` int(11) default NULL,
  `active` int(11) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  `cname` varchar(100) default NULL,
  `comment_type` varchar(100) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `event_track`
--

DROP TABLE IF EXISTS `event_track`;
CREATE TABLE `event_track` (
  `event_id` int(11) default NULL,
  `track_name` varchar(300) default NULL,
  `track_desc` text,
  `ID` int(11) NOT NULL auto_increment,
  `track_color` varchar(6) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `event_name` varchar(200) default NULL,
  `event_start` int(11) default NULL,
  `event_end` int(11) default NULL,
  `event_lat` decimal(10,2) default NULL,
  `event_long` decimal(10,2) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  `event_loc` text,
  `event_desc` text,
  `active` int(11) default NULL,
  `event_stub` varchar(30) default NULL,
  `event_icon` varchar(30) default NULL,
  `pending` int(11) default NULL,
  `event_hashtag` varchar(100) default NULL,
  `event_href` varchar(600) default NULL,
  `event_cfp_start` int(11) default NULL,
  `event_cfp_end` int(11) default NULL,
  `event_voting` varchar(1) default NULL,
  `private` varchar(1) default NULL,
  `event_tz_cont` varchar(30) default NULL,
  `event_tz_place` varchar(70) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `invite_list`
--

DROP TABLE IF EXISTS `invite_list`;
CREATE TABLE `invite_list` (
  `eid` int(11) default NULL,
  `uid` int(11) default NULL,
  `accepted` varchar(1) default NULL,
  `date_added` int(11) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `lang`
--

DROP TABLE IF EXISTS `lang`;
CREATE TABLE `lang` (
  `lang_name` varchar(200) default NULL,
  `lang_abbr` varchar(20) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `speaker_profile`
--

DROP TABLE IF EXISTS `speaker_profile`;
CREATE TABLE `speaker_profile` (
  `user_id` int(11) default NULL,
  `country_id` int(11) default NULL,
  `full_name` varchar(200) default NULL,
  `contact_email` varchar(200) default NULL,
  `website` varchar(200) default NULL,
  `blog` varchar(200) default NULL,
  `phone` varchar(120) default NULL,
  `city` varchar(200) default NULL,
  `zip` varchar(50) default NULL,
  `street` varchar(200) default NULL,
  `job_title` varchar(200) default NULL,
  `bio` text,
  `resume` varchar(200) default NULL,
  `picture` varchar(200) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `speaker_token_fields`
--

DROP TABLE IF EXISTS `speaker_token_fields`;
CREATE TABLE `speaker_token_fields` (
  `speaker_token_id` int(11) default NULL,
  `field_name` varchar(200) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `speaker_tokens`
--

DROP TABLE IF EXISTS `speaker_tokens`;
CREATE TABLE `speaker_tokens` (
  `speaker_profile_id` int(11) default NULL,
  `access_token` varchar(50) default NULL,
  `description` varchar(200) default NULL,
  `created` int(11) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `talk_cat`
--

DROP TABLE IF EXISTS `talk_cat`;
CREATE TABLE `talk_cat` (
  `talk_id` int(11) default NULL,
  `cat_id` int(11) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `talk_comments`
--

DROP TABLE IF EXISTS `talk_comments`;
CREATE TABLE `talk_comments` (
  `talk_id` int(11) default NULL,
  `rating` int(11) default NULL,
  `comment` text,
  `date_made` int(11) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  `private` int(11) default NULL,
  `active` int(11) default NULL,
  `user_id` int(11) default NULL,
  `comment_type` varchar(10) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `talk_track`
--

DROP TABLE IF EXISTS `talk_track`;
CREATE TABLE `talk_track` (
  `talk_id` int(11) default NULL,
  `track_id` int(11) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `talks`
--

DROP TABLE IF EXISTS `talks`;
CREATE TABLE `talks` (
  `talk_title` tinytext,
  `speaker` tinytext,
  `slides_link` tinytext,
  `date_given` int(11) default NULL,
  `event_id` int(11) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  `talk_desc` text,
  `active` int(11) default NULL,
  `owner_id` int(11) default NULL,
  `lang` int(11) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `username` varchar(100) default NULL,
  `password` varchar(32) default NULL,
  `email` varchar(200) default NULL,
  `last_login` int(11) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  `admin` int(11) default NULL,
  `full_name` varchar(200) default NULL,
  `active` int(11) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `user_admin`
--

DROP TABLE IF EXISTS `user_admin`;
CREATE TABLE `user_admin` (
  `uid` int(11) default NULL,
  `rid` int(11) default NULL,
  `rtype` varchar(20) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  `rcode` varchar(40) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `talk_speaker`
--

DROP TABLE IF EXISTS `talk_speaker`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `talk_speaker` (
  `talk_id` int(11) default NULL,
  `speaker_name` varchar(200) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  `speaker_id` int(11) default NULL,
  `status` varchar(10) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=2778 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `user_attend`
--

DROP TABLE IF EXISTS `user_attend`;
CREATE TABLE `user_attend` (
  `uid` int(11) default NULL,
  `eid` int(11) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-03-16  2:40:29
