<?php
namespace Qspay\Alipay\Wap;

use Qspay\Alipay\Alipay;

class AlipayWap extends Alipay {


    public function getConfig(){
        //签名方式
        $alipay_config['sign_type']    = strtoupper('MD5');

        //字符编码格式 目前支持utf-8
        $alipay_config['input_charset']= strtolower('utf-8');

        //ca证书路径地址，用于curl中ssl校验
        //请保证cacert.pem文件在当前文件夹目录中
        $alipay_config['cacert']    = __DIR__ . '/../Common/cacert.pem';

        // 支付类型 ，无需修改
        $alipay_config['payment_type'] = "4";

        // 产品类型，无需修改
        $alipay_config['service'] = "alipay.wap.create.direct.pay.by.user";

        return $alipay_config;
    }

    public function apply($order){
        $out_trade_no = $order['out_trade_no'];
        $subject = $order['subject'];
        $body = $order['body'];
        $amount = $order['total_amount'];

        $config = $this->_config;

        $subject = str_replace('&', '', $subject);
        $body = str_replace('&', '', $body);

        $parameter = array(
            "service"       => $config['service'],
            "partner"       => $config['partner'],
            "seller_id"  => $config['seller_id'],
            "payment_type"	=> $config['payment_type'],
            "notify_url"	=> $config['notify_url'],
            "return_url"	=> $config['return_url'],
            "_input_charset"	=> trim(strtolower($config['input_charset'])),
            "out_trade_no"	=> $out_trade_no,
            "subject"	=> $subject,
            "total_fee"	=> $amount,
            "show_url"	=> "",
            "app_pay"	=> "Y",//启用此参数能唤起钱包APP支付宝
            "body"	=> $body,
            "it_b_pay" => $order['it_b_pay']?$order['it_b_pay']:'10m',
        );
        $html_text = $this->_alipay_submit->buildRequestForm($parameter, 'get', '确认');

        header("Content-type:text/html;charset=utf-8");
        echo $html_text;
    }
}