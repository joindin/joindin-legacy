-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `id` int(10) NOT NULL auto_increment,
  `code` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
);

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `code`, `name`) VALUES
(1, 'AF', 'Afghanistan'),
(2, 'AL', 'Albania'),
(3, 'DZ', 'Algeria'),
(4, 'AS', 'American Samoa'),
(5, 'AD', 'Andorra'),
(6, 'AI', 'Anguilla'),
(7, 'AQ', 'Antarctica'),
(8, 'AG', 'Antigua And Barbuda'),
(9, 'AR', 'Argentina'),
(10, 'AM', 'Armenia'),
(11, 'AW', 'Aruba'),
(12, 'AU', 'Australia'),
(13, 'AT', 'Austria'),
(14, 'AZ', 'Azerbaijan'),
(15, 'BS', 'Bahamas'),
(16, 'BH', 'Bahrain'),
(17, 'BD', 'Bangladesh'),
(18, 'BB', 'Barbados'),
(19, 'BY', 'Belarus'),
(20, 'BE', 'Belgium'),
(21, 'BZ', 'Belize'),
(22, 'BJ', 'Benin'),
(23, 'BM', 'Bermuda'),
(24, 'BT', 'Bhutan'),
(25, 'BO', 'Bolivia'),
(26, 'BA', 'Bosnia and Herzegovina'),
(27, 'BW', 'Botswana'),
(28, 'BV', 'Bouvet Island'),
(29, 'BR', 'Brazil'),
(30, 'IO', 'British Indian Ocean Territory'),
(31, 'BN', 'Brunei Darussalam'),
(32, 'BG', 'Bulgaria'),
(33, 'BF', 'Burkina Faso'),
(34, 'BI', 'Burundi'),
(35, 'KH', 'Cambodia'),
(36, 'CM', 'Cameroon'),
(37, 'CA', 'Canada'),
(38, 'CV', 'Cape Verde'),
(39, 'KY', 'Cayman Islands'),
(40, 'CF', 'Central African Republic'),
(41, 'TD', 'Chad'),
(42, 'CL', 'Chile'),
(43, 'CN', 'China'),
(44, 'CX', 'Christmas Island'),
(45, 'CC', 'Cocos (Keeling) Islands'),
(46, 'CO', 'Colombia'),
(47, 'KM', 'Comoros'),
(48, 'CG', 'Congo'),
(49, 'CD', 'Congo, the Democratic Republic of the'),
(50, 'CK', 'Cook Islands'),
(51, 'CR', 'Costa Rica'),
(52, 'CI', 'Cote d''Ivoire'),
(53, 'HR', 'Croatia'),
(54, 'CY', 'Cyprus'),
(55, 'CZ', 'Czech Republic'),
(56, 'DK', 'Denmark'),
(57, 'DJ', 'Djibouti'),
(58, 'DM', 'Dominica'),
(59, 'DO', 'Dominican Republic'),
(60, 'TP', 'East Timor'),
(61, 'EC', 'Ecuador'),
(62, 'EG', 'Egypt'),
(63, 'SV', 'El Salvador'),
(64, 'GQ', 'Equatorial Guinea'),
(65, 'ER', 'Eritrea'),
(66, 'EE', 'Estonia'),
(67, 'ET', 'Ethiopia'),
(68, 'FK', 'Falkland Islands'),
(69, 'FO', 'Faroe Islands'),
(70, 'FJ', 'Fiji'),
(71, 'FI', 'Finland'),
(72, 'FR', 'France'),
(73, 'GF', 'French Guiana'),
(74, 'PF', 'French Polynesia'),
(75, 'TF', 'French Southern Territories'),
(76, 'GA', 'Gabon'),
(77, 'GM', 'Gambia'),
(78, 'GE', 'Georgia'),
(79, 'DE', 'Germany'),
(80, 'GH', 'Ghana'),
(81, 'GI', 'Gibraltar'),
(82, 'GR', 'Greece'),
(83, 'GL', 'Greenland'),
(84, 'GD', 'Grenada'),
(85, 'GP', 'Guadeloupe'),
(86, 'GU', 'Guam'),
(87, 'GT', 'Guatemala'),
(88, 'GN', 'Guinea'),
(89, 'GW', 'Guinea-Bissau'),
(90, 'GY', 'Guyana'),
(91, 'HT', 'Haiti'),
(92, 'HM', 'Heard and Mc Donald Islands'),
(93, 'HN', 'Honduras'),
(94, 'HK', 'Hong Kong'),
(95, 'HU', 'Hungary'),
(96, 'IS', 'Iceland'),
(97, 'IN', 'India'),
(98, 'ID', 'Indonesia'),
(99, 'IE', 'Ireland'),
(100, 'IL', 'Israel'),
(101, 'IT', 'Italy'),
(102, 'JM', 'Jamaica'),
(103, 'JP', 'Japan'),
(104, 'JO', 'Jordan'),
(105, 'KZ', 'Kazakhstan'),
(106, 'KE', 'Kenya'),
(107, 'KI', 'Kiribati'),
(108, 'KR', 'Korea, Republic of'),
(109, 'KW', 'Kuwait'),
(110, 'KG', 'Kyrgyzstan'),
(111, 'LA', 'Lao People''s Democratic Republic'),
(112, 'LV', 'Latvia'),
(113, 'LB', 'Lebanon'),
(114, 'LS', 'Lesotho'),
(115, 'LR', 'Liberia'),
(116, 'LY', 'Libya'),
(117, 'LI', 'Liechtenstein'),
(118, 'LT', 'Lithuania'),
(119, 'LU', 'Luxembourg'),
(120, 'MO', 'Macau'),
(121, 'MK', 'Macedonia'),
(122, 'MG', 'Madagascar'),
(123, 'MW', 'Malawi'),
(124, 'MY', 'Malaysia'),
(125, 'MV', 'Maldives'),
(126, 'ML', 'Mali'),
(127, 'MT', 'Malta'),
(128, 'MH', 'Marshall Islands'),
(129, 'MQ', 'Martinique'),
(130, 'MR', 'Mauritania'),
(131, 'MU', 'Mauritius'),
(132, 'YT', 'Mayotte'),
(133, 'MX', 'Mexico'),
(134, 'FM', 'Micronesia, Federated States of'),
(135, 'MD', 'Moldova, Republic of'),
(136, 'MC', 'Monaco'),
(137, 'MN', 'Mongolia'),
(138, 'ME', 'Montenegro'),
(139, 'MS', 'Montserrat'),
(140, 'MA', 'Morocco'),
(141, 'MZ', 'Mozambique'),
(142, 'MM', 'Myanmar'),
(143, 'NA', 'Namibia'),
(144, 'NR', 'Nauru'),
(145, 'NP', 'Nepal'),
(146, 'NL', 'Netherlands'),
(147, 'AN', 'Netherlands Antilles'),
(148, 'NC', 'New Caledonia'),
(149, 'NZ', 'New Zealand'),
(150, 'NI', 'Nicaragua'),
(151, 'NE', 'Niger'),
(152, 'NG', 'Nigeria'),
(153, 'NU', 'Niue'),
(154, 'NF', 'Norfolk Island'),
(155, 'MP', 'Northern Mariana Islands'),
(156, 'NO', 'Norway'),
(157, 'OM', 'Oman'),
(158, 'PK', 'Pakistan'),
(159, 'PW', 'Palau'),
(160, 'PA', 'Panama'),
(161, 'PG', 'Papua New Guinea'),
(162, 'PY', 'Paraguay'),
(163, 'PE', 'Peru'),
(164, 'PH', 'Philippines'),
(165, 'PN', 'Pitcairn'),
(166, 'PL', 'Poland'),
(167, 'PT', 'Portugal'),
(168, 'PR', 'Puerto Rico'),
(169, 'QA', 'Qatar'),
(170, 'RE', 'Reunion'),
(171, 'RO', 'Romania'),
(172, 'RU', 'Russian Federation'),
(173, 'RW', 'Rwanda'),
(174, 'KN', 'Saint Kitts and Nevis'),
(175, 'LC', 'Saint Lucia'),
(176, 'VC', 'Saint Vincent and the Grenadines'),
(177, 'WS', 'Samoa (Independent)'),
(178, 'SM', 'San Marino'),
(179, 'ST', 'Sao Tome and Principe'),
(180, 'SA', 'Saudi Arabia'),
(181, 'SN', 'Senegal'),
(182, 'RS', 'Serbia'),
(183, 'CS', 'Serbia and Montenegro'),
(184, 'SC', 'Seychelles'),
(185, 'SL', 'Sierra Leone'),
(186, 'SG', 'Singapore'),
(187, 'SK', 'Slovakia'),
(188, 'SI', 'Slovenia'),
(189, 'SB', 'Solomon Islands'),
(190, 'SO', 'Somalia'),
(191, 'ZA', 'South Africa'),
(192, 'GS', 'South Georgia and the South Sandwich Islands'),
(193, 'ES', 'Spain'),
(194, 'LK', 'Sri Lanka'),
(195, 'SH', 'St. Helena'),
(196, 'PM', 'St. Pierre and Miquelon'),
(197, 'SR', 'Suriname'),
(198, 'SJ', 'Svalbard and Jan Mayen Islands'),
(199, 'SZ', 'Swaziland'),
(200, 'SE', 'Sweden'),
(201, 'CH', 'Switzerland'),
(202, 'TW', 'Taiwan'),
(203, 'TJ', 'Tajikistan'),
(204, 'TZ', 'Tanzania'),
(205, 'TH', 'Thailand'),
(206, 'TG', 'Togo'),
(207, 'TK', 'Tokelau'),
(208, 'TO', 'Tonga'),
(209, 'TT', 'Trinidad and Tobago'),
(210, 'TN', 'Tunisia'),
(211, 'TR', 'Turkey'),
(212, 'TM', 'Turkmenistan'),
(213, 'TC', 'Turks and Caicos Islands'),
(214, 'TV', 'Tuvalu'),
(215, 'UG', 'Uganda'),
(216, 'UA', 'Ukraine'),
(217, 'AE', 'United Arab Emirates'),
(218, 'GB', 'United Kingdom'),
(219, 'US', 'United States'),
(220, 'UM', 'United States Minor Outlying Islands'),
(221, 'UY', 'Uruguay'),
(222, 'UZ', 'Uzbekistan'),
(223, 'VU', 'Vanuatu'),
(224, 'VA', 'Vatican City State (Holy See)'),
(225, 'VE', 'Venezuela'),
(226, 'VN', 'Viet Nam'),
(227, 'VG', 'Virgin Islands (British)'),
(228, 'VI', 'Virgin Islands (U.S.)'),
(229, 'WF', 'Wallis and Futuna Islands'),
(230, 'EH', 'Western Sahara'),
(231, 'YE', 'Yemen'),
(232, 'ZM', 'Zambia'),
(233, 'ZW', 'Zimbabwe');

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

DROP TABLE IF EXISTS `profiles`;
CREATE TABLE `profiles` (
  `id` int(10) NOT NULL auto_increment,
  `user_id` int(10) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `contact_email` varchar(200) NOT NULL,
  `website` varchar(255) default NULL,
  `blog` varchar(255) default NULL,
  `phone` varchar(120) NOT NULL,
  `city` varchar(200) NOT NULL,
  `zip` varchar(50) NOT NULL,
  `country_id` int(10) default NULL,
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

DROP TABLE IF EXISTS `profile_im_accounts`;
CREATE TABLE `profile_im_accounts` (
  `id` int(10) NOT NULL auto_increment,
  `profile_id` int(10) NOT NULL,
  `im_network_id` int(10) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `profile_im_account_networks`
--

DROP TABLE IF EXISTS `profile_im_account_networks`;
CREATE TABLE `profile_im_account_networks` (
  `id` tinyint(10) NOT NULL auto_increment,
  `name` varchar(200) NOT NULL,
  PRIMARY KEY  (`id`)
);

--
-- Dumping data for table `profile_im_account_networks`
--

INSERT INTO `profile_im_account_networks` (`id`, `name`) VALUES
(1, 'AIM'),
(2, 'Google Talk'),
(3, 'ICQ'),
(4, 'IRC'),
(5, 'MSN'),
(6, 'MySpaceIM'),
(7, 'XMPP'),
(8, 'Facebook Chat'),
(9, 'Skype');

-- --------------------------------------------------------

--
-- Table structure for table `profile_tokens`
--

DROP TABLE IF EXISTS `profile_tokens`;
CREATE TABLE `profile_tokens` (
  `id` int(10) NOT NULL auto_increment,
  `profile_id` int(10) NOT NULL,
  `access_token` varchar(200) NOT NULL,
  `description` varchar(200) default NULL,
  `created` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `profile_token_fields`
--

DROP TABLE IF EXISTS `profile_token_fields`;
CREATE TABLE `profile_token_fields` (
  `id` int(10) NOT NULL auto_increment,
  `profile_token_id` int(10) NOT NULL,
  `field_name` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
);


-- --------------------------------------------------------

--
-- Table structure for table `profile_web_addresses`
--
DROP TABLE IF EXISTS `profile_sn_accounts`;
DROP TABLE IF EXISTS `profile_web_addresses`;
CREATE TABLE `profile_web_addresses` (
  `id` int(10) NOT NULL auto_increment,
  `profile_id` int(10) NOT NULL,
  `web_type_id` tinyint(10) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `profile_web_address_types`
--

DROP TABLE IF EXISTS `profile_web_address_types`;
CREATE TABLE `profile_web_address_types` (
  `id` tinyint(10) NOT NULL auto_increment,
  `name` varchar(200) NOT NULL,
  PRIMARY KEY  (`id`)
);

--
-- Dumping data for table `profile_web_address_types`
--

INSERT INTO `profile_web_address_types` (`id`, `name`) VALUES
(1, 'Flickr'),
(2, 'Picasa Web Albums'),
(4, 'delicious'),
(5, 'StumbleUpon'),
(6, 'Digg'),
(7, 'Google Reader'),
(8, 'Reddit'),
(9, 'Facebook'),
(10, 'identi.ca'),
(11, 'Twitter'),
(15, 'FriendFeed'),
(13, 'LinkedIn'),
(14, 'SlideShare');
