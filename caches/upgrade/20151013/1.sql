DROP TABLE IF EXISTS `es_pay`;
CREATE TABLE `es_pay` (
  `title` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `content` text,
  `view` tinyint(1) unsigned DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='系统 - 支付方式';

INSERT INTO `es_pay` VALUES ('支付宝', 'alipay', 'array (\n  \'content\' => \'全球领先的独立第三方支付平台\',\n  \'partner\' => \'\',\n  \'key\' => \'\',\n  \'account\' => \'\',\n)', '0');

DROP TABLE IF EXISTS `es_pay_order`;
CREATE TABLE `es_pay_order` (
  `id` bigint(18) NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned DEFAULT '0',
  `amount` int(10) unsigned DEFAULT '0',
  `int` int(10) unsigned DEFAULT '0',
  `status` tinyint(1) unsigned DEFAULT '0',
  `setting` text,
  `indate` bigint(18) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='系统 - 支付订单记录';