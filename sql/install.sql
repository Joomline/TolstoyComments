CREATE TABLE IF NOT EXISTS `#__tolstoycomments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_comm` int(11) NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `ip` varchar(16) NOT NULL,
  `datеtime` varchar(26) NOT NULL,
  `rating` int(1) NOT NULL DEFAULT '0',
  `attaches` text NOT NULL,
  `visible` varchar(6) NOT NULL,
  `user_id` int(12) NOT NULL DEFAULT '0',
  `user_nick` varchar(256) NOT NULL,
  `user_name` varchar(256) NOT NULL,
  `user_email` varchar(256) NOT NULL,
  `user_phone` varchar(256) NOT NULL,
  `user_avatar` varchar(256) NOT NULL,
  `identity` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;