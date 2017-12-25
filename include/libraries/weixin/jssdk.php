<?php
class WXAPI_JSSDK {
	private $appId;
	private $appSecret;
	private $appMd5;
	private $appDir;
	private $appUrl;
	private $iscorp;

	public function __construct($appId, $appSecret, $appUrl, $iscorp = false) {
		$this->appId = $appId;
		$this->appSecret = $appSecret;
		$this->appMd5 = md5($this->appId.$this->appSecret);
		$this->appDir = BASE_PATH.'caches/token_ticket/'.date("Ym/");
		$this->appUrl = $appUrl?$appUrl:get_url();
		$this->iscorp = ($iscorp === 7)?true:$iscorp;
		if(!is_dir($this->appDir)) {
			@unlink(BASE_PATH.'caches/token_ticket/');
			make_dir($this->appDir);
		}
	}

	public function getSignPackage() {
		$jsapiTicket = $this->getJsApiTicket();

		// 注意 URL 一定要动态获取，不能 hardcode.
		$url = $this->appUrl;

		$timestamp = SYS_TIME;
		$nonceStr = $this->createNonceStr();

		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

		$signature = sha1($string);

		$signPackage = array(
			"appId"     => $this->appId,
			"nonceStr"  => $nonceStr,
			"timestamp" => $timestamp,
			"url"       => $url,
			"signature" => $signature,
			"rawString" => $string
		);
		return $signPackage;
	}

	private function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	private function getJsApiTicket() {
		// jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
		$data = json_decode(file_get_contents($this->appDir.$this->appMd5."_ticket.json"));
		if ($data->expire_time < SYS_TIME) {
			$accessToken = $this->getAccessToken();
			// 如果是企业号用以下 URL 获取 ticket
			if ($this->iscorp === true) {
				$url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
			}else{
				$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
			}
			$res = json_decode($this->httpGet($url));
			$ticket = $res->ticket;
			if ($ticket) {
				$data->expire_time = SYS_TIME + 7000;
				$data->jsapi_ticket = $ticket;
				$fp = fopen($this->appDir.$this->appMd5."_ticket.json", "w");
				fwrite($fp, json_encode($data));
				fclose($fp);
			}
		} else {
			$ticket = $data->jsapi_ticket;
		}

		return $ticket;
	}

	private function getAccessToken() {
		// access_token 应该全局存储与更新，以下代码以写入到文件中做示例
		$data = json_decode(file_get_contents($this->appDir.$this->appMd5."_token.json"));
		if ($data->expire_time < SYS_TIME) {
			// 如果是企业号用以下URL获取access_token
			if ($this->iscorp === true) {
				$url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
			}else{
				$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
			}
			$res = json_decode($this->httpGet($url));
			$access_token = $res->access_token;
			if ($access_token) {
				$data->expire_time = SYS_TIME + 7000;
				$data->access_token = $access_token;
				$fp = fopen($this->appDir.$this->appMd5."_token.json", "w");
				fwrite($fp, json_encode($data));
				fclose($fp);
			}
		} else {
			$access_token = $data->access_token;
		}
		return $access_token;
	}

	private function httpGet($url) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_URL, $url);

		$res = curl_exec($curl);
		curl_close($curl);

		return $res;
	}
}

