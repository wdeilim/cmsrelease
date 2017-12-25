<?php

db_query_simple("ALTER TABLE ".table('vip_users')." ADD INDEX `VIDX_ALID_INDATE` (`alid`,`indate`)");

?>

