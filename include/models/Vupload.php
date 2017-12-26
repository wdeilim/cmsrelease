<?php
error_reporting(0);

/**
 * Class Vupload
 */
class Vupload extends CI_Model {
    var $_uconf = array();

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 上传成功返回示例
     * Array (
        [success] => 1
        [message] => 上传成功
        [upload_data] => Array
        (
            [file_name] => wxemulatorUser_1454248037.jpg
            [file_type] => image/jpeg
            [file_path] => D:/wwwroot/vwins/addons/vwjob/uploadfiles/avatar/
            [full_path] => D:/wwwroot/vwins/addons/vwjob/uploadfiles/avatar/wxemulatorUser_1454248037.jpg
            [raw_name] => wxemulatorUser_1454248037
            [orig_name] => wxemulatorUser_1454248037.jpg
            [client_name] => 201404211638580458.jpg
            [file_ext] => .jpg
            [file_size] => 7.16
            [is_image] => 1
            [image_width] => 180
            [image_height] => 180
            [image_type] => jpeg
            [image_size_str] => width="180" height="180"
            [full_path_site] => addons/vwjob/uploadfiles/avatar/wxemulatorUser_1454248037.jpg
            [full_path_new] => /addons/vwjob/uploadfiles/avatar/wxemulatorUser_1454248037.jpg
        )

     )
     */

    /**
     * 上传失败返回示例
     * Array(
        [success] => 0
        [message] => You did not select a file to upload.
     )
     */

    /**
     * @param $config
     * @param string $field_name
     * @return array
     */
    function upfile($config, $field_name = ""){
        if (empty($config)) return array('success' =>-1);
        $this->make_dir($config['upload_path']);
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload($field_name)){
            $data = array('success' =>0, 'message' => $this->upload->display_errors("",""));
        }else{
            $data = array('success' =>1, 'message' =>'上传成功' ,'upload_data' => $this->upload->data());
            $data['upload_data']['full_path_site'] = str_replace(FCPATH,'',$config['upload_path']).basename($data['upload_data']['full_path']);
            $data['upload_data']['full_path_new'] = BASE_DIR.$data['upload_data']['full_path_site'];

            //缩列图
            if ($config['thumb'] !== false) {
                $this->img2thumb($data['upload_data']['full_path'], $data['upload_data']['full_path']."_thumb.jpg", 100, 0);
            }

            /*
            $_ext = strtolower($data['upload_data']['file_ext']);
            if ($_ext == ".jpeg" || $_ext == ".jpg" || $_ext == ".gif" || $_ext == ".bmp" || $_ext == ".png") {
                //UPYUN
                if (defined('BASE_UPYUN_USE')){
                    if (BASE_UPYUN_USE == 1){
                        //加载upyun
                        $params = array();
                        $params['bucketname'] = BASE_UPYUN_BUCKET;
                        $params['username'] = BASE_UPYUN_USER;
                        $params['password'] = BASE_UPYUN_PASS;
                        $this->load->library('upyun', $params);
                        //上传到upyun
                        $_file = BASE_UPYUN_FOLDER.'uploadfiles/page/'.date("Ymd/").basename($data['upload_data']['full_path']);
                        $fh = fopen($data['upload_data']['full_path'], 'rb');
                        $rsp = $this->upyun->writeFile($_file, $fh, True);
                        fclose($fh);
                        if (isset($rsp['x-upyun-frames'])){
                            $data['upload_data']['full_path_local'] = $data['upload_data']['full_path_new'];
                            $data['upload_data']['full_path_site'] = BASE_UPYUN_HTTP.$_file;
                            $data['upload_data']['full_path_new'] = BASE_UPYUN_HTTP.$_file;
                        }
                    }
                }
            }
            */
        }
        return $data;
    }


    /**
     * @param $url          要下载的文件地址
     * @param $folder       保存目录位置
     * @param $pic_name     保存文件名
     * @param int $timeout  超时时间
     * @return bool
     */
    function get_file($url,$folder,$pic_name,$timeout = 300){
        set_time_limit($timeout); //限制最大的执行时间
        $destination_folder=$folder?$folder.'/':''; //文件下载保存目录
        $newfname=$destination_folder.$pic_name;//文件PATH
        $file=fopen($url,'rb');

        if($file){
            $newf=fopen($newfname,'wb');
            if($newf){
                while(!feof($file)){
                    if (!fwrite($newf,fread($file,1024*8),1024*8)) return false;
                }
            }else{
                return false;
            }
            if($file) fclose($file);
            if($newf) fclose($newf);
            return true;
        }else{
            return false;
        }
    }

    /**
     * php完美实现下载远程图片保存到本地
     * @param $url                  要下载的文件地址
     * @param string $save_dir      保存目录位置
     * @param string $filename      保存文件名, {ext}可替换后缀名
     * @param int $timeout          超时时间
     * @param int $type 	        采集方式
     * @return array
     */
    function getImage($url, $save_dir='', $filename='', $timeout = 300, $type=0){
        set_time_limit($timeout); //限制最大的执行时间
        if(trim($url)==''){
            return array('file_name'=>'','save_path'=>'','error'=>1);
        }
        if(trim($save_dir)==''){
            $save_dir='./';
        }
        $ext=strrchr($url,'.');
        if($ext!='.gif' && $ext!='.jpg' && $ext!='.jpeg' && $ext!='.png' && $ext!='.bmp'){
            return array('file_name'=>'','save_path'=>'','error'=>3);
        }
        if(trim($filename)==''){//保存文件名
            $filename = SYS_TIME.rand(1000,9999).$ext;
        }else{
            $filename = str_replace("{ext}", $ext, $filename);
        }
        if(substr($save_dir, -1) != '/'){
            $save_dir.='/';
        }
        //创建保存目录
        if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
            return array('file_name'=>'','save_path'=>'','error'=>5);
        }
        //获取远程文件所采用的方法
        if($type){
            $ch=curl_init();
            $timeout=5;
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
            $img=curl_exec($ch);
            curl_close($ch);
        }else{
            ob_start();
            readfile($url);
            $img=ob_get_contents();
            ob_end_clean();
        }
        //$size=strlen($img);
        //文件大小
        $fp2=@fopen($save_dir.$filename,'a');
        fwrite($fp2,$img);
        fclose($fp2);
        unset($img,$url);
        return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0);
    }

    /**
     * 创建文件夹
     * @param $path
     */
    function make_dir($path){
        if(!file_exists($path))
        {
            make_dir(dirname($path));
            @mkdir($path,0777);
            @chmod($path,0777);
        }
    }

    /**
     * 生成缩略图
     * @param string $src_img 源图绝对完整地址{带文件名及后缀名}
     * @param string $dst_img 目标图绝对完整地址{带文件名及后缀名}
     * @param int $width 缩略图宽{0:此时目标高度不能为0，目标宽度为源图宽*(目标高度/源图高)}
     * @param int $height 缩略图高{0:此时目标宽度不能为0，目标高度为源图高*(目标宽度/源图宽)}
     * @param int $cut 是否裁切{宽,高必须非0}
     * @param int $proportion 缩放{0:不缩放, 0<this<1:缩放到相应比例(此时宽高限制和裁切均失效)}
     * @return bool
     */
    function img2thumb($src_img, $dst_img, $width = 75, $height = 75, $cut = 0, $proportion = 0)
    {
        if(!is_file($src_img)) {
            return false;
        }
        $st = $this->fileext($src_img);
        if (!in_array($st, array('jpg','jpeg','png','gif','bmp'))) {
            return false;
        }
        $ot = $this->fileext($dst_img);
        $otfunc = 'image' . ($ot == 'jpg' ? 'jpeg' : $ot);
        $srcinfo = getimagesize($src_img);
        $src_w = $srcinfo[0];
        $src_h = $srcinfo[1];
        $type  = strtolower(substr(image_type_to_extension($srcinfo[2]), 1));
        if (empty($type)) {
            return false;
        }
        $createfun = 'imagecreatefrom' . ($type == 'jpg' ? 'jpeg' : $type);

        $dst_h = $height;
        $dst_w = $width;
        $x = $y = 0;

        /**
         * 缩略图不超过源图尺寸（前提是宽或高只有一个）
         */
        if(($width> $src_w && $height> $src_h) || ($height> $src_h && $width == 0) || ($width> $src_w && $height == 0))
        {
            $proportion = 1;
        }
        if($width> $src_w)
        {
            $dst_w = $width = $src_w;
        }
        if($height> $src_h)
        {
            $dst_h = $height = $src_h;
        }

        if(!$width && !$height && !$proportion)
        {
            return false;
        }
        if(!$proportion)
        {
            if($cut == 0)
            {
                if($dst_w && $dst_h)
                {
                    if($dst_w/$src_w> $dst_h/$src_h)
                    {
                        $dst_w = $src_w * ($dst_h / $src_h);
                        $x = 0 - ($dst_w - $width) / 2;
                    }
                    else
                    {
                        $dst_h = $src_h * ($dst_w / $src_w);
                        $y = 0 - ($dst_h - $height) / 2;
                    }
                }
                else if($dst_w xor $dst_h)
                {
                    if($dst_w && !$dst_h)  //有宽无高
                    {
                        $propor = $dst_w / $src_w;
                        $height = $dst_h  = $src_h * $propor;
                    }
                    else if(!$dst_w && $dst_h)  //有高无宽
                    {
                        $propor = $dst_h / $src_h;
                        $width  = $dst_w = $src_w * $propor;
                    }
                }
            }
            else
            {
                if(!$dst_h)  //裁剪时无高
                {
                    $height = $dst_h = $dst_w;
                }
                if(!$dst_w)  //裁剪时无宽
                {
                    $width = $dst_w = $dst_h;
                }
                $propor = min(max($dst_w / $src_w, $dst_h / $src_h), 1);
                $dst_w = (int)round($src_w * $propor);
                $dst_h = (int)round($src_h * $propor);
                $x = ($width - $dst_w) / 2;
                $y = ($height - $dst_h) / 2;
            }
        }
        else
        {
            $proportion = min($proportion, 1);
            $height = $dst_h = $src_h * $proportion;
            $width  = $dst_w = $src_w * $proportion;
        }

        $src = $createfun($src_img);
        $dst = imagecreatetruecolor($width ? $width : $dst_w, $height ? $height : $dst_h);
        try {
            $white = imagecolorallocate($dst, 255, 255, 255);
            imagefill($dst, 0, 0, $white);
        } catch (Exception $e) {}
        if(function_exists('imagecopyresampled'))
        {
            imagecopyresampled($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
        }
        else
        {
            imagecopyresized($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
        }
        $otfunc($dst, $dst_img);
        imagedestroy($dst);
        imagedestroy($src);
        return true;
    }

    function fileext($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }
}
?>