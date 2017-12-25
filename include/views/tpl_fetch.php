<?php
if ( ! defined('___TPL_FETCH_FILE')) exit('No direct script access allowed');
if (___TPL_FETCH_FILE) {
	tpl(___TPL_FETCH_FILE, get_defined_vars());
}else{
	tpl(get_defined_vars());
}
?>