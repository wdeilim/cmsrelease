<?php
function smarty_function_ddb_pc($params, &$smarty)
{
    $CI =& get_instance();
    $params['set'] = str_replace("\,", "\u002c", $params['set']);
    $arr=explode(',',$params['set']);
    foreach($arr as $str)
    {
        $str = str_replace("\u002c", ",", $str);
        $a = explode(':', $str);
		if (count($a) > 2) $a[1] = substr($str, strlen($a[0]) + 1);
        switch ($a[0])
        {
            case "列表名":
                $aset['listname'] = $a[1];
                break;
            case "用户表":
                $aset['usertable'] = $a[1];
                break;
            case "ID列表":
                $aset['listname_id'] = $a[1];
                break;
            case "ID列名":
                $aset['listid'] = $a[1];
                break;
            case "列名":
                $aset['fieldname'] = $a[1];
                break;
            case "显示数目":
                $aset['row'] = $a[1];
                break;
            case "开始位置":
                $aset['start'] = $a[1];
                break;
            case "分页显示":
                $aset['paged'] = $a[1];
                break;
            case "分页名":
                $aset['pagename'] = $a[1];
                break;
            case "分页地址":
                $aset['page_url'] = $a[1];
                break;
            case "分页类型":
                $aset['page_html'] = $a[1];
                break;
            case "当前页":
                $aset['page_now'] = $a[1];
                break;
            case "数据表":
                $aset['tabledb'] = $a[1];
                break;
            case "排序":
                $aset['order'] = $a[1];
                break;
        }
    }
    if (isset($aset) && is_array($aset)) $aset=array_map("get_smarty_request",$aset);
    $aset['listname']       = isset($aset['listname'])?$aset['listname']:"list";
    $aset['row']            = isset($aset['row'])?intval($aset['row']):10;
    $aset['start']          = isset($aset['start'])?intval($aset['start']):0;
    $aset['titlelen']       = isset($aset['titlelen'])?intval($aset['titlelen']):15;
    $aset['pagename']       = isset($aset['pagename'])?$aset['pagename']:'page';
    $aset['page_url']       = isset($aset['page_url'])?$aset['page_url']:'(?)';
    $aset['page_html']      = isset($aset['page_html'])?$aset['page_html']:'html';
    $aset['page_now']       = isset($aset['page_now'])?$aset['page_now']:1;
    $aset['listid']         = isset($aset['listid'])?$aset['listid']:"id";
    $aset['listname_id']    = isset($aset['listname_id'])?$aset['listname_id']:"";
    $aset['order']          = isset($aset['order'])?$aset['order']:"";
    $params['where']        = isset($params['where'])?$params['where']:"";
    $tablefun = isset($aset['usertable'])?'tableal':'table';
    //
    if ($aset['page_now'] < 1) $aset['page_now'] = 1;
    //
    $orderbysql = $aset['order']?' ORDER BY '.str_replace(">", ",", $aset['order']):'';
    $wheresql = "";
    if ($params['where']) {
        preg_match_all('/LIKE\s*[\'|\"]%+([^>]*?)%[\'|\"]/is', $params['where'], $matchew);
        foreach($matchew[1] AS $key=>$item) {
            $params['where'] = str_replace($matchew[0][$key], "LIKE '%".db_escape_str($item)."%'", $params['where']);
        }
        $wheresql.= " AND ".$params['where'];
    }
    if (!empty($wheresql)) {
        $wheresql = " WHERE ".ltrim(ltrim($wheresql),'AND');
    }
    if (isset($aset['paged']))
    {
        $total_sql = "SELECT COUNT(*) AS num FROM ".$tablefun($aset['tabledb']).$wheresql;
        $total_count = db_total($total_sql);
        $page_now = ceil($total_count / $aset['row']);
        $aset['page_now'] = ($aset['page_now']>$page_now)?$page_now:$aset['page_now'];
        $params = array(
            'total_rows'=>$total_count, #总数量
            'method'    =>$aset['page_html'], #(必须)地址类型
            'parameter' =>$aset['page_url'],  #url规则
            'now_page'  =>$aset['page_now'],  #当前页
            'total_page' =>ceil($total_count / $aset['row']), #总页数
            'list_rows' =>$aset['row'], #每页显示
        );
        $CI->load->library('page', $params);
        $CI->page->config($params);
        $aset['start']=($aset['page_now']-1)*$aset['row'];
        $smarty->assign($aset['pagename'], $CI->page->show(2));
        $smarty->assign($aset['pagename'].'_info',$params);
    }
    $limit = ($aset['row'] == -1)?"":" LIMIT ".abs($aset['start']).','.$aset['row'];
    if (!empty($aset['fieldname'])){
        $aset['fieldname'] = str_replace("|", ",", $aset['fieldname']);
        $result = db_query("SELECT {$aset['fieldname']} FROM ".$tablefun($aset['tabledb'])." ".$wheresql.$orderbysql.$limit);
    }else{
        $result = db_query("SELECT * FROM ".$tablefun($aset['tabledb'])." ".$wheresql.$orderbysql.$limit);
    }
    $list= array();
    $__n= 1;$_timearrid = '';
    foreach($result->result_array() as $row){
        $_timearrid.= ($aset['listname_id'])?$row[$aset['listid']].',':'';
        $row['_n']=(isset($aset['paged']))?$__n+($aset['page_now']*$aset['row'])-$aset['row']:$__n;
        $__n ++;
        $list[] = $row;
    }
    if ($aset['listname_id']) $smarty->assign($aset['listname_id'],rtrim($_timearrid,','));
    $smarty->assign($aset['listname'],$list);
}
?>