<?php

if(!db_fieldexists(table('functions'), 'reply')) {
	db_query("ALTER TABLE ".table('functions')." ADD COLUMN `reply` tinyint(1) UNSIGNED NULL DEFAULT 0 AFTER `oauth`");
}

?>