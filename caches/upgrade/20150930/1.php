<?php

db_query("UPDATE ".table('vip_users')." A, ".table('fans')." B set A.avatar=B.avatar WHERE A.openid=B.openid AND A.alid=B.alid");

if(!db_fieldexists(table('functions'), 'point')) {
	db_query("ALTER TABLE ".table('functions')." ADD COLUMN `point` int(10) NULL DEFAULT -1 AFTER `reply`");
	db_update(table('functions'), array('point'=>-1));
}

if(!db_fieldexists(table('users'), 'point')) {
	db_query("ALTER TABLE ".table('users')." ADD COLUMN `point` int(10) NULL DEFAULT 0 AFTER `address`");
	db_update(table('users'), array('point'=>0));
}

?>

