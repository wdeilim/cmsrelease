<?php
require_once 'HttpRequst.php';
require_once 'AopSdk.php';
require_once 'function.inc.php';
class UserInfo {
	public function getUserInfo($auth_code, $gtype = 'info') {
		$token = $this->requestToken ( $auth_code );
		writeLog ( "token" . var_export ( $token, true ) );

		$retuser = array();
		if (isset ( $token->alipay_system_oauth_token_response )) {
			$retuser['oauth'] = $token->alipay_system_oauth_token_response;
			$token_str = $token->alipay_system_oauth_token_response->access_token;
			$AlipayUserUserinfoShareRequest = new AlipayUserUserinfoShareRequest ();
			$user_info = aopclient_request_execute ( $AlipayUserUserinfoShareRequest, $token_str );
			if (isset ( $user_info->alipay_user_userinfo_share_response )) {
				$retuser['info'] = $user_info->alipay_user_userinfo_share_response;

			} elseif (isset ( $user_info->error_response )) {
				$retuser['error'] = $user_info->error_response;
			}

		} elseif (isset ( $token->error_response )) {
			$errormsg = $token->error_response->msg.$token->error_response->sub_msg;
			message("温馨提示", "错误-1: ".characet($errormsg));
		}
		writeLog ( "user_info" . var_export ( $retuser, true ) );
		return $retuser;
	}

	public function requestToken($auth_code) {
		$AlipaySystemOauthTokenRequest = new AlipaySystemOauthTokenRequest ();
		$AlipaySystemOauthTokenRequest->setCode ( $auth_code );
		$AlipaySystemOauthTokenRequest->setGrantType ( "authorization_code" );
		$result = aopclient_request_execute ( $AlipaySystemOauthTokenRequest );
		return $result;
	}
}

?>