<?php
namespace Qspay;

use We;
use Closure;
use Exception;
use Mobile_Detect;

class Alipay extends BasePay{

    protected $app;

    protected $require_key = [
        'appid', 'public_key', 'private_key'
    ];

    protected function setConfig($config)
    {
        $detect = new Mobile_Detect;
        if($detect->isMobile()){
            $this->app = We::AliPayWap($config);
        }
        else{
            $this->app = We::AliPayWeb($config);
        }

    }

    public function wapPay($order){
        return $this->app->apply($order);
    }

    public function pcPay($order){
        return $this->app->apply($order);
    }

    public function notifyHandle(Closure $business_handle){
        $data = $this->app->notify();

        $res = $business_handle($data);
        if($res !== true){
            throw new Exception($res . print_r($data));
        }

        return true;
    }

}