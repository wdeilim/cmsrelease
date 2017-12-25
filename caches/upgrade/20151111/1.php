<?php

if(!db_fieldexists(table('reply'), 'vip_title')) {
    db_query("ALTER TABLE ".table('reply')." ADD COLUMN `vip_title` varchar(50) NULL AFTER `title`");
}

if(!db_fieldexists(table('reply'), 'vip_link')) {
    db_query("ALTER TABLE ".table('reply')." ADD COLUMN `vip_link` tinyint(1) UNSIGNED NULL DEFAULT 0 AFTER `title`");
}

?>

