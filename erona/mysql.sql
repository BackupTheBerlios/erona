#
# Tabellenstruktur für Tabelle `feeds`
#

CREATE TABLE `feeds` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `rss` varchar(150) NOT NULL default '',
  `url` varchar(100) NOT NULL default '',
  `title` varchar(50) NOT NULL default '',
  `description` text NOT NULL,
  `lang` char(2) NOT NULL default '',
  `eingetragen` varchar(10) NOT NULL default '',
  `updated` varchar(10) NOT NULL default '',
  `reader` mediumint(8) unsigned NOT NULL default '0',
  UNIQUE KEY `id` (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `items`
#

CREATE TABLE `items` (
  `num` mediumint(11) unsigned NOT NULL auto_increment,
  `feed_id` mediumint(8) unsigned NOT NULL default '0',
  `url` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `stamp_date` varchar(10) NOT NULL default '',
  `indexed` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`num`),
  UNIQUE KEY `url` (`url`),
  KEY `stamp_date` (`stamp_date`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `items_contents`
#

CREATE TABLE `items_contents` (
  `id` mediumint(8) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `descr` text NOT NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `title` (`title`,`descr`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `user`
#

CREATE TABLE `user` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `password` varchar(12) NOT NULL default '',
  `title` varchar(50) NOT NULL default '',
  `descr` text NOT NULL,
  `public` char(1) NOT NULL default '0',
  `lasttime` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `user_feeds`
#

CREATE TABLE `user_feeds` (
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `feed_id` mediumint(8) unsigned NOT NULL default '0',
  KEY `feed_id` (`feed_id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `user_items`
#

CREATE TABLE `user_items` (
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `item_num` mediumint(8) unsigned NOT NULL default '0',
  `iread` tinyint(4) NOT NULL default '0',
  `date_read` varchar(10) default NULL,
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;
