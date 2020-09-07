<?php
namespace Qspay;

use Closure;
use Exception;
use EasyWeChat\Factory;

class Wepay extends BasePay{

    protected $app;

    protected $require_key = [
        'app_id', 'mch_id', 'key', 'cert_path', 'key_path', 'notify_url'
    ];

    protected function setConfig($config)
    {
        $this->app = Factory::payment($config);
    }

    public function wapPay($order){
        $order['trade_type'] = 'JSAPI';
        $result = $this->app->order->unify($order);
        if (!$result['prepay_id']){
            E(json_encode($result));
        }
        $jssdk = $this->app->jssdk;

        return $jssdk->bridgeConfig($result['prepay_id']);
    }

    public function pcPay($order){
        $order['trade_type'] = 'NATIVE';
        $result = $this->app->order->unify($order);
        if (!$result['prepay_id']){
            E(json_encode($result));
        }

        if($result['return_code'] == 'FAIL'){
            throw new Exception($result['return_msg']);
        }

        return $result['code_url'];
    }

    public function notifyHandle(Closure $business_handle){
        $response = $this->app->handlePaidNotify(function($message, $fail) use ($business_handle){
            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if ($message['result_code'] === 'SUCCESS') {
                    $res = $business_handle($message);
                    if($res === true){
                        return true;
                    }
                    else if(is_string($res)){
                        return $fail($res);
                    }
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            return true;
        });

        $response->send();
    }

}