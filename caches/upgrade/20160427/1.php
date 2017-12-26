<?php

if(!db_fieldexists(table('users_al'), 'wx_lastin')) {
    db_query_simple("ALTER TABLE ".table('users_al')." ADD COLUMN `wx_lastin`  bigint(18) UNSIGNED NULL DEFAULT 0 AFTER `wx_qrcode`");
}

if(!db_fieldexists(table('users_al'), 'al_lastin')) {
    db_query_simple("ALTER TABLE ".table('users_al')." ADD COLUMN `al_lastin`  bigint(18) UNSIGNED NULL DEFAULT 0 AFTER `al_qrcode`");
}

?>

