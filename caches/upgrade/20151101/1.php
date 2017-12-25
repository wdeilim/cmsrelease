<?php

db_query("ALTER TABLE ".table('message')." MODIFY COLUMN `id`  bigint(18) UNSIGNED NOT NULL AUTO_INCREMENT FIRST");

?>

