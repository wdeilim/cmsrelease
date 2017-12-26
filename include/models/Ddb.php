<?php
class Ddb extends CI_Model {
    public $skipcheck = array();
    
    public function __construct() {
		parent::__construct();
		$this->load->database();
        $this->load->library('safecheck');
	}

    public function query($sql, $wherearr = array()) {
        $sql.= $this->where_preg($wherearr);
        $sql = $this->checksql($sql);
        return $this->db->query($sql);
    }

    public function query_simple($sql, $wherearr = array()) {
        $sql.= $this->where_preg($wherearr);
        $sql = $this->checksql($sql);
        return $this->db->simple_query($sql);
    }

    public function getone($sql, $wherearr = array(), $ordersql = '') {
        if (!leftexists($sql,'select', true)) {
            $sql = "SELECT * FROM  ".$sql;
        }
        $sql.= $this->where_preg($wherearr);
        if ($ordersql){
            $sql.= " ORDER BY ".$ordersql;
        }
		$query = $this->query($sql);
		return $query->row_array(0);
	}

    public function getall($sql, $wherearr = array(), $ordersql = '') {
        if (!leftexists($sql,'select', true)) {
            $sql = "SELECT * FROM  ".$sql;
        }
        $sql.= $this->where_preg($wherearr);
        if ($ordersql){
            $sql.= " ORDER BY ".$ordersql;
        }
		$query = $this->query($sql);
		return $query->result_array();
	}

    public function get_total($sql, $wherearr = array()) {
        $row = $this->getall($sql, $wherearr);
        $v = 0;
        if (!empty($row) && is_array($row)){
            foreach($row as $n){
                $v=$v+$n['num'];
            }
        }
        return $v;
    }

    public function get_count($sql, $wherearr = array()) {
        if (!leftexists($sql,'select', true)) {
            $sql = "SELECT count(*) AS num FROM  ".$sql;
        }
        return $this->get_total($sql, $wherearr);
    }

    public function insert($table, $data = array(), $retid = false) {
		$_ret = $this->db->insert($table, $data);
		if ($_ret && $retid){
			$_ret = $this->db->insert_id();
		}
		return $_ret;
	}

    public function replace($table, $data = array(), $retid = false) {
		$_ret = $this->db->replace($table, $data);
		if ($_ret && $retid){
			$_ret = $this->db->insert_id();
		}
		return $_ret;
	}

    public function update($table, $data = array(), $where = array()) {
		$newdate = $this->data_preg($data);
		if ($newdate) {
			return $this->query("UPDATE `". $table."` SET ".implode(',', $newdate), $where);
		}else{
			return $this->db->update($table, $data, $where);
		}
	}

    public function delete($table, $where = array(), $glue = 'AND') {
		if (strtolower($glue) == "or") {
			return $this->db->delete($table, $this->db->or_where($where));
		}else{
			return $this->db->delete($table, $where);
		}
	}

    public function run($sql, $stuff = 'es_', $simple = false) {
        if(!isset($sql) || empty($sql)) return;

        $sql = str_replace("\r", "\n", str_replace(' ' . $stuff, ' ' . BASE_DB_FORE, $sql));
        $sql = str_replace("\r", "\n", str_replace(' `' . $stuff, ' `' . BASE_DB_FORE, $sql));
        $ret = array();
        $num = 0;
        foreach(explode(";\n", trim($sql)) as $query) {
            $ret[$num] = '';
            $queries = explode("\n", trim($query));
            foreach($queries as $query) {
                $ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0].$query[1] == '--') ? '' : $query;
            }
            $num++;
        }
        unset($sql);
        foreach($ret as $query) {
            $query = trim($query);
            if($query) {
                if ($simple) {
                    $this->query_simple($query);
                }else{
                    $this->query($query);
                }
            }
        }
    }

    public function run_simple($sql, $stuff = 'es_') {
        $this->run($sql, $stuff, true);
    }

    public function fieldexists($tablename, $fieldname) {
		$isexists = $this->query("DESCRIBE " . $tablename . " `{$fieldname}`");
		$isexists = $isexists->row_array(0);
		return !empty($isexists) ? true : false;
	}

    public function indexexists($tablename, $indexname) {
		if (!empty($indexname)) {
			$indexs = $this->query("SHOW INDEX FROM " . $tablename);
			$indexs = $indexs->result_array();
			if (!empty($indexs) && is_array($indexs)) {
				foreach ($indexs as $row) {
					if ($row['Key_name'] == $indexname) {
						return true;
					}
				}
			}
		}
		return false;
	}

    public function tableexists($table) {
		if(!empty($table)) {
			$data = $this->query("SHOW TABLES LIKE '".$table."'");
			$data = $data->row_array(0);
			if(!empty($data)) {
				$data = array_values($data);
				$tablename = $table;
				if(in_array($tablename, $data)) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

    /**
     * 获取分页列表
     * @param string $table 表名称
     * @param string $where 查询条件，默认空
     * @param string $order 排序方式，默认空
     * @param int $row 每页显示，默认10
     * @param int $page 当前页，默认1
     * @param string $field 读取字段名称
     * @return array (total=>总数量,perpage=>每页显示,nowpage=>当前页,totalpage=>总页数,list=>数据列表)
     */
    public function getlist($table, $where='', $order='', $row=10, $page=1, $field='*') {
		if (empty($table)) return array();
        if (!empty($order)) $order = " ORDER BY ".$order;
        if (!empty($where)) {
            if (is_array($where)){
                $_where = " WHERE ";
				$_where.= " 1 ";
                foreach ($where as $key=>$val) {
					$_where.= ("i".$val == "i".intval($val))?" AND `{$key}`={$val}":" AND `{$key}`='{$val}'";
                }
                $where = $_where;
            }else{
                $where = " WHERE ".$where;
            }
        }
        $total_sql = "SELECT COUNT(*) AS num FROM ".$table." ".$where;
		$total_count = $this->get_total($total_sql);
		$totalpage = $total_count / $row;
		$totalpage = ($totalpage > intval($totalpage))?intval($totalpage+1):intval($totalpage);
		if ($page > $totalpage) $page = $totalpage;
		if ($page < 1) $page = 1;
		$pagearr = array(
			'total'=>$total_count, //总数
			'perpage'=>$row, //每页显示
			'nowpage'=>$page, //当前页
			'totalpage'=>$totalpage, //总页数
		);
		$start = ($page-1)*$row;
		$limit=" LIMIT ".abs($start).','.$row;
		$sql="SELECT ".$field." FROM ".$table." ".$where." ".$order." ".$limit;
		$query = $this->query($sql);
		$list= array();
		$__n= 1;
		foreach ($query->result_array() as $rows){
			$rows['_n']=$__n+($page*$row)-$row;
			$__n ++;
			$list[] = $rows;
		}
		$pagearr['list'] = $list;
		return $pagearr;
	}

    public function trans_start() {
        $this->db->trans_start();
    }

    public function trans_complete() {
        $this->db->trans_complete();
    }

    public function escape($str) {
        return $this->db->escape($str);
    }

    public function escape_str($str) {
        return $this->db->escape_str($str);
    }

    public function escape_like_str($str) {
        return $this->db->escape_like_str($str);
    }

    public function escape_identifiers($str) {
        return $this->db->escape_identifiers($str);
    }

    public function addcheck($string) {
        if (is_array($string)) {
            foreach ($string AS $item) {
                $this->addcheck($item);
            }
        }elseif ($string){
            $this->skipcheck[md5($string)] = $string;
        }
        return $this->skipcheck;
    }

    public function checksql($db_string) {
        $db_string_bak = $db_string;
        if (defined('OFF_SQL_TEMP') && OFF_SQL_TEMP < time()) {
            $this->safecheck->checkquery($db_string, $this->skipcheck);
        }
        return $db_string_bak;
    }

    protected function data_preg($data) {
        if (empty($data)) return $data;
        $fields = array();
        $isfields = false;
        foreach ($data as $key => $value) {
            preg_match('/([\w]+)(\[(\+|\-|\*|\/)\])?/i', $key, $match);
            if (isset($match[3])) {
                if (is_numeric($value)) {
                    $fields[] = $this->column_quote($match[1]) . ' = ' . $this->column_quote($match[1]) . ' ' . $match[3] . ' ' . $value;
                    $isfields = true;
                }
            }else{
                $column = $this->column_quote($key);
                switch (gettype($value)){
                    case 'NULL':
                        $fields[] = $column . ' = NULL';
                        break;
                    case 'array':
                        preg_match("/\(JSON\)\s*([\w]+)/i", $key, $column_match);
                        if (isset($column_match[0])) {
                            $fields[] = $this->column_quote($column_match[1]) . ' = ' . json_encode($value);
                        }else{
                            $fields[] = $column . ' = ' . serialize($value);
                        }
                        break;
                    case 'boolean':
                        $fields[] = $column . ' = ' . ($value ? '1' : '0');
                        break;
                    case 'integer':
                    case 'double':
                    case 'string':
                        if ("i".$value == "i".intval($value)) {
                            $fields[] = $column . ' = ' . $value;
                        }else{
                            $fields[] = $column . " = '" . $this->escape_str($value) . "'";
                        }
                        break;
                }
            }
        }
        return $isfields?$fields:array();
    }

    protected function column_quote($string) {
        return '`' . str_replace('.', '"."', preg_replace('/(^#|\(JSON\))/', '', $string)) . '`';
    }

    protected function _has_operator($str) {
        return (bool) preg_match('/(<|>|!|=|\sIS NULL|\sIS NOT NULL|\sEXISTS|\sBETWEEN|\sLIKE|\sIN\s*\(|\s)/i', trim($str));
    }

    protected function where_preg($key, $havewhere = true) {
        if (empty($key)) {
            return "";
        }
        if (!is_array($key)) {
            $wheresql = $key;
        }else {
            $wheresql = "";
            foreach ($key as $k => $v) {
                if (!$this->_has_operator($k)) {
                    if (is_null($v)) {
                        $k = '`'.$k.'` IS NULL ';
                    }else{
                        $k = '`'.$k.'` = ';
                    }
                }
                if (!is_null($v) && $v !== intval($v)) {
                    $v = "'".$this->escape_str($v)."'";
                }
                $wheresql.= " AND ".$k.$v." ";
            }
        }
        if ($wheresql) {
            $wheresql = ltrim(ltrim(trim($wheresql), "AND"), "OR");
            if ($havewhere) {
                $wheresql = " WHERE ".$wheresql;
            }
        }
        return $wheresql;
    }
}
?>