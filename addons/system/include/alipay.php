<?php

if (!function_exists('get_code')) {
    /**
     * @param $order
     * @param $payment
     * @return bool|提交表单HTML文本
     */
    function get_code($order, $payment)
    {
        if (!is_array($order) || !is_array($payment)) return false;

        $alipay_config = get_alipay_config($payment);

        $parameter = array(
            "service" => "create_direct_pay_by_user",
            "partner" => trim($payment['partner']),
            "seller_email" => trim($payment['account']),
            "payment_type" => 1,
            "notify_url" => $order['notify_url'],
            "return_url" => $order['return_url'],
            "out_trade_no" => $order['payid'],
            "subject" => $order['title'],
            "total_fee" => intval($order['amount']),
            "body" => $order['remark'],
            "show_url" => BASE_URI,
            "anti_phishing_key" => "",
            "exter_invoke_ip" => "",
            "_input_charset" => $alipay_config['input_charset']
        );

        //建立请求
        require_once("alipay/alipay_submit.class.php");
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter, "get", "确认");
        return $html_text;
    }
}

if (!function_exists('get_respond')) {
    /**
     * @param $payment
     * @return 验证结果
     */
    function get_respond($payment, $verify)
    {
        require_once("alipay/alipay_notify.class.php");
        $alipayNotify = new AlipayNotify(get_alipay_config($payment));
        if ($verify == 'notify') {
            $verify_result = $alipayNotify->verifyNotify();
        }else{
            $verify_result = $alipayNotify->verifyReturn();
        }
        return $verify_result;
    }
}

if (!function_exists('get_alipay_config')) {
    /**
     * @param $payment
     * @return array 配置
     */
    function get_alipay_config($payment)
    {
        $alipay_config = array();
        $alipay_config['partner'] = $payment['partner'];
        $alipay_config['seller_email'] = $payment['account'];
        $alipay_config['key'] = $payment['key'];
        $alipay_config['sign_type'] = strtoupper('MD5');
        $alipay_config['input_charset'] = strtolower('utf-8');
        $alipay_config['cacert'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'alipay' . DIRECTORY_SEPARATOR . 'cacert.pem';
        $alipay_config['transport'] = 'http';
        return $alipay_config;
    }
}
?>