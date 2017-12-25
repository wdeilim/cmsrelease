<?php
class WXAPI_RESPONSE {
	private $openId;
	private $appID;

	public function __construct($openId, $appID) {
		$this->openId = $openId;
		$this->appID = $appID;
	}

	public function respText($content) {
		if (empty($content)) {
			return error(-1, 'Invaild value');
		}
		$content = str_replace("\r\n", "\n", $content);
		$response = array();
		$response['FromUserName'] = $this->appID;
		$response['ToUserName'] = $this->openId;
		$response['MsgType'] = 'text';
		$response['Content'] = htmlspecialchars_decode($content);
		return $response;
	}


	public function respImage($mid) {
		if (empty($mid)) {
			return error(-1, 'Invaild value');
		}
		$response = array();
		$response['FromUserName'] = $this->appID;
		$response['ToUserName'] = $this->openId;
		$response['MsgType'] = 'image';
		$response['Image']['MediaId'] = $mid;
		return $response;
	}

	public function respVoice($mid) {
		if (empty($mid)) {
			return error(-1, 'Invaild value');
		}
		$response = array();
		$response['FromUserName'] = $this->appID;
		$response['ToUserName'] = $this->openId;
		$response['MsgType'] = 'voice';
		$response['Voice']['MediaId'] = $mid;
		return $response;
	}

	public function respVideo(array $video) {
		if (empty($video)) {
			return error(-1, 'Invaild value');
		}
		$response = array();
		$response['FromUserName'] = $this->appID;
		$response['ToUserName'] = $this->openId;
		$response['MsgType'] = 'video';
		$response['Video']['MediaId'] = $video['MediaId'];
		$response['Video']['Title'] = $video['Title'];
		$response['Video']['Description'] = $video['Description'];
		return $response;
	}

	public function respMusic(array $music) {
		if (empty($music)) {
			return error(-1, 'Invaild value');
		}
		$music = array_change_key_case($music);
		$response = array();
		$response['FromUserName'] = $this->appID;
		$response['ToUserName'] = $this->openId;
		$response['MsgType'] = 'music';
		$response['Music'] = array(
			'Title' => $music['title'],
			'Description' => $music['description'],
			'MusicUrl' => fillurl($music['musicurl'])
		);
		if (empty($music['hqmusicurl'])) {
			$response['Music']['HQMusicUrl'] = $response['Music']['MusicUrl'];
		} else {
			$response['Music']['HQMusicUrl'] = fillurl($music['hqmusicurl']);
		}
		if($music['thumb']) {
			$response['Music']['ThumbMediaId'] = $music['thumb'];
		}
		return $response;
	}

	public function respNews(array $news) {
		if (empty($news) || count($news) > 10) {
			return error(-1, 'Invaild value');
		}
		$news = array_change_key_case($news);
		if (!empty($news['title'])) {
			$news = array($news);
		}
		$response = array();
		$response['FromUserName'] = $this->appID;
		$response['ToUserName'] = $this->openId;
		$response['MsgType'] = 'news';
		$response['ArticleCount'] = count($news);
		$response['Articles'] = array();
		foreach ($news as $row) {
			$response['Articles'][] = array(
				'Title' => $row['title'],
				'Description' => ($response['ArticleCount'] > 1) ? '' : $row['description'],
				'PicUrl' => fillurl($row['picurl']),
				'Url' => $row['url'],
				'TagName' => 'item'
			);
		}
		return $response;
	}

	public function array2xml($data,$tag = '')
	{
		$xml = '';
		foreach($data as $key => $value)
		{
			if(is_numeric($key)) {
				if(is_array($value)) {
					$xml .= "<$tag>";
					$xml .= self::array2xml($value);
					$xml .="</$tag>";
				}else{
					$xml .= "<$tag>$value</$tag>";
				}
			}else{
				if(is_array($value)) {
					$keys = array_keys($value);
					if(is_numeric($keys[0])) {
						$xml .= self::array2xml($value,$key);
					}else{
						$xml .= "<$key>";
						$xml .= self::array2xml($value);
						$xml .= "</$key>";
					}
				}else{
					$xml .= "<$key>$value</$key>\r\n";
				}
			}
		}
		return "<xml>\r\n".$xml."\r\n</xml>";
	}

    public function buildResponse($data = array()) {
        $result = array();
        $result['MsgType'] = $data['type'];
        $data = $data['content'];

        if ($result['MsgType'] == 'text') {
            $result['Content'] = $data;
        } elseif ($result['MsgType'] == 'news') {
            $result['ArticleCount'] = $data['ArticleCount'];
            $result['Articles'] = array();
            if (!isset($data[0])) {
                $temp[0] = $data;
                $data = $temp;
            }
            foreach ($data as $row) {
                $result['Articles'][] = array(
                    'Title' => $row['Title'],
                    'Description' => $row['Description'],
                    'PicUrl' => $row['PicUrl'],
                    'Url' => $row['Url'],
                    'TagName' => 'item',
                );
            }
        } elseif ($result['MsgType'] == 'music') {
            $result['Music'] = array(
                'Title'	=> $data['Title'],
                'Description' => $data['Description'],
                'MusicUrl' => $data['MusicUrl'],
                'HQMusicUrl' => $data['HQMusicUrl'],
            );
        }
        return $result;
    }

}

