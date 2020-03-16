<?php
namespace Qspay\Alipay\Pc;

use Qspay\Alipay\Alipay;

class AlipayPc extends Alipay {

    public function apply($order){
        $out_trade_no = $order['out_trade_no'];
        $subject = $order['subject'];
        $body = $order['body'];
        $amount = $order['total_amount'];

        $config = $this->_config;

        $subject = str_replace('&', '', $subject);
        $body = str_replace('&', '', $body);

        $parameter = array(
            "service" => 'create_donate_trade_p',
            "partner" => $config['partner'],
            "payment_type"	=> 4,  //即时到账
            "notify_url"	=> $config['notify_url'],
            "return_url"	=> $config['return_url'],
            "seller_email"	=> $config['seller_email'],
            "out_trade_no"	=> $out_trade_no,
            "subject"	=> $subject,
            "total_fee"	=> $amount,
            "body"	=> $body,
            "it_b_pay" => '10m',
            "show_url"	=> '',
            "_input_charset"	=> 'utf-8',
            "extra_common_param" => ''
        );

        $html_text = $this->_alipay_submit->buildRequestForm($parameter, 'get', '确认');

        header("Content-type:text/html;charset=utf-8");
        echo $html_text;
    }

    static function getConfig(){
        $config['sign_type'] = 'MD5';
        $config['input_charset'] = 'utf-8';
        $config['cacert'] = __DIR__ . '/cacert.pem';
        return $config;
    }
}