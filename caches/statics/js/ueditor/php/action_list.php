<?php
@session_start();
/**
 * 获取已上传的文件列表
 * User: Jinqn
 * Date: 14-04-09
 * Time: 上午10:17
 */
include "Uploader.class.php";

/* 判断类型 */
switch ($_GET['action']) {
    /* 列出文件 */
    case 'listfile':
        $allowFiles = $CONFIG['fileManagerAllowFiles'];
        $listSize = $CONFIG['fileManagerListSize'];
        $path = $CONFIG['fileManagerListPath'];
        break;
    /* 列出图片 */
    case 'listimage':
    default:
        $allowFiles = $CONFIG['imageManagerAllowFiles'];
        $listSize = $CONFIG['imageManagerListSize'];
        $path = $CONFIG['imageManagerListPath'];
}
$allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

/* 获取参数 */
$size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
$start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
$end = $start + $size;

/* 获取文件列表 */
$path = $_SERVER['DOCUMENT_ROOT'] . (substr($path, 0, 1) == "/" ? "":"/") . $path;
$files = getfiles($path, $allowFiles);
if (!count($files)) {
    return json_encode(array(
        "state" => "no match file",
        "list" => array(),
        "start" => $start,
        "total" => count($files)
    ));
}

$list = array();
/* 获取指定范围的列表 */
$len = count($files);
$filefirst = end($files);
if ($filefirst['mtime'] === -99) {
    $len--;
    array_pop($files);
    $list[] = $filefirst;
}
for ($i = min($end, $len) - 1; $i < $len && $i >= 0 && $i >= $start; $i--){
    $list[] = $files[$i];
}
//倒序
//for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
//    $list[] = $files[$i];
//}

/* 返回数据 */
$result = json_encode(array(
    "state" => "SUCCESS",
    "list" => $list,
    "start" => $start,
    "total" => count($files)
));

return $result;


/**
 * 遍历获取目录下的指定类型的文件
 * @param $path
 * @param array $files
 * @return array
 */
function getfiles($path, $allowFiles, &$files = array())
{
    if (!is_dir($path)) return null;
	
	//UPYUN(加载图片列表时)		
	if ($_GET['action'] == "listimage"){
		$ufiles = array();
		if (defined('BASE_UPYUN_USE')){
			if (BASE_UPYUN_USE == 1){
				//加载upyun
				$params = array();
				$params['bucketname'] = BASE_UPYUN_BUCKET;
				$params['username'] = BASE_UPYUN_USER;
				$params['password'] = BASE_UPYUN_PASS;
				$upyun = new Upyun($params);
				try {
					$nul1 = BASE_UPYUN_FOLDER.trim(substr($path, strlen($_SERVER['DOCUMENT_ROOT'])),'/');
					$list = $upyun->getList($nul1);
					foreach($list as $item){
						if ($item['type'] == 'folder'){
							$_list = $upyun->getList($nul1.'/'.$item['name']);
							foreach($_list as $_item){
								if ($_item['type'] == 'file'){
									$ufiles[] = array(
										'url'=> BASE_UPYUN_HTTP.$nul1.'/'.$item['name'].'/'.$_item['name'],
										'mtime'=> $_item['time']
									);
								}
							}
						}
					}
				}
				catch(Exception $e) {
					echo $e->getCode();
					echo $e->getMessage();
				}
			}
		}
		if (!empty($ufiles)){
			return $ufiles;
		}
	}

    if ($_GET['action'] == 'listimage') {
        $dirfile = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR;
        if ($_GET['get'] == '-1'){
            $_GET['get'] = $_SESSION['uedit:'.$_GET['action'].':_path'];
        }else{
            $_SESSION['uedit:'.$_GET['action'].':_path'] = $_GET['get'];
        }
        $get = trim($_GET['get'],"|")."|";
        $pget = str_replace("|","/", $get);
        $pget = str_replace("..", "", $pget);
        if ($pget && $pget != "/") {
            $path.= $pget;
            if (empty($_GET['start'])) {
                $files[] = array(
                    'url'=> substr($dirfile.'dialogs\image\images\folder.png', strlen($_SERVER['DOCUMENT_ROOT'])),
                    'mtime'=> -99,
                    'title'=> '...',
                    'type'=> 'dir',
                    'get'=> substr($_GET['get'], 0, strrpos($_GET['get'], '|'))
                );
            }
        }
    }

    if(substr($path, strlen($path) - 1) != '/') $path .= '/';
    $handle = opendir($path);
    while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..' && substr($file,-10) != "_thumb.jpg" && substr($file,-9) != "_cart.jpg") {
            $path2 = $path . $file;
            if (is_dir($path2)) {
                if ($_GET['action'] == 'listimage') {
                    $files[] = array(
                        'url'=> substr($dirfile.'dialogs\image\images\folder.png', strlen($_SERVER['DOCUMENT_ROOT'])),
                        'mtime'=> filemtime($path2),
                        'title'=> $file,
                        'type'=> 'dir',
                        'get'=> $get.$file
                    );
                }else{
                    getfiles($path2, $allowFiles, $files);
                }
            } else {
                if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
                    $files[] = array(
                        'url'=> substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
                        'mtime'=> filemtime($path2),
                        'title'=> substr($file, 0, strrpos($file, '.')),
                        'type'=> 'file',
                        'get'=> ''
                    );
                }
            }
        }
    }

    if ($_GET['action'] == 'listimage') {
        if (intval($_COOKIE['alipay_userid_admin']) && preg_match('/^uploadfiles\/users\/(\d+)\/*$/i', substr($path, strlen($_SERVER['DOCUMENT_ROOT'])+1))) {
            foreach($files AS $k=>$v) {
                if (!in_array($v['title'], array('...', 'images'))) {
                    unset($files[$k]);
                }
            }
        }

        $dir = array();
        $file = array();
        $inorder = array();
        $inorderfile = array();
        foreach ($files AS $key => $val) {
            if ($val['type'] == 'dir') {
                $dir[] = $val;
                $inorder[] = $val['title'];
            }else{
                $file[] = $val;
                $inorderfile[] = $val['title'];
            }

        }
        array_multisort($inorder, SORT_DESC, $dir);
        array_multisort($inorderfile, SORT_DESC, $file);
        $files = array_merge($file, $dir);
    }

    return $files;
}