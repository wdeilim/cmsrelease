<?php

$_tmp_fans = db_getall("SELECT alid,openid FROM ".table('fans') , array('type'=>'alipay', 'follow'=>2), '`id`');
foreach($_tmp_fans AS $item) {
    $itemo5 = substr($item['openid'], 0, 5);
    if ($itemo5 != (string) intval($itemo5)) {
        db_update(table('fans'), array('follow'=>4), array('alid'=>$item['alid'], 'openid'=>$item['openid']));
        db_update(table('vip_users'), array('follow'=>4), array('alid'=>$item['alid'], 'openid'=>$item['openid']));
    }
}
?>

