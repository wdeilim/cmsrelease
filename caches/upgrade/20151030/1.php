<?php

if(!db_fieldexists(table('library'), 'media_id')) {
	db_query("ALTER TABLE ".table('library')." ADD COLUMN `media_id` varchar(100) NULL AFTER `inorder`");
}

if(!db_fieldexists(table('library'), 'media_url')) {
	db_query("ALTER TABLE ".table('library')." ADD COLUMN `media_url` varchar(500) NULL AFTER `media_id`");
}

?>

