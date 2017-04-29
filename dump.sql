
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `servers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `game` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `vip` tinyint(1) NOT NULL DEFAULT '0',
  `port` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'XX',
  `votes` int(11) NOT NULL DEFAULT '0',
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `map` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `players` int(11) DEFAULT NULL,
  `maxPlayers` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `cache_time` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `settings` (
  `id` int(11) NOT NULL DEFAULT '1',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Comples Servers List',
  `facebook` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
  `twitter` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
  `contact_email` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `pagination` int(11) NOT NULL DEFAULT '15',
  `register` int(11) NOT NULL DEFAULT '1',
  `disable_index_querying` int(11) NOT NULL DEFAULT '0',
  `vote_login_restriction` int(11) NOT NULL DEFAULT '0',
  `server_cache` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '600',
  `show_offline_servers` int(11) NOT NULL DEFAULT '0',
  `email_confirmation` int(11) NOT NULL DEFAULT '1',
  `server_confirmation` int(11) NOT NULL DEFAULT '1',
  `advertise_top` varchar(2555) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
  `advertise_bottom` varchar(2555) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
  `url` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `settings` (`id`, `title`, `facebook`, `twitter`, `contact_email`, `pagination`, `register`, `disable_index_querying`, `vote_login_restriction`, `server_cache`, `show_offline_servers`, `email_confirmation`, `server_confirmation`, `advertise_top`, `advertise_bottom`, `url`)
VALUES
	(1,'Servers List','false','false','ContactEmail',15,1,0,0,'600',0,0,0,'false','false',NULL);


CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email_code` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `avatar` varchar(65) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `active` int(11) NOT NULL DEFAULT '0',
  `type` int(1) NOT NULL DEFAULT '0',
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0.0.0.0',
  `date` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `last_activity` varchar(65) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `email_code`, `name`, `avatar`, `active`, `type`, `ip`, `date`, `last_activity`)
VALUES
	(1,'admin','21232f297a57a5a743894a0e4a801fc3','email','','Admin','avatars/7963843a08.png',1,1,'0.0.0.0','beginning of time','1493491396');

CREATE TABLE `votes` (
  `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `server_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  KEY `server_id` (`server_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
