--
-- Table structure for table `profiles`
--

CREATE TABLE IF NOT EXISTS `profiles` (
  `id` tinyint(10) NOT NULL auto_increment,
  `user_id` tinyint(10) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `contact_email` varchar(200) NOT NULL,
  `phone` varchar(120) NOT NULL,
  `city` varchar(200) NOT NULL,
  `zip` varchar(50) NOT NULL,
  `country` varchar(200) NOT NULL,
  `street` varchar(200) NOT NULL,
  `bio` text NOT NULL,
  `resume` varchar(200) default NULL,
  `picture` varchar(200) default NULL,
  PRIMARY KEY  (`id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `profile_im_accounts`
--

CREATE TABLE IF NOT EXISTS `profile_im_accounts` (
  `id` tinyint(10) NOT NULL auto_increment,
  `profile_id` tinyint(10) NOT NULL,
  `network_name` varchar(200) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `profile_sn_accounts`
--

CREATE TABLE IF NOT EXISTS `profile_sn_accounts` (
  `id` tinyint(10) NOT NULL auto_increment,
  `profile_id` tinyint(10) NOT NULL,
  `service_name` varchar(200) NOT NULL,
  `account_url` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `profile_tokens`
--

CREATE TABLE IF NOT EXISTS `profile_tokens` (
  `id` tinyint(10) NOT NULL auto_increment,
  `profile_id` tinyint(10) NOT NULL,
  `access_token` varchar(200) NOT NULL,
  `description` varchar(200) default NULL,
  `created` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
);
