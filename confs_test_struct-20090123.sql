
CREATE TABLE blog_cats (
  name varchar(100) default NULL,
  ID int(11) NOT NULL auto_increment,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `blog_comments`
--

CREATE TABLE blog_comments (
  title varchar(100) default NULL,
  author_id int(11) default NULL,
  content text,
  ID int(11) NOT NULL auto_increment,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `blog_post_cat`
--

CREATE TABLE blog_post_cat (
  post_id int(11) default NULL,
  cat_id int(11) default NULL,
  ID int(11) NOT NULL auto_increment,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `blog_posts`
--

CREATE TABLE blog_posts (
  title varchar(250) default NULL,
  content text,
  date_posted int(11) default NULL,
  author_id int(11) default NULL,
  ID int(11) NOT NULL auto_increment,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `categories`
--

CREATE TABLE categories (
  cat_title varchar(200) default NULL,
  cat_desc text,
  ID int(11) NOT NULL auto_increment,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `event_comments`
--

CREATE TABLE event_comments (
  event_id int(11) default NULL,
  `comment` text,
  date_made int(11) default NULL,
  user_id int(11) default NULL,
  active int(11) default NULL,
  ID int(11) NOT NULL auto_increment,
  cname varchar(100) default NULL,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  event_name varchar(200) default NULL,
  event_start int(11) default NULL,
  event_end int(11) default NULL,
  event_lat decimal(10,2) default NULL,
  event_long decimal(10,2) default NULL,
  ID int(11) NOT NULL auto_increment,
  event_loc text,
  event_desc text,
  active int(11) default NULL,
  event_stub varchar(30) default NULL,
  event_tz int(11) default NULL,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `lang`
--

CREATE TABLE lang (
  lang_name varchar(200) default NULL,
  lang_abbr varchar(20) default NULL,
  ID int(11) NOT NULL auto_increment,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `talk_cat`
--

CREATE TABLE talk_cat (
  talk_id int(11) default NULL,
  cat_id int(11) default NULL,
  ID int(11) NOT NULL auto_increment,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `talk_comments`
--

CREATE TABLE talk_comments (
  talk_id int(11) default NULL,
  rating int(11) default NULL,
  `comment` text,
  date_made int(11) default NULL,
  ID int(11) NOT NULL auto_increment,
  private int(11) default NULL,
  active int(11) default NULL,
  user_id int(11) default NULL,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `talks`
--

CREATE TABLE talks (
  talk_title tinytext,
  speaker tinytext,
  slides_link tinytext,
  date_given int(11) default NULL,
  event_id int(11) default NULL,
  ID int(11) NOT NULL auto_increment,
  talk_desc text,
  active int(11) default NULL,
  owner_id int(11) default NULL,
  lang int(11) default NULL,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `tz`
--

CREATE TABLE tz (
  `offset` int(11) default NULL,
  cont varchar(50) default NULL,
  area varchar(70) default NULL,
  ID int(11) NOT NULL auto_increment,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  username varchar(100) default NULL,
  `password` varchar(32) default NULL,
  email varchar(200) default NULL,
  last_login int(11) default NULL,
  ID int(11) NOT NULL auto_increment,
  admin int(11) default NULL,
  full_name varchar(200) default NULL,
  active int(11) default NULL,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `user_admin`
--

CREATE TABLE user_admin (
  uid int(11) default NULL,
  rid int(11) default NULL,
  rtype varchar(20) default NULL,
  ID int(11) NOT NULL auto_increment,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `user_attend`
--

CREATE TABLE user_attend (
  uid int(11) default NULL,
  eid int(11) default NULL,
  ID int(11) NOT NULL auto_increment,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

