# phpMyAdmin SQL Dump
# version 2.5.4
# http://www.phpmyadmin.net
#
# Host: localhost
# Generation Time: Nov 24, 2004 at 10:26 
# Server version: 4.0.18
# PHP Version: 4.3.8
# 
# Database : `wifiadmin`
# 

# --------------------------------------------------------
# ALWAYS UPDATE VERSION NUMBERS

#
# Table structure for table `privileges`
#

CREATE TABLE `privileges` (
  `username` varchar(10) NOT NULL default '',
  `view_status` varchar(10) NOT NULL default 'false',
  `view_status_ext` varchar(10) NOT NULL default 'false',
  `view_macs` varchar(10) NOT NULL default 'false',
  `ban_users` varchar(10) NOT NULL default 'false',
  `access_ifs` varchar(10) NOT NULL default 'false',
  `edit_users` varchar(10) NOT NULL default 'false',
  `edit_privileges` varchar(10) NOT NULL default 'false',
  `manage_routers`  varchar(10) NOT NULL default 'false',
  PRIMARY KEY  (`username`)
) ENGINE=MyISAM;

#
# Dumping data for table `privileges`
#

INSERT INTO `privileges` VALUES ('admin', 'true', 'true', 'true', 'true', 'true', 'true', 'true','true');
INSERT INTO `privileges` VALUES ('guest', 'true', 'false', 'false', 'false', 'false', 'false', 'false','false');

# --------------------------------------------------------

#
# Table structure for table `user`
#

CREATE TABLE `user` (
  `username` varchar(15) NOT NULL default '',
  `password` varchar(60) NOT NULL default '',
  `email` varchar(30) NOT NULL default '',
  `mac` varchar(100) NOT NULL default '',
  `ip` varchar(100) NOT NULL default '',
  `owns_subnet` varchar(100) NOT NULL default '',
  `services` text NOT NULL,
  `phone1` varchar(100) NOT NULL default '',
  `phone2` varchar(100) NOT NULL default '',
  `antenna` varchar(100) NOT NULL default '',
  `nodedb_id` varchar(100) NOT NULL default '',
  `comment` varchar(100) NOT NULL default '',
  `firstname` varchar(40) NOT NULL default '',
  `lastname` varchar(100) NOT NULL default '',
  `password_string` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`username`)
) ENGINE=MyISAM;

#
# Dumping data for table `user`
#

INSERT INTO `user` VALUES ('admin', 'c178d9bb85c3e412dbc478d76320c2be', 'root@localhost', 'not specific', 'not specific', '', '24h service', '', '', '', '', '', '', '', 'wifiadmin');
INSERT INTO `user` VALUES ('guest', '084e0343a0486ff05530df6c705c8bb4', 'no email', 'not specific', 'not specific', '', '', '', '', '', '', '', 'Guest', 'Guestopoulos', 'guest');

# --------------------------------------------------------

#
# Table structure for table `user_tokens`
#

CREATE TABLE `user_tokens` (
  `username` varchar(40) NOT NULL default '',
  `status` varchar(40) NOT NULL default '',
  `token` varchar(40) NOT NULL default ''
) ENGINE=MyISAM;

#
# Dumping data for table `user_tokens`
#

INSERT INTO `user_tokens` VALUES ('admin', 'enabled', '');
INSERT INTO `user_tokens` VALUES ('guest', 'enabled', '');


CREATE TABLE `user_options` (
	`username` varchar(40) NOT NULL default '',
	`option` varchar(40) NOT NULL default '',
	`value` varchar(380) NOT NULL default '',
	PRIMARY KEY (`username`,`option`)
) ENGINE=MyISAM;

CREATE TABLE `global_options` (
	`option` varchar(40) NOT NULL default '',
	`value` varchar(380) NOT NULL default '',
	PRIMARY KEY (`option`)
) ENGINE=MyISAM;

INSERT INTO `global_options` VALUES ('db_version', '010003');


	
    

