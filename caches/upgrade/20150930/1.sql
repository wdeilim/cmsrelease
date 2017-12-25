DROP TABLE IF EXISTS `es_users_point`;
CREATE TABLE `es_users_point` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned DEFAULT '0',
  `change` int(10) DEFAULT '0',
  `point` int(10) DEFAULT '0',
  `pointtxt` varchar(255) DEFAULT NULL,
  `indate` bigint(18) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='系统 - 客户积分记录';