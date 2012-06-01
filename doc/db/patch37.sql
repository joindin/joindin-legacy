-- add table to track users attending talks

CREATE TABLE `user_attend_talk` (
  `uid` int(11) DEFAULT NULL,
  `tid` int(11) DEFAULT NULL,
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`ID`),
  KEY `idx_talk` (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8