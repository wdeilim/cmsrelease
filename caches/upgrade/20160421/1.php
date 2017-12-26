<?php

if(!db_fieldexists(table('reply'), 'do')) {
    db_query_simple("ALTER TABLE ".table('reply')." ADD COLUMN `do`  varchar(100) NULL AFTER `title`");
}


$__uptemplist = db_getall(table('functions'));
foreach ($__uptemplist AS $__tempitem) {
    $__tempsetting = string2array($__tempitem['setting']);
    if ($__tempsetting['bindings']['menu'] && !isset($__tempsetting['bindings']['reply'])) {
        $__tempsetting['bindings']['reply'] = array();
        $__tempsetting['bindings']['cover'] = array();
        $__tempsetting['bindings']['setting'] = array();
        foreach ($__tempsetting['bindings']['menu'] AS $__k=>$__v) {
            $__v['embed'] = 0;
            $__tempsetting['bindings']['reply'][$__k] = $__v;
        }
        $__tempsetting['bindings']['menu'] = array();
        db_update(table('functions'), array('setting'=>array2string($__tempsetting)), array('id'=>$__tempitem['id']));
    }
}

?>

