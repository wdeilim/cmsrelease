DROP TABLE IF EXISTS `es_vip_uploadfiles`;
CREATE TABLE `es_vip_uploadfiles` (
  `id` bigint(18) unsigned NOT NULL AUTO_INCREMENT,
  `alid` int(10) unsigned DEFAULT '0',
  `openid` varchar(30) DEFAULT '0',
  `path` varchar(255) DEFAULT NULL,
  `indate` bigint(18) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `VIDX_ALID_OPENID` (`alid`,`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='VIP - 上传记录';

