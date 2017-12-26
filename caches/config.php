<?php
//自定义配置
include('version.php');
include('cache.config.php');
include('cache.offsite.php');
include('config_db.php');
include('safe.php');

//网站名称
define('BASE_NAME', (defined('DIY_BASE_NAME')&&DIY_BASE_NAME)?DIY_BASE_NAME:'服务管理平台');

//品牌名称
define('BRAND_NAME', (defined('DIY_BRAND_NAME')&&DIY_BRAND_NAME)?DIY_BRAND_NAME:'微窗');

//品牌网址
define('BRAND_URL', (defined('DIY_BRAND_URL')&&DIY_BRAND_URL)?DIY_BRAND_URL:'http://www.vwins.cn');

//网址
define('BASE_URL', isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443'?'https://':'http://'.$_SERVER['HTTP_HOST']);

//根目录
define('BASE_DIR', (isset($__proself)&&$__proself)?$__proself:'/');

//网址(含目录)
define('BASE_URI', BASE_URL.BASE_DIR);

//首页文件
define('BASE_IPAGE', (defined('DIY_BASE_IPAGE'))?DIY_BASE_IPAGE:'index.php');

//积分名称
define('POINT_NAME', (defined('DIY_POINT_NAME')&&DIY_POINT_NAME)?DIY_POINT_NAME:'积分');

//积分充值比例
define('POINT_CONVERT', (defined('DIY_POINT_CONVERT')&&DIY_POINT_CONVERT>0)?DIY_POINT_CONVERT:1);

//短信验证
define('SMS_OPEN', (defined('DIY_SMS_OPEN')&&DIY_SMS_OPEN)?DIY_SMS_OPEN:0);

//短信比例
define('SMS_PROPORTION', (defined('DIY_SMS_PROPORTION')&&DIY_SMS_PROPORTION>0)?DIY_SMS_PROPORTION:10);

//底部信息
define('BOTTOM_INFO', (defined('DIY_BOTTOM_INFO')&&DIY_BOTTOM_INFO)?DIY_BOTTOM_INFO:'广西伊索网络科技有限公司 版权所有 备案号：<a href="http://www.miitbeian.gov.cn/" target="_blank">桂ICP备14000021号</a><br/>地址：广西南宁青秀区长湖路24号浩天广场1007号 技术支持：<a href="http://www.vwins.cn">伊索网络</a> 联系电话：0771-5671712 传真：0771-5671712');

//定义框架路径
define('BASE_PATH', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);

//模板文件夹
define('BASE_TEMP', 'default');

//静态文件
define('JS_PATH', BASE_URI.'caches/statics/js/'); //JS
define('CSS_PATH', BASE_URI.'caches/statics/css/'); //CSS
define('IMG_PATH', BASE_URI.'caches/statics/images/'); //img

//网页编码
define('BASE_CHARSET', 'UTF-8');

//CI框架目录
define('CI_APPLICATION', 'include');

//加密密钥
define('BASE_ENCRYPTION', 'eso_net');

//数据库
define('BASE_DB_HOST', $__dbhost);
define('BASE_DB_NAME', $__dbname);
define('BASE_DB_USER', $__dbuser);
define('BASE_DB_PASS', $__dbpass);
define('BASE_DB_FORE', $__dbpre);