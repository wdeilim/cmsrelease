<?php

$up_temp_list = db_getall(table('vip_setting'), array('title'=>'signkey'));
foreach($up_temp_list AS $temp_item) {
    $up_temp_one = db_getone(table('vip_setting'), array('title'=>'signkey_'.$temp_item['alid']));
    if (empty($up_temp_one)) {
        db_update(table('vip_setting'), array('title'=>'signkey_'.$temp_item['alid']), array('title'=>$temp_item['title']));
    }else{
        db_delete(table('vip_setting'), array('title'=>$temp_item['title']));
    }
}

?>

