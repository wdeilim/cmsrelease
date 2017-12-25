<?php

if(!db_fieldexists(table('pay_order'), 'payid')) {
	db_query("ALTER TABLE ".table('pay_order')." ADD COLUMN `payid`  varchar(50) NULL AFTER `id`");
	db_query("UPDATE ".table('pay_order')." SET payid=id");
}

?>

