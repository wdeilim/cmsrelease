<?php

if(!db_fieldexists(table('users'), 'sms')) {
	db_query("ALTER TABLE ".table('users')." ADD COLUMN `sms` int(10) NULL DEFAULT 0 AFTER `point`");
}
if(!db_fieldexists(table('vip_users'), 'idnumber')) {
	db_query("ALTER TABLE ".table('vip_users')." ADD COLUMN `idnumber` varchar(30) NULL AFTER `address`");
}
if(!db_fieldexists(table('vip_users'), 'email')) {
	db_query("ALTER TABLE ".table('vip_users')." ADD COLUMN `email` varchar(50) NULL AFTER `phone`");
}
?>

