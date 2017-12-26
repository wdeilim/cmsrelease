<?php
error_reporting(E_ALL ^ E_NOTICE);
@set_time_limit(0);
@set_magic_quotes_runtime(0);
date_default_timezone_set('prc');
ob_start();
header("Content-Type:text/html;charset=utf-8");

if(phpversion() < '5.3.0') set_magic_quotes_runtime(0);
if(phpversion() < '5.3.0') exit('您的php版本过低，不能安装本软件，请升级到5.3.0或更高版本再安装，谢谢！');
define('BASE_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('CLOUD_URL', 'http://cloud.vwins.cn/');
define('CLOUD_GATEWAY', CLOUD_URL.'gateway.php');
if(file_exists(BASE_PATH.'caches/install.lock')) exit('您已经安装过微窗,如果需要重新安装，请删除 ./caches/install.lock 文件！');

function get_link($str, $amp = '', $baoliu = '', $array = array(), $allurl = ''){ if (!$amp) { $amp = '&'; } $str = str_replace('|', ',', $str); $arr = explode(',', $str); $get = !empty($array) ? $array : $_GET; if ($baoliu) { $get = array(); foreach ($arr as $key => $value) { $get[$value] = $_GET[$value]; } } else { foreach ($arr as $key => $value) { unset($get[$value]); } } $url = ''; if (!empty($get)) { foreach ($get as $k => $v) { $url .= "{$k}={$v}{$amp}"; } } $url = !empty($url) ? '?' . substr($url, 0, -strlen($amp)) : '?index=0'; $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://'; if ($allurl) { return $_SERVER['PHP_SELF'] . $url; } else { return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $_SERVER['PHP_SELF'] . $url; }}function local_mkdirs($path){ if (!is_dir($path)) { local_mkdirs(dirname($path)); mkdir($path); } return is_dir($path);}function showerror($errno, $message = ''){ return array('errno' => $errno, 'error' => $message);}function getFile($url, $save_dir = '', $filename = '', $type = 0){ if (trim($url) == '') { return false; } if (trim($save_dir) == '') { $save_dir = './'; } if (0 !== strrpos($save_dir, '/')) { $save_dir .= '/'; } if (!file_exists($save_dir) && !mkdir($save_dir, 511, true)) { return false; } if ($type) { $ch = curl_init(); curl_setopt($ch, CURLOPT_URL, $url); curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); $content = curl_exec($ch); curl_close($ch); } else { ob_start(); readfile($url); $content = ob_get_contents(); ob_end_clean(); } $size = strlen($content); $fp2 = @fopen($save_dir . $filename, 'a'); fwrite($fp2, $content); fclose($fp2); unset($content, $url); return array('file_name' => $filename, 'save_path' => $save_dir . $filename, 'size'=>$size);}
class engine_compress_decompress { public $sourcePath = ''; public $savePath = ''; public $object = false; public $resultInfo = array(); public $documentRoot = ''; public $datasec, $ctrl_dir = array(); public $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00"; public $old_offset = 0; public $dirs = array("."); public function __construct() { $this->documentRoot = $_SERVER['DOCUMENT_ROOT']; } public function decompress($sourceFile, $toPath) { $sourceFile = ((substr($sourceFile, 0, 1) == '/') || (substr($sourceFile, 0, 1) == '\\')) ? $sourceFile : '/' . $sourceFile; $sourceFile = $this->documentRoot . str_replace($this->documentRoot, '', $sourceFile); $toPath = ((substr($toPath, 0, 1) == '/') || (substr($toPath, 0, 1) == '\\')) ? $toPath : '/' . $toPath; $toPath = $this->documentRoot . str_replace($this->documentRoot, '', $toPath); $errorTmp = null; if (is_file($sourceFile) == false) { $errorTmp = '不是有效文件 : ' . $this->sourcePath; } else { $this->sourcePath = $this->replacePath($sourceFile); $fileName = @strtolower(@substr(@strrchr($this->sourcePath, '/'), 1)); $fileRoot = @substr($this->sourcePath, 0, (@strlen($this->sourcePath) - @strlen($fileName))); } if (is_dir($toPath) == false) { $isToPath = $this->dirCreate($toPath); if ($isToPath !== true) { $errorTmp = '无法自动创建目录 : ' . $toPath; } } if (is_dir($toPath) == false) { $errorTmp = '目标目录不正确'; } else { if (substr($toPath, 0, -1) != '/') { $toPath = $this->replacePath($toPath); } $this->savePath = $toPath; } if (empty($errorTmp)) { $resultTemp = $this->ExtractAll($this->sourcePath, $this->savePath); if ($resultTemp == -1) { $this->resultInfo['error'] = '文件: ' . $fileName . ' 解压缩失败'; } else { $this->resultInfo['filelist'] = $resultTemp; $totalFolder = 0; $totalFiles = 0; foreach ($this->resultInfo['filelist'] as $value) { if ($value == (1)) { $totalFiles += 1; } if ($value === (-1)) { $totalFolder += 1; } } $this->resultInfo['totalfolder'] = $totalFolder; $this->resultInfo['totalfiles'] = $totalFiles; $this->resultInfo['linkurl'] = 'http://' . $_SERVER['HTTP_HOST'] . str_replace($this->documentRoot, '', $this->savePath); $this->resultInfo['fileroot'] = str_replace($this->documentRoot, '', $this->savePath); } } else { $this->resultInfo['error'] = $errorTmp; } return $this->resultInfo; } public function compress($sourcePath = array(), $toPath = '') { $errorC = null; $filesA = array(); if ((!empty($sourcePath)) && (!empty($toPath))) { $toPath = str_replace('\\', '/', $toPath); if (substr($toPath, 0, 1) == '/') { $toPath = substr($toPath, 1); } $this->savePath = str_replace($this->documentRoot . dirname($_SERVER['PHP_SELF']), '', $toPath); if (is_array($sourcePath)) { if (!empty($sourcePath)) { foreach ($sourcePath as $value) { if (file_exists($value)) { if ((is_dir($value)) && (substr($value, 0, -1) != '/')) { $value = $value . '/'; } $filesA[] = $value; } else { $errorC = '文件' . $value . '不存在'; } } } } } else { $errorC = '请完整输入'; } if ((empty($errorC)) && (!empty($filesA))) { return $filesA; } } public function fileUpload($inputName = '') { $fuResult = array(); $sourcePath = ''; if ($inputName == '') { $inputName = 'upfile'; } if ($this->fileext($_FILES["{$inputName}"]['name']) == 'zip') { $sourceFile = $_FILES["{$inputName}"]['tmp_name']; $targetFile = dirname($_SERVER['PHP_SELF']) . '/' . $_FILES["{$inputName}"]['name']; $isMove = @move_uploaded_file($sourceFile, $_SERVER['DOCUMENT_ROOT'] . $targetFile); if ($isMove != false) { $sourcePath = $targetFile; $upfileError = '<font color="green">文件上传成功</font>'; } else { if ($_FILES["{$inputName}"]['error'] == 4) { $upfileError = '<font color="red">上传文件不能为空</font>'; } elseif (($_FILES["{$inputName}"]['error'] == 1) || $_FILES["{$inputName}"]['error'] == 2) { $upfileError = '<font color="red">上传文件大小超过限制</font>'; } elseif ($_FILES["{$inputName}"]['error'] == 6) { $upfileError = '<font color="red">上传文件转移时发生错误</font>'; } elseif ($_FILES["{$inputName}"]['error'] == 7) { $upfileError = '<font color="red">上传文件写入失败</font>'; } elseif ($_FILES["{$inputName}"]['error'] == 3) { $upfileError = '<font color="red">只有部分文件被上传</font>'; } else { $upfileError = '<font color="red">上传失败,未知错误</font>'; } } } else { $upfileError= '<font color="red">文件只能是ZIP类型</font>'; } $fuResult['sourcefile'] = $sourcePath; $fuResult['error'] = $upfileError; return $fuResult; } public function fileArray($sourceFile = '') { $filearray = array(); if (!empty($sourceFile)) { if (stristr($sourceFile, $this->documentRoot) != false) { $sourceFile = str_replace($this->documentRoot, '', $sourceFile); } if (substr($sourceFile, 0, 1) != '/') { $sourceFile = '/' . $sourceFile; } if (is_file($this->documentRoot . $sourceFile)) { if ($this->fileext($sourceFile) == 'zip') { $filearray[] = str_replace($this->documentRoot, '', $sourceFile); } else { $filearray['pleaseinput'] = '文件只能是ZIP类型'; } } else { if (is_dir($this->documentRoot . $sourceFile)) { $flieList = $this->fileList($this->documentRoot . $sourceFile); foreach ($flieList as $value) { if ($this->fileext($value) == 'zip') { $filearray[] = str_replace($this->documentRoot, '', $value); } } } else { $filearray['pleaseinput'] = '不是有效文件或目录, 请重新输入'; } } } else { $filearray['pleaseinput'] = '请在上一步骤输入需要操作的文件路径或者文件夹来获取文件'; } if (empty($filearray)) { $filearray['pleaseinput'] = '该目录没有任何可解压缩的文件'; } return $filearray; } public function compressFileArray($sourceFile) { $resultArray= array(); $filearray = array(); $errorarray = ''; if (isset($sourceFile)) { if (stristr($sourceFile, $this->documentRoot) != false) { $sourceFile = str_replace($this->documentRoot, '', $sourceFile); } if (($sourceFile == '/') || ($sourceFile == '')) { $sourceFile = './'; } $this->sourcePath = $sourceFile; if (is_file($this->sourcePath)) { $filearray[] = $this->sourcePath; } else { if (is_dir($this->sourcePath)) { $flieList = $this->fileListC($this->sourcePath); foreach ($flieList as $value) { $newValue = str_replace($this->documentRoot, '', $value); if (substr($newValue, 0, 3) == '../') { continue; } elseif (substr($newValue, 0, 2) == './') { $newValue = substr($newValue, 2); } $filearray[] = $newValue; } } else { $errorarray = '不是有效文件或目录, 请重新输入'; } } } else { $errorarray = '请在上一步骤输入需要操作的文件路径或者文件夹来获取文件'; } if ((empty($filearray)) && (empty($errorarray))) { $resultArray['error'] = '该目录没有任何可解压缩的文件'; } elseif (!empty($errorarray)) { $resultArray['error'] = $errorarray; } else { $resultArray['files'] = $filearray; } return $resultArray; } public function fileext($filename) { return @strtolower(@substr(@strrchr($filename, '.'), 1,10)); } public function replacePath($dirString) { $temp = str_replace('\\', '/', $dirString); if ((substr($temp, 0, -1) != '/') && (is_file($temp) == false)) { $temp .= '/'; } $temp = str_replace('//', '/', $temp); $temp = str_replace('\\/', '/', $temp); $temp = str_replace('/\\', '/', $temp); return $temp; } public function dirCreate($dirName) { $dirName = $this->replacePath($dirName); $dirTemp = explode('/', $dirName); $dirCount = count($dirTemp) - 1; $dirCur = ''; $dirCreateError = array(); for ($j = 0; $j < $dirCount; $j++) { $dirCur .= $dirTemp[$j] . '/'; if (is_dir($dirCur)) { continue; } $isMkdir = @mkdir($dirCur, 0777); if ($isMkdir == false) { $dirCreateError[$j] = $dirTemp[$j]; } } if (empty($dirCreateError)) { return true; } else { return $dirCreateError; } } public function fileList($dir, $list = array()) { if (!is_array($list)) { $list = array(); } if (empty($dir)) { $dir = './'; } $dir = str_replace('//', '/', $this->replacePath($dir)); $files = glob($dir . '*'); foreach ($files as $value) { $list[] = $value; if (is_dir($value)) { $list = $this->fileList($value, $list); } } return $list; } public function fileListC($dir, $list = array()) { if (!is_array($list)) { $list = array(); } $dir = str_replace('//', '/', $this->replacePath($dir)); $files = glob($dir . '*'); foreach ($files as $value) { if (is_dir($value)) { $list = $this->fileList($value, $list); } else { $list[] = $value; } } return $list; } public function get_List($zip_name) { $ret = ''; $zip = @fopen($zip_name, 'rb'); if (!$zip) { return (0); } $centd = $this->ReadCentralDir($zip, $zip_name); @rewind($zip); @fseek($zip, $centd['offset']); for ($i = 0; $i < $centd['entries']; $i++) { $header = $this->ReadCentralFileHeaders($zip); $header['index'] = $i; $info['filename'] = $header['filename']; $info['stored_filename'] = $header['stored_filename']; $info['size'] = $header['size']; $info['compressed_size'] = $header['compressed_size']; $info['crc'] = strtoupper(dechex($header['crc'])); $info['mtime'] = $header['mtime']; $info['comment'] = $header['comment']; $info['folder'] = ($header['external'] == 0x41FF0010 || $header['external'] == 16) ? 1 : 0; $info['index'] = $header['index']; $info['status'] = $header['status']; $ret[] = $info; unset($header); } return $ret; } public function Add($files, $compact) { if (!is_array($files[0])) { $files = array($files); } for ($i = 0; $files[$i]; $i++) { $fn = $files[$i]; if (!in_Array(dirname($fn[0]), $this->dirs)) { $this->add_Dir(dirname($fn[0])); } if (basename($fn[0])) { $ret[basename($fn[0])] = $this->add_File($fn[1], $fn[0], $compact); } } return $ret; } public function get_file() { $data = implode('', $this->datasec); $ctrldir = implode('', $this->ctrl_dir); return $data . $ctrldir . $this->eof_ctrl_dir . pack('v', sizeof($this->ctrl_dir)) . pack('v', sizeof($this->ctrl_dir)) . pack('V', strlen($ctrldir)) . pack('V', strlen($data)) . "\x00\x00"; } public function add_dir($name) { $name = str_replace("\\", "/", $name); $fr = "\x50\x4b\x03\x04\x0a\x00\x00\x00\x00\x00\x00\x00\x00\x00"; $fr .= pack("V", 0) . pack("V", 0) . pack("V", 0) . pack("v", strlen($name)); $fr .= pack("v", 0) . $name . pack("V", 0) . pack("V", 0) . pack("V", 0); $this->datasec[] = $fr; $new_offset = strlen(implode("", $this->datasec)); $cdrec = "\x50\x4b\x01\x02\x00\x00\x0a\x00\x00\x00\x00\x00\x00\x00\x00\x00"; $cdrec .= pack("V", 0) . pack("V", 0) . pack("V", 0) . pack("v", strlen($name)); $cdrec .= pack("v", 0) . pack("v", 0) . pack("v", 0) . pack("v", 0); $ext = "\xff\xff\xff\xff"; $cdrec .= pack("V", 16) . pack("V", $this->old_offset) . $name; $this->ctrl_dir[] = $cdrec; $this->old_offset = $new_offset; $this->dirs[] = $name; } public function CompileZipFile($filename, $tozipfilename, $ftype = 'dir') { if (@function_exists('gzcompress')) { if ($ftype == 'dir') { $filelist = $this->ListDirFiles($filename); } elseif ($ftype == 'file') { $filelist[] = $filename; } else { $filelist = $filename; } $i = 0; if (count($filelist) > 0) { foreach ($filelist as $filename) { if (is_file($filename)) { $i++; $fd = fopen($filename, "r"); if (filesize($filename) > 0) { $content = fread($fd, filesize($filename)); } else { $content = ' '; } fclose($fd); $this->add_File($content, $filename); } } $out = $this->get_file(); $fp = fopen($tozipfilename, "w"); fwrite($fp, $out, strlen($out)); fclose($fp); } return $i; } else { return 0; } } public function ListDirFiles($dirname) { $files = array(); if (is_dir($dirname)) { $fh = opendir($dirname); while (($file = readdir($fh)) !== false) { if (strcmp($file, '.') == 0 || strcmp($file, '..') == 0) { continue; } $filepath = $dirname . '/' . $file; if (is_dir($filepath)) { $files = array_merge($files, $this->ListDirFiles($filepath)); } else { array_push($files, $filepath); } } closedir($fh); } else { $files = false; } return $files; } public function add_File($data, $name, $compact = 1) { $name = str_replace('\\', '/', $name); $dtime = dechex($this->DosTime()); $hexdtime = '\x' . $dtime[6] . $dtime[7] . '\x' . $dtime[4] . $dtime[5] . '\x' . $dtime[2] . $dtime[3] . '\x' . $dtime[0] . $dtime[1]; eval('$hexdtime = "' . $hexdtime . '";'); if ($compact) $fr = "\x50\x4b\x03\x04\x14\x00\x00\x00\x08\x00" . $hexdtime; else { $fr = "\x50\x4b\x03\x04\x0a\x00\x00\x00\x00\x00" . $hexdtime; } $unc_len = strlen($data); $crc = crc32($data); if ($compact) { $zdata = gzcompress($data); $c_len = strlen($zdata); $zdata = substr(substr($zdata, 0, strlen($zdata) - 4), 2); } else { $zdata = $data; } $c_len = strlen($zdata); $fr .= pack('V', $crc) . pack('V', $c_len) . pack('V', $unc_len); $fr .= pack('v', strlen($name)) . pack('v', 0) . $name . $zdata; $fr .= pack('V', $crc) . pack('V', $c_len) . pack('V', $unc_len); $this->datasec[] = $fr; $new_offset = strlen(implode('', $this->datasec)); if ($compact) { $cdrec = "\x50\x4b\x01\x02\x00\x00\x14\x00\x00\x00\x08\x00"; } else { $cdrec = "\x50\x4b\x01\x02\x14\x00\x0a\x00\x00\x00\x00\x00"; } $cdrec .= $hexdtime . pack('V', $crc) . pack('V', $c_len) . pack('V', $unc_len); $cdrec .= pack('v', strlen($name)) . pack('v', 0) . pack('v', 0); $cdrec .= pack('v', 0) . pack('v', 0) . pack('V', 32); $cdrec .= pack('V', $this->old_offset); $this->old_offset = $new_offset; $cdrec .= $name; $this->ctrl_dir[] = $cdrec; return true; } public function DosTime() { $timearray = getdate(); if ($timearray['year'] < 1980) { $timearray['year'] = 1980; $timearray['mon'] = 1; $timearray['mday'] = 1; $timearray['hours'] = 0; $timearray['minutes'] = 0; $timearray['seconds'] = 0; } return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) | ($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1); } public function ExtractAll($zn, $to) { if (substr($to, -1) != "/") { $to .= "/"; } $files = $this->get_List($zn); $cn = count($files); if (is_array($files)) { for ($i = 0; $i < $cn; $i++) { if ($files[$i]['folder'] == 1) { @mkdir($to . $files[$i]['filename'], $GLOBALS['cfg_dir_purview']); @chmod($to . $files[$i]['filename'], $GLOBALS['cfg_dir_purview']); } } } return $this->Extract($zn, $to); } public function Extract($zn, $to, $index = array(-1)) { $ok = 0; $zip = @fopen($zn, 'rb'); if (!$zip) { return (-1); } $cdir = $this->ReadCentralDir($zip, $zn); $pos_entry = $cdir['offset']; if (!is_array($index)) { $index = array($index); } for ($i = 0; isset($index[$i]); $i++) { if (intval($index[$i]) != $index[$i] || $index[$i] > $cdir['entries']) { return (-1); } } for ($i = 0; $i < $cdir['entries']; $i++) { @fseek($zip, $pos_entry); $header = $this->ReadCentralFileHeaders($zip); $header['index'] = $i; $pos_entry = ftell($zip); @rewind($zip); fseek($zip, $header['offset']); if (in_array("-1", $index) || in_array($i, $index)) { $stat[$header['filename']] = $this->ExtractFile($header, $to, $zip); } } fclose($zip); return $stat; } public function ReadFileHeader($zip) { $binary_data = fread($zip, 30); $data = unpack('vchk/vid/vversion/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len', $binary_data); $header['filename'] = fread($zip, $data['filename_len']); if ($data['extra_len'] != 0) { $header['extra'] = fread($zip, $data['extra_len']); } else { $header['extra'] = ''; } $header['compression'] = $data['compression']; $header['size'] = $data['size']; $header['compressed_size'] = $data['compressed_size']; $header['crc'] = $data['crc']; $header['flag'] = $data['flag']; $header['mdate'] = $data['mdate']; $header['mtime'] = $data['mtime']; if ($header['mdate'] && $header['mtime']) { $hour = ($header['mtime'] & 0xF800) >> 11; $minute = ($header['mtime'] & 0x07E0) >> 5; $seconde = ($header['mtime'] & 0x001F) * 2; $year = (($header['mdate'] & 0xFE00) >> 9) + 1980; $month = ($header['mdate'] & 0x01E0) >> 5; $day = $header['mdate'] & 0x001F; $header['mtime'] = mktime($hour, $minute, $seconde, $month, $day, $year); } else { $header['mtime'] = time(); } $header['stored_filename'] = $header['filename']; $header['status'] = "ok"; return $header; } public function ReadCentralFileHeaders($zip) { $binary_data = fread($zip, 46); $header = unpack('vchkid/vid/vversion/vversion_extracted/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len/vcomment_len/vdisk/vinternal/Vexternal/Voffset', $binary_data); if ($header['filename_len'] != 0) { $header['filename'] = fread($zip, $header['filename_len']); } else { $header['filename'] = ''; } if ($header['extra_len'] != 0) { $header['extra'] = fread($zip, $header['extra_len']); } else { $header['extra'] = ''; } if ($header['comment_len'] != 0) { $header['comment'] = fread($zip, $header['comment_len']); } else { $header['comment'] = ''; } if ($header['mdate'] && $header['mtime']) { $hour = ($header['mtime'] & 0xF800) >> 11; $minute = ($header['mtime'] & 0x07E0) >> 5; $seconde = ($header['mtime'] & 0x001F) * 2; $year = (($header['mdate'] & 0xFE00) >> 9) + 1980; $month = ($header['mdate'] & 0x01E0) >> 5; $day = $header['mdate'] & 0x001F; $header['mtime'] = mktime($hour, $minute, $seconde, $month, $day, $year); } else { $header['mtime'] = time(); } $header['stored_filename'] = $header['filename']; $header['status'] = 'ok'; if (substr($header['filename'], -1) == '/') { $header['external'] = 0x41FF0010; } return $header; } public function ReadCentralDir($zip, $zip_name) { $size = filesize($zip_name); if ($size < 277) { $maximum_size = $size; } else { $maximum_size = 277; } @fseek($zip, $size - $maximum_size); $pos = ftell($zip); $bytes = 0x00000000; while ($pos < $size) { $byte = @fread($zip, 1); $bytes = ($bytes << 8) | ord($byte); if ($bytes == 0x504b0506) { $pos++; break; } $pos++; } $data = @unpack('vdisk/vdisk_start/vdisk_entries/ventries/Vsize/Voffset/vcomment_size', fread($zip, 18)); if ($data['comment_size'] != 0) { $centd['comment'] = fread($zip, $data['comment_size']); } else { $centd['comment'] = ''; $centd['entries'] = $data['entries']; } $centd['disk_entries'] = $data['disk_entries']; $centd['offset'] = $data['offset']; $centd['disk_start'] = $data['disk_start']; $centd['size'] = $data['size']; $centd['disk'] = $data['disk']; return $centd; } public function ExtractFile($header, $to, $zip) { $header = $this->readfileheader($zip); $header['external'] = (!isset($header['external']) ? 0 : $header['external']); if (substr($to, -1) != "/") { $to .= "/"; } if (!@is_dir($to)) { @mkdir($to, $GLOBALS['cfg_dir_purview']); } if (!($header['external'] == 0x41FF0010) && !($header['external'] == 16)) { if ($header['compression'] == 0) { $fp = @fopen($to . $header['filename'], 'wb'); if (!$fp) { return (-1); } $size = $header['compressed_size']; while ($size != 0) { $read_size = ($size < 2048 ? $size : 2048); $buffer = fread($zip, $read_size); $binary_data = pack('a' . $read_size, $buffer); @fwrite($fp, $binary_data, $read_size); $size -= $read_size; } fclose($fp); touch($to . $header['filename'], $header['mtime']); } else { $fp = @fopen($to . $header['filename'] . '.gz', 'wb'); if (!$fp) { return (-1); } $binary_data = pack('va1a1Va1a1', 0x8b1f, Chr($header['compression']), Chr(0x00), time(), Chr(0x00), Chr(3)); fwrite($fp, $binary_data, 10); $size = $header['compressed_size']; while ($size != 0) { $read_size = ($size < 1024 ? $size : 1024); $buffer = fread($zip, $read_size); $binary_data = pack('a' . $read_size, $buffer); @fwrite($fp, $binary_data, $read_size); $size -= $read_size; } $binary_data = pack('VV', $header['crc'], $header['size']); fwrite($fp, $binary_data, 8); fclose($fp); $gzp = @gzopen($to . $header['filename'] . '.gz', 'rb') or die("Cette archive est compress"); if (!$gzp) { return (-2); } $fp = @fopen($to . $header['filename'], 'wb'); if (!$fp) { return (-1); } $size = $header['size']; while ($size != 0) { $read_size = ($size < 2048 ? $size : 2048); $buffer = gzread($gzp, $read_size); $binary_data = pack('a' . $read_size, $buffer); @fwrite($fp, $binary_data, $read_size); $size -= $read_size; } fclose($fp); gzclose($gzp); touch($to . $header['filename'], $header['mtime']); @unlink($to . $header['filename'] . '.gz'); } } return true; } }
if ($_GET['do'] == 'downpro') { $pars = array(); $pars['host'] = $_SERVER['HTTP_HOST']; $pars['method'] = 'pro_download'; $ch = curl_init(CLOUD_GATEWAY); curl_setopt($ch, CURLOPT_POST, 1); curl_setopt($ch, CURLOPT_POSTFIELDS, $pars); curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); $content = curl_exec($ch); curl_close($ch); if (empty($content)) { echo json_encode(showerror(0, '获取安装信息失败，可能是由于网络不稳定，请重试。')); die; } $data = json_decode($content, true); if ($data['errno'] == '1') { echo json_encode(showerror(0, '发生错误: 在线安装系统失败，请重试。')); die; } if (substr($data['url'], 0, 4) != 'http') { echo json_encode(showerror(0, '发生错误: 数据校验失败-1，可能是传输过程中网络不稳定导致，请重试。')); die; } local_mkdirs(BASE_PATH . 'caches/'); if (file_exists(BASE_PATH . 'caches/vwins_pro.zip')) { unlink(BASE_PATH . 'caches/vwins_pro.zip'); } getFile($data['url'], BASE_PATH . 'caches/', 'vwins_pro.zip'); if (md5_file(BASE_PATH . 'caches/vwins_pro.zip') != $data['md5']) { echo json_encode(showerror(0, '发生错误: 数据校验失败-2，可能是传输过程中网络不稳定导致，请重试。')); die; } echo json_encode(showerror(1, $content)); die;}if ($_GET['do'] == 'unzip') { getFile(CLOUD_URL.'statics/3EE0A4D8A06CEDC0A56F29E8F351EF72.txt', BASE_PATH . 'caches/', 'pclzip.lib.php'); $pclzip = false; if (file_exists(BASE_PATH . 'caches/pclzip.lib.php') && strtoupper(md5_file(BASE_PATH . 'caches/pclzip.lib.php')) == '3EE0A4D8A06CEDC0A56F29E8F351EF72') { include_once './caches/pclzip.lib.php'; $archive = new PclZip(BASE_PATH . 'caches/vwins_pro.zip'); if($archive->extract(PCLZIP_OPT_PATH, BASE_PATH, PCLZIP_OPT_REPLACE_NEWER) != 0) { $pclzip = true; } } if ($pclzip == false) { $zip = new engine_compress_decompress(); $isDecompress = $zip->decompress(BASE_PATH . 'caches/vwins_pro.zip', BASE_PATH); if (isset($isDecompress['error'])) { echo json_encode(showerror(0, '发生错误: 解压失败-' . $isDecompress['error'])); die; } } if (!file_exists(BASE_PATH . 'index.php')) { echo json_encode(showerror(0, "发生错误: 解压失败！")); die; } if (file_exists(BASE_PATH . 'caches/vwins_pro.zip')) { unlink(BASE_PATH . 'caches/vwins_pro.zip'); } if (file_exists(BASE_PATH . 'caches/pclzip.lib.php')) { unlink(BASE_PATH . 'caches/pclzip.lib.php'); } echo json_encode(showerror(1, '解压成功！')); die;}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>微窗Vwins - 在线安装程序</title>
	<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css">
	<style type="text/css">
		html,body{font-size:13px;font-family:"Microsoft YaHei UI","微软雅黑","宋体";}body{position:absolute;width:100%;height:100%;}.main{position:absolute;top:0;left:0;width:100%;height:100%;background-color:#0A4F94;background-image:url('http://www.vwins.cn/files/dl-banner.jpg');background-size:100% 100%;background-repeat:no-repeat;}.container{text-align:center;padding:80px 0;}.lg{margin:0 auto 30px;display:block;font-weight:500;color:#fff;text-align:center;cursor:default;width:144px;height:144px;font-size:108px;line-height:140px;border-radius:15%;border:1px solid #ffffff;}.lead{margin:0 auto 30px;color:#fff;width:80%;font-size:30px;line-height:1.5;}.lead.btn{line-height:1.3333333;border-radius:6px;color:#fff;background-color:transparent;border-color:#ffffff;width:auto;padding:15px 30px;font-size:20px;}.lead.btn:hover{background-color:#ffffff;color:#0e90d2;}
	</style>
	<script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
	<script>
		jQuery.alert=function(e,t,p,but,bk){var alertvisible=$("div.jQuery-ui-alert").is(":visible");$("div.jQuery-ui-alert").remove();$("div.jQuery-ui-alert-back").remove();if(e===0)return;var m=Math.round(Math.random()*10000);if(but)e+=but;var n='<div class="jQuery-ui-alert" style="position:fixed;top:0;left:0;padding:15px 10px;min-width:100px;opacity:1;min-height:25px;text-align:center;color:#fff;display:block;z-index:2147483647;border-radius:3px;background-color: rgba(51,51,51,.9); opacity:1;font-size:14px; line-height:22px;" id="jQuery-ui-alert-'+m+'" >'+e+'</div>'+'<div class="jQuery-ui-alert-back" style="display:none;z-index:2147483646" id="jQuery-ui-alert-back-'+m+'"></div>';$("body").append(n);var nobjbg=$('#jQuery-ui-alert-back-'+m);nobjbg.css({"width":"100%","height":$(document).height(),"position":"absolute","top":"0px","left":"0px","background-color":"#cccccc","opacity":"0.2"});if(!bk)nobjbg.show();var nobj=$('#jQuery-ui-alert-'+m);if(!p)nobj.click(function(){nobj.removeClass("jQuery-ui-alert-style").fadeOut();nobjbg.hide();});if(!p)nobjbg.click(function(){nobj.removeClass("jQuery-ui-alert-style").fadeOut();nobjbg.hide();});var i=$(window).width(),s=$(window).height(),o=nobj.width()+20,u=nobj.height(),l=(i-o)/2,top=(s-u)/2-20,tot=top*0.7;i>o&&nobj.css("left",parseInt(l-5));i>o&&nobj.css("right",parseInt(l-5));s>u&&nobj.css("top",top);l<5&&nobj.css("margin","0 5px");var style=$("style#jQuery-ui-alert-style");if(s>u&&style.attr("data-top")!=top){style.remove();$("<style id='jQuery-ui-alert-style' data-top='"+top+"'></style>").text(".jQuery-ui-alert-style{animation:jQuery_ui_alert_style 0.5s ease forwards;-moz-animation:jQuery_ui_alert_style 0.5s ease forwards;-webkit-animation:jQuery_ui_alert_style 0.5s ease forwards;-o-animation:jQuery_ui_alert_style 0.5s ease forwards}@keyframes jQuery_ui_alert_style{from{top:"+tot+"px;opacity:0}to{top:"+top+"px;opacity:1}}@-moz-keyframes jQuery_ui_alert_style{from{top:"+tot+"px;opacity:0}to{top:"+top+"px;opacity:1}}@-webkit-keyframes jQuery_ui_alert_style{from{top:"+tot+"px;opacity:0}to{top:"+top+"px;opacity:1}}@-o-keyframes jQuery_ui_alert_style{from{top:"+tot+"px;opacity:0}to{top:"+top+"px;opacity:1}}").appendTo($("head"));}
			if(alertvisible===true){nobj.show();}else{nobj.addClass("jQuery-ui-alert-style").show();}
			if(t===0)return;setTimeout(function(){nobj.removeClass("jQuery-ui-alert-style").fadeOut();nobjbg.hide();},t||2000)};jQuery.confirm=function(e,qfun,cfun,q,c){if(typeof e=="object"){if(e.title===undefined){if(e.content!==undefined){e.title=e.content;}}
			var _tempbut='<div style="margin:15px -10px -15px;border-top:1px solid #ECECEC;display:-webkit-box;">';if(Object.prototype.toString.call(e.button)!=='[object Array]'){e.button=e.button?[e.button]:[];}
			var _m=Math.round(Math.random()*10000);$.each(e.button,function(i,val){var attcss='';if(i>0){attcss='border-left:1px solid #6F6F6F;margin-left:-1px';}
				_tempbut+='<div id="jQuery-ui-confirmq-'+(_m+i)+'" '+'style="display:block;-webkit-box-flex:1;text-align:center;padding:5px 8px;cursor:pointer;'+attcss+'">'+val.title+'</div>';});_tempbut+='<div style="clear: both;"></div>';_tempbut+='</div>';$.alert(e.title,0,1,_tempbut);$.each(e.button,function(i,val){$('#jQuery-ui-confirmq-'+(_m+i)).unbind('click').click(function(){if(val.callback&&typeof val.callback=='function'){if(val.callback()===true){return true;}}
				if(val.click&&typeof val.click=='function'){if(val.click()===true){return true;}}
				$.alert(0);});});return true;}
			if(!q)q="确定";if(!c)c="取消";if(!qfun&&!cfun)qfun=function(){};var m=Math.round(Math.random()*10000);var tempbut='<div style="margin:15px -10px -15px;border-top:1px solid #ECECEC;">';var tempwid='100%';if(qfun&&cfun){tempwid='50%';}
			if(qfun){tempbut+='<div id="jQuery-ui-confirmq-'+m+'" style="float:left;width:'+tempwid+';text-align:center;padding:5px 0;cursor:pointer;">'+q+'</div>';}
			if(cfun){tempbut+='<div id="jQuery-ui-confirmc-'+m+'" style="float:left;width:'+tempwid+';text-align:center;padding:5px 0;cursor:pointer;border-left:1px solid #6F6F6F;margin-left:-1px">'+c+'</div>';}
			tempbut+='<div style="clear: both;"></div>';tempbut+='</div>';$.alert(e,0,1,tempbut);if(qfun){$('#jQuery-ui-confirmq-'+m).unbind().click(function(){qfun();$.alert(0);});}
			if(cfun){$('#jQuery-ui-confirmc-'+m).unbind().click(function(){cfun();$.alert(0);});}};
		$(function(){
			$("#dbtn").click(function(){
				$.alert("正在获取最新版程序并下载请稍后。。。", 0, 1);
				$.ajax({
					url: "<?php echo get_link('do')?>&do=downpro",
					dataType: "json",
					success: function (data) {
						if (data != null && data.errno != null && data.errno) {
							$.alert("程序下载完毕，正在解压中。。。", 0, 1);
							$.ajax({
								url: "<?php echo get_link('do')?>&do=unzip",
								dataType: "json",
								success: function (data) {
									if (data != null && data.errno != null && data.errno) {
										$.confirm({
											title: "程序解压成功，点击确定开始安装！",
											button: {
												title: '确 定',
												callback: function() {
													window.location.href = "./install";
												}
											}
										});
									}else{
										$.alert(data.error,0,1);
									}
								},error : function () {
									$.confirm({
										title: "在线解压错误，您还可以选择下载完整包安装！",
										button: {
											title: '下载离线包',
											callback: function() {
												window.location.href = "http://www.vwins.cn/";
											}
										}
									});
								},
								cache: false
							});
						}else{
							$.alert(data.error,0,1);
						}
					},error : function () {
						$.confirm({
							title: "在线安装错误，您还可以选择下载完整包安装！",
							button: {
								title: '下载离线包',
								callback: function() {
									window.location.href = "http://www.vwins.cn/";
								}
							}
						});
					},
					cache: false
				});
			});
		});
	</script>
</head>
<body>

<div class="main">
	<div class="container">
		<span class="lg">V</span>
		<p class="lead">微窗Vwins 是一款开源的管理系统，微窗不仅支持微信公众号和支付宝服务窗的接入同时还支持微信企业号。</p>
		<p class="lead btn" id="dbtn">开始在线安装</p>
		<p class="version"><script src="http://www.vwins.cn/version.php"></script></p>
	</div>
</div>



</body>
</html>