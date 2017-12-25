<?php
function smarty_function_ddb_contracts($params, &$smarty)
{
    $CI =& get_instance();
    $db = $CI->ddb;
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
            case "ID列表":
                $aset['listname_id'] = $a[1];
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
            case "排序":
                $aset['order'] = $a[1];
                break;
        }
    }
    if (is_array($aset)) $aset=array_map("get_smarty_request",$aset);
    $aset['listname']=isset($aset['listname'])?$aset['listname']:"list";
    $aset['row']=isset($aset['row'])?intval($aset['row']):10;
    $aset['start']=isset($aset['start'])?intval($aset['start']):0;
    $aset['titlelen']=isset($aset['titlelen'])?intval($aset['titlelen']):15;
    $aset['pagename']=isset($aset['pagename'])?$aset['pagename']:'page';
    $aset['page_url']=isset($aset['page_url'])?$aset['page_url']:'(?)';
    $aset['page_html']=isset($aset['page_html'])?$aset['page_html']:'html';
    $aset['page_now']=isset($aset['page_now'])?$aset['page_now']:1;
    $aset['listname_id']=isset($aset['listname_id'])?$aset['listname_id']:"";
    $aset['order']=isset($aset['order'])?$aset['order']:"";
    $params['where']=isset($params['where'])?$params['where']:"";
    //
    if ($aset['page_now'] < 1) $aset['page_now'] = 1;
    //
    $orderbysql = $aset['order']?' ORDER BY '.str_replace(">", ",", $aset['order']):'';
    $wheresql = "";
    if ($params['where']) {
        $wheresql.= " AND ".$params['where'];
    }
    if (!empty($wheresql)) {
        $wheresql = str_replace('{SITE}','a.site='.intval(SYS_SITE), $wheresql);
        $wheresql = " WHERE ".ltrim(ltrim($wheresql),'AND');
    }
    if (isset($aset['paged']))
    {
        $total_sql="SELECT COUNT(*) AS num FROM ".table('contracts')." a INNER JOIN ".table('users_wx')." b ON a.wxid=b.id ".$wheresql;
        $total_count=$db->get_total($total_sql);
        $page_now = ceil($total_count / $aset['row']);
        $aset['page_now'] = ($aset['page_now']>$page_now)?$page_now:$aset['page_now'];
        $CI =& get_instance();
        $params = array(
            'total_rows'=>$total_count, #总数量
            'method'    =>$aset['page_html'], #(必须)地址类型
            'parameter' =>$aset['page_url'],  #url规则
            'now_page'  =>$aset['page_now'],  #当前页
            'total_page' =>ceil($total_count / $aset['row']), #总页数
            'list_rows' =>$aset['row'], #每页显示
        );
        $CI->load->library('page', $params);
        $aset['start']=($aset['page_now']-1)*$aset['row'];
        $smarty->assign($aset['pagename'], $CI->page->show(2));
        $smarty->assign($aset['pagename'].'_info',$params);
    }
    $limit = ($aset['row'] == -1)?"":" LIMIT ".abs($aset['start']).','.$aset['row'];
    $result = $db->query("SELECT a.*,a.id AS ida,a.indate AS indatea,b.* FROM ".table('contracts')." a INNER JOIN ".table('users_wx')." b ON a.wxid=b.id ".$wheresql.$orderbysql.$limit);
    $list= array();
    $__n= 1;$_timearrid = '';
    foreach($result->result_array() as $row){
        $_timearrid.= ($aset['listname_id'])?$row['id'].',':'';
        $row['_n']=(isset($aset['paged']))?$__n+($aset['page_now']*$aset['row'])-$aset['row']:$__n;
        //
        $row['function'] = string2array($row['function']);
        if (!isset($row['function'][$row['functionsid']])) {
            //合同已经删除
            $row['fun_arr'] = string2array($row['setting']);
            $row['fun_arr']['statusa'] = '已失效';
        }else{
            $row['fun_arr'] = $row['function'][$row['functionsid']];
        }
        $__n ++;
        $list[] = $row;
    }
    if ($aset['listname_id']) $smarty->assign($aset['listname_id'],rtrim($_timearrid,','));
    $smarty->assign($aset['listname'],$list);
}
?>