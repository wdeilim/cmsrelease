<?php

class X2a
{

	// Xml 转 数组, 包括根键，忽略空元素和属性，尚有重大错误
	public function xml_to_array( $xml )
	{
		$reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
		if(preg_match_all($reg, $xml, $matches))
		{
			$count = count($matches[0]);
			$arr = array();
			for($i = 0; $i < $count; $i++)
			{
				$key = $matches[1][$i];
				$val = $this->xml_to_array( $matches[2][$i] );  // 递归
				if(array_key_exists($key, $arr))
				{
					if(is_array($arr[$key]))
					{
						if(!array_key_exists(0,$arr[$key]))
						{
							$arr[$key] = array($arr[$key]);
						}
					}else{
						$arr[$key] = array($arr[$key]);
					}
					$arr[$key][] = $val;
				}else{
					$arr[$key] = $val;
				}
			}
			return $arr;
		}else{
			return preg_replace("/<!\[CDATA\[(.+?)\]\]>/is", "$1", str_replace("<![CDATA[]]>", "", $xml));
		}
	}


	// Xml 转 数组, 不包括根键
	public function xmltoarray( $xml )
	{

		$arr = $this->xml_to_array($xml);
		$key = array_keys($arr);
		return $arr[$key[0]];
	}
}
?>