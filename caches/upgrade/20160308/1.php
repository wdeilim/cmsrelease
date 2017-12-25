<?php

if(!db_fieldexists(table('vip_users'), 'suppoint')) {
    db_query_simple("ALTER TABLE ".table('vip_users')." ADD COLUMN `suppoint`  int(10) UNSIGNED NULL DEFAULT 0 AFTER `outpoint`");
}

?>

