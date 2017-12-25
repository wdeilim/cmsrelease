<?php

if(!db_fieldexists(table('users_al'), 'wx_corp')) {
    db_query_simple("ALTER TABLE ".table('users_al')." ADD COLUMN `wx_corp` text NULL AFTER `wx_qrcode`");
}

?>

