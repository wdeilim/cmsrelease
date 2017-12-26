DROP TABLE IF EXISTS `es_bind_setting`;
CREATE TABLE `es_bind_setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(100) DEFAULT NULL,
  `alid` int(10) unsigned DEFAULT '0',
  `do` varchar(100) DEFAULT NULL,
  `setting` text,
  PRIMARY KEY (`id`),
  KEY `VIDX_MODULE_DO` (`module`,`do`),
  KEY `VIDX_ALID` (`alid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='系统 - 绑定参数';
