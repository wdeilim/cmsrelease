DROP TABLE IF EXISTS `es_bindings`;
CREATE TABLE `es_bindings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(50) DEFAULT NULL,
  `module_name` varchar(255) DEFAULT NULL,
  `entry` varchar(10) DEFAULT '',
  `call` varchar(50) DEFAULT '',
  `title` varchar(50) DEFAULT NULL,
  `do` varchar(30) DEFAULT NULL,
  `state` varchar(200) DEFAULT NULL,
  `direct` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `module` (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='系统 - 绑定菜单';