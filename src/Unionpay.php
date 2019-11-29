<?php
namespace Qspay;

use Closure;
use Exception;

class Unionpay extends BasePay {

    protected $app;
    protected $config;

    protected $require_key = [
        'merId', 'signCertPath', 'signCertPwd'
    ];

    protected function setConfig($config)
    {
        $config['version'] = '5.1.0';
        $config['signMethod'] = '01';
        $config['encoding'] = 'UTF-8';
        $config['currencyCode'] = '156';
        $config['ifValidateCNName'] = true;

        $this->app = \zhangv\unionpay\UnionPay::B2C($config);

        $this->config = $config;
    }

    public function wapPay($order){
        return $this->pcPay($order);
    }

    public function pcPay($order){
        $out_trade_no = $order['out_trade_no'];
        $amount = $order['amount'];

        unset($order['out_trade_no']);
        unset($order['amount']);

        $config = array_merge($this->config, $order);
        $this->app->setConfig($config);

        return $this->app->pay($out_trade_no,$amount);
    }

    public function notifyHandle(Closure $business_handle)
    {
        $post_data = $_POST;
        if($post_data['respCode'] == '00' || $post_data['respCode'] == 'A6'){
            $res = $this->app->onNotify($post_data,$business_handle);
            if($res !== true){
                throw new Exception($res . print_r($post_data, true));
            }
            return $res;
        }
        else{
            throw new Exception(print_r($post_data, true));
        }

    }
}