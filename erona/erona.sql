# phpMyAdmin SQL Dump
# version 2.5.6-rc1
# http://www.phpmyadmin.net
#
# Host: localhost
# Erstellungszeit: 19. März 2004 um 03:25
# Server Version: 4.0.17
# PHP-Version: 4.3.4
# 
# Datenbank: `sascha_wwworker_com`
# 

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `feeds`
#

CREATE TABLE `feeds` (
  `id` int(9) NOT NULL auto_increment,
  `rss` varchar(150) NOT NULL default '',
  `url` varchar(100) NOT NULL default '',
  `title` varchar(50) NOT NULL default '',
  `description` text NOT NULL,
  `lang` char(2) NOT NULL default '',
  `eingetragen` varchar(10) NOT NULL default '',
  `updated` varchar(10) NOT NULL default '',
  `reader` int(11) NOT NULL default '0',
  UNIQUE KEY `id` (`id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `items`
#

CREATE TABLE `items` (
  `num` int(11) NOT NULL auto_increment,
  `id` varchar(255) NOT NULL default '0',
  `feed_id` int(11) NOT NULL default '0',
  `url` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `descr` text NOT NULL,
  `date` varchar(26) default NULL,
  PRIMARY KEY  (`num`),
  UNIQUE KEY `id` (`id`),
  KEY `date` (`date`),
  FULLTEXT KEY `fulltext_index` (`title`,`descr`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `user`
#

CREATE TABLE `user` (
  `id` int(9) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `password` varchar(12) NOT NULL default '',
  `title` varchar(50) NOT NULL default '',
  `descr` text NOT NULL,
  `public` char(1) NOT NULL default '0',
  UNIQUE KEY `id` (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `descr` (`descr`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `user_feeds`
#

CREATE TABLE `user_feeds` (
  `user_id` int(11) NOT NULL default '0',
  `feed_id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `user_items`
#

CREATE TABLE `user_items` (
  `user_id` int(11) NOT NULL default '0',
  `item_num` int(11) NOT NULL default '0',
  `iread` tinyint(4) NOT NULL default '0'
) TYPE=MyISAM;
