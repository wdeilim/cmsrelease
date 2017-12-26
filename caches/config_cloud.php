<?php
//查看IPAGE、版本 信息
include('config.php');
$arr = array('ipage'=>BASE_IPAGE,'version'=>ES_VERSION,'release'=>ES_RELEASE,'basename'=>BASE_NAME,'baseurl'=>BASE_URL,'basedir'=>BASE_DIR);
echo json_encode($arr); exit();
