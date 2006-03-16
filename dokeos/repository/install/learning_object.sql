CREATE TABLE `%prefix%learning_object` (
  `id` int(11) unsigned NOT NULL default '0',
  `owner` int(11) unsigned NOT NULL default '0',
  `type` varchar(255) NOT NULL default '',
  `title` text NOT NULL,
  `description` text NOT NULL,
  `parent` int(10) unsigned NOT NULL default '0',
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `owner` (`owner`),
  KEY `type` (`type`),
  KEY `category` (`category`)
);