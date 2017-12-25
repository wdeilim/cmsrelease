<?php

db_query_simple("ALTER TABLE ".table('vip_users')." ADD INDEX `VIDX_ALID_OPENID` (`alid`,`openid`)");
db_query_simple("ALTER TABLE ".table('vip_users')." ADD INDEX `VIDX_ALID_CARD` (`alid`,`card`)");
db_query_simple("ALTER TABLE ".table('vip_users')." ADD INDEX `VIDX_OPENID` (`openid`)");
db_query_simple("ALTER TABLE ".table('vip_users')." ADD INDEX `VIDX_ALID` (`alid`)");

db_query_simple("ALTER TABLE ".table('fans')." DROP INDEX `appidkey`");
db_query_simple("ALTER TABLE ".table('fans')." DROP INDEX `openidkey`");
db_query_simple("ALTER TABLE ".table('fans')." DROP INDEX `alidkey`");
db_query_simple("ALTER TABLE ".table('fans')." ADD INDEX `VIDX_ALID_USERPHONE` (`alid`,`userphone`)");
db_query_simple("ALTER TABLE ".table('fans')." ADD INDEX `VIDX_ALID_OPENID` (`alid`,`openid`)");
db_query_simple("ALTER TABLE ".table('fans')." ADD INDEX `VIDX_OPENID` (`openid`)");
db_query_simple("ALTER TABLE ".table('fans')." ADD INDEX `VIDX_APPID` (`appid`)");
db_query_simple("ALTER TABLE ".table('fans')." ADD INDEX `VIDX_ALID` (`alid`)");

db_query_simple("ALTER TABLE ".table('linkage')." DROP INDEX `parentid`");
db_query_simple("ALTER TABLE ".table('linkage')." ADD INDEX `VIDX_PARENTID_LISTORDER` (`parentid`,`listorder`)");

db_query_simple("ALTER TABLE ".table('bindings')." DROP INDEX `module`");
db_query_simple("ALTER TABLE ".table('bindings')." ADD INDEX `VIDX_MODULE` (`module`)");

db_query_simple("ALTER TABLE ".table('core_paylog')." DROP INDEX `idx_openid`");
db_query_simple("ALTER TABLE ".table('core_paylog')." DROP INDEX `idx_tid`");
db_query_simple("ALTER TABLE ".table('core_paylog')." DROP INDEX `idx_uniacid`");
db_query_simple("ALTER TABLE ".table('core_paylog')." ADD INDEX `VIDX_OPENID` (`openid`)");
db_query_simple("ALTER TABLE ".table('core_paylog')." ADD INDEX `VIDX_TID` (`tid`)");
db_query_simple("ALTER TABLE ".table('core_paylog')." ADD INDEX `VIDX_ALID` (`alid`)");


db_query_simple("ALTER TABLE ".table('message')." ADD INDEX `VIDX_ALID` (`alid`)");
db_query_simple("ALTER TABLE ".table('message')." ADD INDEX `VIDX_MSGTYPE` (`msgtype`)");
db_query_simple("ALTER TABLE ".table('message')." ADD INDEX `VIDX_INDATE` (`indate`)");
db_query_simple("ALTER TABLE ".table('message')." ADD INDEX `VIDX_ALID_MSGTYPE` (`alid`, `msgtype`)");
$_show_table = db_query("SHOW TABLES")->result_array();
if ($_show_table) {
    foreach($_show_table AS $_table_val) {
        $_table_val = reset($_table_val);
        if (preg_match('/^'.BASE_DB_FORE.'_al\d+_message*/', $_table_val)) {
            db_query_simple("ALTER TABLE ".$_table_val." ADD INDEX `VIDX_ALID` (`alid`)");
            db_query_simple("ALTER TABLE ".$_table_val." ADD INDEX `VIDX_MSGTYPE` (`msgtype`)");
            db_query_simple("ALTER TABLE ".$_table_val." ADD INDEX `VIDX_INDATE` (`indate`)");
            db_query_simple("ALTER TABLE ".$_table_val." ADD INDEX `VIDX_ALID_MSGTYPE` (`alid`, `msgtype`)");
        }
    }
}

?>

