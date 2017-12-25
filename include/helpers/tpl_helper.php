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

function tpl_form_imagemore($name, $value = '', $max = 10, $default = '', $options = array(), $callback = null, $hidepreview = false) {
	global $_A;
	if (empty($default)) { $default = IMG_PATH.'nopic.jpg'; }
	$s = '';
	$s.= __tpl_load_cssjs(JS_PATH.'formfile/jquery.formfile.js');
	if (!defined('TPL_INIT_IMAGEMORE')) {
		$s.= '
		<script type="text/javascript">
		    window.TPL_INIT_IMAGEMORE = true;
		    window.TPL_INIT_AL_USERID = '.intval($_A['al']['userid']).';
			function showImageDialogmore(elm, opts, options, callback) {
			    var btn = $(elm);
			    var imgbox = btn.parents(".formfile-inputbox").next();
                var val = {};
                imgbox.find("input").each(function(i){
					if ($(this).val()) {
						val[$(this).val()] = imgbox.find("img").eq(i).attr("src");
					}
				});
			    $.formfilemore(function(data){
			    	imgbox.html("");
			    	var j = 1;
			    	$.each(data.images, function(n,v) {
						if (v != "" && j <= parseInt(btn.attr("data-num"))) {
							j++;
							imgbox.append(\'<div class="inimem"><input type="hidden" name="\'+btn.attr("data-name")+\'[]" value="\'+n+\'" autocomplete="off"><img src="\'+v+\'" onerror="this.src=\\\''.$default.'\\\'; this.title=\\\'图片未找到.\\\'" class="img-responsive img-thumbnail" width="150" '.($options['extras']['image'] ? $options['extras']['image'] : '').'/><em class="close" title="删除这张图片" onclick="deleteImageDialogmore(this, \\\''.$callback.'\\\');">×</em></div>\');
						}
					});
					if (callback) {
						eval(callback);
					}
			    }, val, "'.$_A['url']['index'].'", parseInt(btn.attr("data-num")));
			}
			function deleteImageDialogmore(elm, callback){
				if ($(elm).parents(".formfile-inputbox").find("input").length > 1) {
                	$(elm).parent(".inimem").remove();
				}else{
					$(elm).prev().attr("src", "'.$default.'");
                	$(elm).prev().prev().remove();
				}
                if (callback) {
                    eval(callback);
                }
			}
		</script>';
		define('TPL_INIT_IMAGEMORE', true);
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

	$valuearr = $value;
	if (!is_array($value)) {
		$valuearr = array($value);
	}
	$valbox = '';
	foreach($valuearr AS $item) {
		if ($item) {
			$valbox.= '<div class="inimem"><input type="hidden" name="'.$name.'[]" value="'.$item.'" autocomplete="off"><img src="'.fillurl($item).'" onerror="this.src=\''.$default.'\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail" width="150" '.($options['extras']['image'] ? $options['extras']['image'] : '').'/><em class="close" title="删除这张图片" onclick="deleteImageDialogmore(this, \''.$callback.'\');">×</em></div>';
		}
	}
	if (empty($valbox)) {
		$valbox.= '<div class="inimem"><img src="'.$default.'" onerror="this.src=\''.$default.'\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail" width="150" '.($options['extras']['image'] ? $options['extras']['image'] : '').'/><em class="close" title="删除这张图片" onclick="deleteImageDialogmore(this, \''.$callback.'\');">×</em></div>';
	}

	$s .= '
<div class="input-group formfile-inputbox cf '. $options['class_extra'] .'">
	<input type="text" id="'.$name.'" value="" placeholder="批量上传图片"'.($options['extras']['text'] ? $options['extras']['text'] : '').' class="form-control" autocomplete="off" disabled><span class="input-group-btn"><button data-name="'.$name.'" data-num="'.intval($max).'" class="btn-default" type="button" onclick="showImageDialogmore(this, \'' . base64_encode(serialize($options)) . '\', '. str_replace('"','\'', json_encode($options)).', \''.$callback.'\');">选择图片</button></span>
</div>';
	if(!empty($options['tabs']['browser']) || !empty($options['tabs']['upload'])){
		$hidepreviewsty = '';
		if ($hidepreview) {
			$hidepreviewsty = 'display:none;';
		}
		$s .= '<div class="input-group formfile-inputbox cf '. $options['class_extra'] .'" style="margin-top:.5em;'.$hidepreviewsty.'">'.$valbox.'</div>';
	}
	return $s;
}

function tpl_form_image($name, $value = '', $default = '', $options = array(), $callback = null, $hidepreview = false) {
    global $_A;
	if (empty($default)) { $default = IMG_PATH.'nopic.jpg'; }
    $s = '';
	$s.= __tpl_load_cssjs(JS_PATH.'formfile/jquery.formfile.js');
    if (!defined('TPL_INIT_IMAGE')) {
        $s.= '
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
                $(elm).prev().attr("src", "'.$default.'");
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
					else if (val.substring(0,1) != "/" && val.substring(0,4) != "http") { val = "'.BASE_URI.'"+val; }
					if (!val) val = "'.$default.'";
					img.attr("src", val);
			    }
			}
		</script>';
        define('TPL_INIT_IMAGE', true);
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
	$s.= __tpl_load_cssjs(JS_PATH.'daterangepicker/daterangepicker.css', true);
	$s.= __tpl_load_cssjs(JS_PATH.'daterangepicker/moment.js');
	$s.= __tpl_load_cssjs(JS_PATH.'daterangepicker/daterangepicker.js');
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

	$s.= '<input name="'.$name . '[start]'.'" type="hidden" value="'. $value['start'].'" />';
	$s.= '<input name="'.$name . '[end]'.'" type="hidden" value="'. $value['end'].'" />';
	$s.= '<button class="btn btn-default daterange '.(!empty($time) ? 'daterange-time' : 'daterange-date').'" data-limit="'.(($datelimit>0)?$datelimit:0).'" type="button"><span class="date-title">'.$value['start'].' 至 '.$value['end'].'</span> <i class="fa fa-calendar"></i></button>';
	return $s;
}


function tpl_form_date($name, $value = '', $withtime = false) {
	$html = '';
	$html.= __tpl_load_cssjs(JS_PATH.'datetimepicker/bootstrap-datetimepicker.min.css', true);
	$html.= __tpl_load_cssjs(JS_PATH.'datetimepicker/bootstrap-datetimepicker.min.js');
	if ($withtime && !defined('TPL_INIT_DATA_TIME')) {
		$html.= '
			<script type="text/javascript">
				$(function(){
					$(".datetimepicker.datetime").each(function(){
						var opt = {
							language: "zh-CN",
							minView: 0,
							autoclose: true,
							format : "yyyy-mm-dd hh:ii",
							todayBtn: true,
							minuteStep: 5
						};
						$(this).datetimepicker(opt);
					});
				});
			</script>';
		define('TPL_INIT_DATA_TIME', true);
	}

	if (!$withtime  && !defined('TPL_INIT_DATA') ) {
		$html.= '
			<script type="text/javascript">
				$(function(){
					$(".datetimepicker.date").each(function(){
						var opt = {
							language: "zh-CN",
							minView: 2,
							format: "yyyy-mm-dd",
							autoclose: true,
							todayBtn: true
						};
						$(this).datetimepicker(opt);
					});
				});
			</script>';
		define('TPL_INIT_DATA', true);
	}

	$class = $withtime ? 'datetime' : 'date';
	$placeholder = $withtime ? '日期时刻' : '日期';
	$value = !empty($value) ? $value : ($withtime ? date('Y-m-d H:i') : date('Y-m-d'));

	$html .= '<input type="text" name="' . $name . '" value="'.$value.'" placeholder="'.$placeholder.'"  readonly="readonly" class="datetimepicker '.$class.' form-control" style="padding:6px 12px;cursor:text;"/>';

	return $html;
}


function tpl_form_calendar($name, $values = array()) {
	$html = '';
	$html.= __tpl_load_cssjs(JS_PATH.'moment.js');
	if (!defined('TPL_INIT_CALENDAR')) {
		$html .= '
		<script type="text/javascript">
			$(function(){
				$(".tpl-calendar").each(function(){
					handlerCalendar($(this).find("select.tpl-year")[0]);
				});
			});
			function handlerCalendar(elm) {
				var tpl = $(elm).parent().parent();
				var year = tpl.find("select.tpl-year").val();
				var month = tpl.find("select.tpl-month").val();
				var day = tpl.find("select.tpl-day");
				day[0].options.length = 1;
				if(year && month) {
					var date = moment(year + "-" + month, "YYYY-M");
					var days = date.daysInMonth();
					for(var i = 1; i <= days; i++) {
						var opt = new Option(i, i);
						day[0].options.add(opt);
					}
					if(day.attr("data-value")!=""){
						day.val(day.attr("data-value"));
					} else {
						day[0].options[0].selected = "selected";
					}
				}
			}
		</script>';
		define('TPL_INIT_CALENDAR', true);
	}

	if (empty($values) || !is_array($values)) {
		if (strtotime($values)){
			$values = strtotime($values);
			$values = array('year'=>date('Y',$values),'month'=>date('m',$values),'day'=>date('d',$values));
		}elseif (date("Y",$values) != '1970'){
			$values = array('year'=>date('Y',$values),'month'=>date('m',$values),'day'=>date('d',$values));
		}else{
			$values = array(0,0,0);
		}
	}
	$values['year'] = intval($values['year']);
	$values['month'] = intval($values['month']);
	$values['day'] = intval($values['day']);

	if (empty($values['year'])) {
		$values['year'] = '1980';
	}
	$year = array(date('Y'), '1914');
	$html .= '<div class="row row-fix tpl-calendar">
			<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
				<select name="' . $name . '[year]" onchange="handlerCalendar(this)" class="form-control tpl-year">
					<option value="">年</option>';
	for($i = $year[1]; $i <= $year[0]; $i++) {
		$html .= '<option value="' . $i . '"'.($i == $values['year'] ? ' selected="selected"' : '').'>' . $i . '</option>';
	}
	$html .= '	</select>
			</div>
			<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
				<select name="' . $name . '[month]" onchange="handlerCalendar(this)" class="form-control tpl-month">
					<option value="">月</option>';
	for($i = 1; $i <= 12; $i++) {
		$html .= '<option value="' . $i . '"'.($i == $values['month'] ? ' selected="selected"' : '').'>' . $i . '</option>';
	}
	$html .= '	</select>
			</div>
			<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
				<select name="' . $name . '[day]" data-value="' . $values['day'] . '" class="form-control tpl-day">
					<option value="0">日</option>
				</select>
			</div>
		</div>';
	return $html;
}


function tpl_form_coordinate($name = '', $value = '', $options = array())
{
	$s = '';
	if (!is_array($value)) $value = array();
	$s.= __tpl_load_cssjs(JS_PATH.'baidu_map/jquery.baidu_map.js');
	$s.= __tpl_load_cssjs('http://api.map.baidu.com/api?v=2.0&ak=eDsGxG65jw27rKR2hGfhRIBp');
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


function __tpl_load_cssjs($path, $iscss = false) {
	static $loadcssjsarr = array();
	$key = md5($path);
	$key.= $iscss?1:0;
	if (isset($loadcssjsarr[$key])) {
		return '';
	}
	$loadcssjsarr[$key] = true;
	if ($iscss) {
		return '<link rel="stylesheet" href="'.$path.'"/>';
	}else{
		return '<script type="text/javascript" src="'.$path.'"></script>';
	}
}