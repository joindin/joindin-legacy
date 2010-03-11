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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM AUTO_INCREMENT=695 DEFAULT CHARSET=latin1;

--
-- Table structure for table `blog_post_cat`
--

DROP TABLE IF EXISTS `blog_post_cat`;
CREATE TABLE `blog_post_cat` (
  `post_id` int(11) default NULL,
  `cat_id` int(11) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `cat_title` varchar(200) default NULL,
  `cat_desc` text,
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `name` varchar(50) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Data for table `countries`

INSERT INTO `countries` (`name`) VALUES ('AFGHANISTAN'), ('ÅLAND ISLANDS'), ('ALBANIA'), ('ALGERIA'), ('AMERICAN SAMOA'), ('ANDORRA'), ('ANGOLA'), ('ANGUILLA'), ('ANTARCTICA'), ('ANTIGUA AND BARBUDA'), ('ARGENTINA'), ('ARMENIA'), ('ARUBA'), ('AUSTRALIA'), ('AUSTRIA'), ('AZERBAIJAN'), ('BAHAMAS'), ('BAHRAIN'), ('BANGLADESH'), ('BARBADOS'), ('BELARUS'), ('BELGIUM'), ('BELIZE'), ('BENIN'), ('BERMUDA'), ('BHUTAN'), ('BOLIVIA, PLURINATIONAL STATE OF'), ('BOSNIA AND HERZEGOVINA'), ('BOTSWANA'), ('BOUVET ISLAND'), ('BRAZIL'), ('BRITISH INDIAN OCEAN TERRITORY'), ('BRUNEI DARUSSALAM'), ('BULGARIA'), ('BURKINA FASO'), ('BURUNDI'), ('CAMBODIA'), ('CAMEROON'), ('CANADA'), ('CAPE VERDE'), ('CAYMAN ISLANDS'), ('CENTRAL AFRICAN REPUBLIC'), ('CHAD'), ('CHILE'), ('CHINA'), ('CHRISTMAS ISLAND'), ('COCOS (KEELING) ISLANDS'), ('COLOMBIA'), ('COMOROS'), ('CONGO'), ('CONGO, THE DEMOCRATIC REPUBLIC OF THE'), ('COOK ISLANDS'), ('COSTA RICA'), ('CÔTE D\'IVOIRE'), ('CROATIA'), ('CUBA'), ('CYPRUS'), ('CZECH REPUBLIC'), ('DENMARK'), ('DJIBOUTI'), ('DOMINICA'), ('DOMINICAN REPUBLIC'), ('ECUADOR'), ('EGYPT'), ('EL SALVADOR'), ('EQUATORIAL GUINEA'), ('ERITREA'), ('ESTONIA'), ('ETHIOPIA'), ('FALKLAND ISLANDS (MALVINAS)'), ('FAROE ISLANDS'), ('FIJI'), ('FINLAND'), ('FRANCE'), ('FRENCH GUIANA'), ('FRENCH POLYNESIA'), ('FRENCH SOUTHERN TERRITORIES'), ('GABON'), ('GAMBIA'), ('GEORGIA'), ('GERMANY'), ('GHANA'), ('GIBRALTAR'), ('GREECE'), ('GREENLAND'), ('GRENADA'), ('GUADELOUPE'), ('GUAM'), ('GUATEMALA'), ('GUERNSEY'), ('GUINEA'), ('GUINEA-BISSAU'), ('GUYANA'), ('HAITI'), ('HEARD ISLAND AND MCDONALD ISLANDS'), ('HOLY SEE (VATICAN CITY STATE)'), ('HONDURAS'), ('HONG KONG'), ('HUNGARY'), ('ICELAND'), ('INDIA'), ('INDONESIA'), ('IRAN, ISLAMIC REPUBLIC OF'), ('IRAQ'), ('IRELAND'), ('ISLE OF MAN'), ('ISRAEL'), ('ITALY'), ('JAMAICA'), ('JAPAN'), ('JERSEY'), ('JORDAN'), ('KAZAKHSTAN'), ('KENYA'), ('KIRIBATI'), ('KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF'), ('KOREA, REPUBLIC OF'), ('KUWAIT'), ('KYRGYZSTAN'), ('LAO PEOPLE\'S DEMOCRATIC REPUBLIC'), ('LATVIA'), ('LEBANON'), ('LESOTHO'), ('LIBERIA'), ('LIBYAN ARAB JAMAHIRIYA'), ('LIECHTENSTEIN'), ('LITHUANIA'), ('LUXEMBOURG'), ('MACAO'), ('MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF'), ('MADAGASCAR'), ('MALAWI'), ('MALAYSIA'), ('MALDIVES'), ('MALI'), ('MALTA'), ('MARSHALL ISLANDS'), ('MARTINIQUE'), ('MAURITANIA'), ('MAURITIUS'), ('MAYOTTE'), ('MEXICO'), ('MICRONESIA, FEDERATED STATES OF'), ('MOLDOVA, REPUBLIC OF'), ('MONACO'), ('MONGOLIA'), ('MONTENEGRO'), ('MONTSERRAT'), ('MOROCCO'), ('MOZAMBIQUE'), ('MYANMAR'), ('NAMIBIA'), ('NAURU'), ('NEPAL'), ('NETHERLANDS'), ('NETHERLANDS ANTILLES'), ('NEW CALEDONIA'), ('NEW ZEALAND'), ('NICARAGUA'), ('NIGER'), ('NIGERIA'), ('NIUE'), ('NORFOLK ISLAND'), ('NORTHERN MARIANA ISLANDS'), ('NORWAY'), ('OMAN'), ('PAKISTAN'), ('PALAU'), ('PALESTINIAN TERRITORY, OCCUPIED'), ('PANAMA'), ('PAPUA NEW GUINEA'), ('PARAGUAY'), ('PERU'), ('PHILIPPINES'), ('PITCAIRN'), ('POLAND'), ('PORTUGAL'), ('PUERTO RICO'), ('QATAR'), ('RÉUNION'), ('ROMANIA'), ('RUSSIAN FEDERATION'), ('RWANDA'), ('SAINT BARTHÉLEMY'), ('SAINT HELENA, ASCENSION AND TRISTAN DA CUNHA'), ('TTS AND NEVIS'), ('SAINT LUCIA'), ('SAINT MARTIN'), ('SAINT PIERRE AND MIQUELON'), ('SAINT VINCENT AND THE GRENADINES'), ('SAMOA'), ('SAN MARINO'), ('SAO TOME AND PRINCIPE'), ('SAUDI ARABIA'), ('SENEGAL'), ('SERBIA'), ('SEYCHELLES'), ('SIERRA LEONE'), ('SINGAPORE'), ('SLOVAKIA'), ('SLOVENIA'), ('SOLOMON ISLANDS'), ('SOMALIA'), ('SOUTH AFRICA'), ('SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS'), ('SPAIN'), ('SRI LANKA'), ('SUDAN'), ('SURINAME'), ('SVALBARD AND JAN MAYEN'), ('SWAZILAND'), ('SWEDEN'), ('SWITZERLAND'), ('SYRIAN ARAB REPUBLIC'), ('TAIWAN, PROVINCE OF CHINA'), ('TAJIKISTAN'), ('TANZANIA, UNITED REPUBLIC OF'), ('THAILAND'), ('TIMOR-LESTE'), ('TOGO'), ('TOKELAU'), ('TONGA'), ('TRINIDAD AND TOBAGO'), ('TUNISIA'), ('TURKEY'), ('TURKMENISTAN'), ('TURKS AND CAICOS ISLANDS'), ('TUVALU'), ('UGANDA'), ('UKRAINE'), ('UNITED ARAB EMIRATES'), ('UNITED KINGDOM'), ('UNITED STATES'), ('UNITED STATES MINOR OUTLYING ISLANDS'), ('URUGUAY'), ('UZBEKISTAN'), ('VANUATU'), ('VATICAN CITY STATE'), ('VENEZUELA, BOLIVARIAN REPUBLIC OF'), ('VIET NAM'), ('VIRGIN ISLANDS, BRITISH'), ('VIRGIN ISLANDS, U.S.'), ('WALLIS AND FUTUNA'), ('WESTERN SAHARA'), ('YEMEN'), ('ZAMBIA'), ('ZIMBABWE');

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM AUTO_INCREMENT=240 DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

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
  `event_tz` int(11) default NULL,
  `event_icon` varchar(30) default NULL,
  `pending` int(11) default NULL,
  `event_hashtag` varchar(100) default NULL,
  `event_href` varchar(600) default NULL,
  `event_cfp_start` int(11) default NULL,
  `event_cfp_end` int(11) default NULL,
  `event_voting` varchar(1) default NULL,
  `private` varchar(1) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=136 DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;

--
-- Table structure for table `lang`
--

DROP TABLE IF EXISTS `lang`;
CREATE TABLE `lang` (
  `lang_name` varchar(200) default NULL,
  `lang_abbr` varchar(20) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM AUTO_INCREMENT=1258 DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM AUTO_INCREMENT=2516 DEFAULT CHARSET=latin1;

--
-- Table structure for table `talk_track`
--

DROP TABLE IF EXISTS `talk_track`;
CREATE TABLE `talk_track` (
  `talk_id` int(11) default NULL,
  `track_id` int(11) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM AUTO_INCREMENT=1269 DEFAULT CHARSET=latin1;

--
-- Table structure for table `tz`
--

DROP TABLE IF EXISTS `tz`;
CREATE TABLE `tz` (
  `offset` int(11) default NULL,
  `cont` varchar(50) default NULL,
  `area` varchar(70) default NULL,
  `ID` int(11) NOT NULL auto_increment,
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
) ENGINE=MyISAM AUTO_INCREMENT=1674 DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM AUTO_INCREMENT=832 DEFAULT CHARSET=latin1;

--
-- Table structure for table `user_attend`
--

DROP TABLE IF EXISTS `user_attend`;
CREATE TABLE `user_attend` (
  `uid` int(11) default NULL,
  `eid` int(11) default NULL,
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1153 DEFAULT CHARSET=latin1;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-02-27 19:38:49
