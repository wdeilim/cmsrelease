DROP TABLE IF EXISTS `es_tmp`;
CREATE TABLE `es_tmp` (
  `title` varchar(100) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `content` text,
  `indate` bigint(18) unsigned DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='系统 - 通用临时数据';

