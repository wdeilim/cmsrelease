<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


function tpl_form_aledit($name = '', $value = '', $options = array()) {
    global $_A;
    $s = $sc = '';
    if (!defined('TPL_INIT_ALEDIT')) {
        $sc = '
        <script type="text/javascript" src="'.JS_PATH.'aliedit/jquery.aliedit.js"></script>
        <script type="text/javascript">
        	window.TPL_INIT_AL_USERID = '.intval($_A['al']['userid']).';
        </script>';
        define('TPL_INIT_ALEDIT', true);
    }
    if (empty($name)) $name = "__tpl_form_aledit";
    $showtab = 'text,imagetext,image,video,voice,material';
    $opt = array();
    $opt['name'] = $name;
    $opt['id'] = "aledit_".md5($name);
    $opt['class'] = 'form-control';
    if ($options === 1) $options = array('data-link'=>1);
    if ($options) {
        if (is_array($options)) {
            $opt = array_merge($opt, $options);
            if (isset($opt['tab'])) {
                $showtab = $opt['tab'];
                unset($opt['tab']);
            }
        }else{
            $showtab = $options;
        }
    }
    $_option = '';
    foreach ($opt AS $k=>$v) {
        $_option.= ' '.$k.'="'.$v.'" ';
    }
    if (!is_array($value)) $value = array();
    if (!isset($value['imagetext'])) $value['imagetext'] = '';
    $s.= '<textarea '.$_option.'>'.value($value,'text').'</textarea>';
    $sc.= '<script type="text/javascript">
        $(function(){ $("#'.$opt['id'].'").aliedit({home: "'.$_A['url']['index'].'", BASE_URI: "'.BASE_URI.'", IMG_PATH: "'.IMG_PATH.'", alid: "'.$_A['al']['id'].'", value: '.json_encode($value).', showtab: "'.$showtab.'"}); });
    </script>';
    if ($name == "__tpl_form_aledit") {
        $s = '<div style="display:none">'.$s.'</div>';
        $sc.= '<script type="text/javascript">
        $.fn.tpl_form_aledit = function() {
            this.aliedit({home: "'.$_A['url']['index'].'", BASE_URI: "'.BASE_URI.'", IMG_PATH: "'.IMG_PATH.'", alid: "'.$_A['al']['id'].'", value: '.json_encode($value).', showtab: "'.$showtab.'"});
        };
        </script>';

    }
    return $s.$sc;
}

function tpl_form_image($name, $value = '', $default = '', $options = array(), $callback = null, $hidepreview = false) {
    global $_A;
    $s = '';
    if (!defined('TPL_INIT_IMAGE')) {
        $s = '
        <script type="text/javascript" src="'.JS_PATH.'formfile/jquery.formfile.js"></script>
		<script type="text/javascript">
		    window.TPL_INIT_IMAGE = true;
		    window.TPL_INIT_AL_USERID = '.intval($_A['al']['userid']).';
			function showImageDialog(elm, opts, options, callback) {
			    var btn = $(elm);
			    var ipt = btn.parent().prev();
                var val = ipt.val();
                var img = ipt.parent().next().children();
			    $.formfile(function(url){
			        if(url.path){
			            ipt.val(url.path);
                        ipt.attr("path",url.path);
                        if (callback) {
                            eval(callback);
                        }
			        }
			        if(url.fullpath){
                        if(img.length > 0){
                            img.get(0).src = url.fullpath;
                        }
                        ipt.attr("url",url.fullpath);
                    }
			    }, val, "'.$_A['url']['index'].'");
			}
			function deleteImageDialog(elm, callback){
                $(elm).prev().attr("src", "'.IMG_PATH.'nopic.jpg");
                $(elm).parent().prev().find("input").val("");
                if (callback) {
                    eval(callback);
                }
			}
			function onblurImageDialog(elm) {
			    var btn = $(elm);
			    var img = btn.parent().next().children();
			    if(img.length > 0){
					var val = btn.val();
					if (val.substring(0,12) == "uploadfiles/") { val = "'.BASE_URI.'"+val; }
					else if (val.substring(0,1) == "/" && val.substring(0,4) != "http") { val = "'.BASE_URI.'"+val; }
					if (!val) val = "'.IMG_PATH.'nopic.jpg";
					img.attr("src", val);
			    }
			}
		</script>';
        define('TPL_INIT_IMAGE', true);
    }
    if(empty($default)) {
        $default = IMG_PATH.'nopic.jpg';
    }
    $val = $default;
    if(!empty($value)) {
        $val = fillurl($value);
    }
    if(empty($options['tabs'])){
        $options['tabs'] = array('browser'=>'active', 'upload'=>'');
    }
    if(empty($options['width'])) {
        $options['width'] = 800;
    }
    if(empty($options['height'])) {
        $options['height'] = 600;
    }
    if(!empty($options['global'])){
        $options['global'] = true;
    } else {
        $options['global'] = false;
    }
    if(empty($options['class_extra'])) {
        $options['class_extra'] = '';
    }

    $options = array_elements(array('width', 'height', 'extras', 'global', 'class_extra', 'tabs'), $options);

    $s .= '
<div class="input-group formfile-inputbox '. $options['class_extra'] .'">
	<input type="text" id="'.$name.'" name="'.$name.'" value="'.$value.'"'.($options['extras']['text'] ? $options['extras']['text'] : '').' class="form-control" onblur="onblurImageDialog(this)" autocomplete="off"><span class="input-group-btn"><button class="btn-default" type="button" onclick="showImageDialog(this, \'' . base64_encode(serialize($options)) . '\', '. str_replace('"','\'', json_encode($options)).', \''.$callback.'\');">选择图片</button></span>
</div>';
    if(!empty($options['tabs']['browser']) || !empty($options['tabs']['upload'])){
		$hidepreviewsty = '';
		if ($hidepreview) {
			$hidepreviewsty = 'display:none;';
		}
		$s .=
			'<div class="input-group formfile-inputbox '. $options['class_extra'] .'" style="margin-top:.5em;'.$hidepreviewsty.'">
	<img src="' . $val . '" onerror="this.src=\''.$default.'\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail" width="150" '.($options['extras']['image'] ? $options['extras']['image'] : '').'/><em class="close" title="删除这张图片" onclick="deleteImageDialog(this, \''.$callback.'\');">×</em>
</div>';
    }
    return $s;
}

function tpl_form_daterange($name = '', $value = array(), $time = false, $datelimit = 0)
{
	$s = '';
	if (!defined('TPL_INIT_DATERANGEPICKER')) {
		$s.= '
		<link rel="stylesheet" href="'.JS_PATH.'daterangepicker/daterangepicker.css"/>
        <script type="text/javascript" src="'.JS_PATH.'daterangepicker/moment.js"></script>
        <script type="text/javascript" src="'.JS_PATH.'daterangepicker/daterangepicker.js"></script>';
		define('TPL_INIT_DATERANGEPICKER', true);
	}
	if (!defined('TPL_INIT_DATERANGE')) {
		$s.= '
		<script type="text/javascript">
			$(document).ready(function() {
			  	$(".daterange.daterange-date").each(function(){
					var elm = this;
					var elmcon = {
						startDate: $(elm).prev().prev().val(),
						endDate: $(elm).prev().val(),
						format: "YYYY-MM-DD"
					};
					var limit = parseInt($(elm).attr("data-limit"));
					if (limit > 0) { elmcon.dateLimit = {days: limit}; }
					$(this).daterangepicker(elmcon, function(start, end){
						$(elm).find(".date-title").html(start.toDateStr() + " 至 " + end.toDateStr());
						$(elm).prev().prev().val(start.toDateStr());
						$(elm).prev().val(end.toDateStr());
					});
				});
				$(".daterange.daterange-time").each(function(){
					var elm = this;
					var elmcon = {
						startDate: $(elm).prev().prev().val(),
						endDate: $(elm).prev().val(),
						format: "YYYY-MM-DD HH:mm",
						timePicker: true,
						timePicker12Hour : false,
						timePickerIncrement: 1,
						minuteStep: 1
					};
					var limit = parseInt($(elm).attr("data-limit"));
					if (limit > 0) { elmcon.dateLimit = {days: limit}; }
					$(this).daterangepicker(elmcon, function(start, end){
						$(elm).find(".date-title").html(start.toDateTimeStr() + " 至 " + end.toDateTimeStr());
						$(elm).prev().prev().val(start.toDateTimeStr());
						$(elm).prev().val(end.toDateTimeStr());
					});
				});
			});
		</script>';
		define('TPL_INIT_DATERANGE', true);
	}
	$value = string2array($value);

	$value['start'] = empty($value['start']) ? SYS_TIME : $value['start'];
	$value['end'] = empty($value['end']) ? SYS_TIME : $value['end'];

	$options = $time?"Y-m-d H:i:s":"Y-m-d";
	if (is_numeric($value['start'])) $value['start'] = date($options, $value['start']);
	if (is_numeric($value['end'])) $value['end'] = date($options, $value['end']);

	$ranid = "daterange_".md5($name);

	$s.= '<input name="'.$name . '[start]'.'" type="hidden" value="'. $value['start'].'" />';
	$s.= '<input name="'.$name . '[end]'.'" type="hidden" value="'. $value['end'].'" />';
	$s.= '<button class="btn btn-default daterange '.(!empty($time) ? 'daterange-time' : 'daterange-date').'" data-limit="'.(($datelimit>0)?$datelimit:0).'" type="button"><span class="date-title">'.$value['start'].' 至 '.$value['end'].'</span> <i class="fa fa-calendar"></i></button>';
	return $s;
}

function tpl_form_coordinate($name = '', $value = '', $options = array())
{
	$s = '';
	if (!is_array($value)) $value = array();
	if (!defined('TPL_INIT_COORDINATE')) {
		$s.= '<script type="text/javascript" src="'.JS_PATH.'baidu_map/jquery.baidu_map.js"></script><script type="text/javascript" src="http://api.map.baidu.com/api?v=1.1&key=KMAaKTqxQPZ6bDpp8NeKK7uu&services=true"></script>';
        define('TPL_INIT_COORDINATE', true);
	}
	if ($options) {
		$opt = array();
		$opt['name'] = $name;
		$opt['id'] = $name;
		$opt['type'] = 'text';
		$opt['class'] = 'form-control';
		if (is_array($options)) {
			$opt = array_merge($opt, $options);
		}
		$_option = '';
		foreach ($opt AS $k=>$v) {
			$_option.= ' '.$k.'="'.$v.'" ';
		}
		$s.= '<input '.$_option.'/>';
	}
	if (strpos($name, '$') === false) $name = '$("#'.$name.'")';
	$s.= '<script type="text/javascript"> $(function(){ '.$name.'.baidu_map('.json_encode($value).');}); </script>';
	return $s;
}