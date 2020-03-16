<?php
namespace Qspay;

use Qspay\Alipay\Pc\AlipayPc;
use Qspay\Alipay\Wap\AlipayWap;
use We;
use Closure;
use Exception;
use Mobile_Detect;

class Alipay extends BasePay{

    const ALIPAY_INTERFACE_TYPE_DONATEV3 = 1;
    const ALIPAY_INTERFACE_TYPE_DONATE_NEW = 2;

    protected $app;

    protected $require_key = [
        'interface_type'
    ];

    protected function setConfig($config)
    {
        switch($config['interface_type']){
            case self::ALIPAY_INTERFACE_TYPE_DONATE_NEW:
                self::setConfigForDonateNew($config);
                break;
            case self::ALIPAY_INTERFACE_TYPE_DONATEV3:
                self::setConfigForDonateV3($config);
                break;
            default:
                throw new Exception("error interface_type");
                break;
        }
    }

    protected function setConfigForDonateNew($config){
        $detect = new Mobile_Detect;
        if($detect->isMobile()){
            $this->app = We::AliPayWap($config);
        }
        else{
            $this->app = We::AliPayWeb($config);
        }
    }

    protected function setConfigForDonateV3($config){
        $detect = new Mobile_Detect;
        if($detect->isMobile()){
            $this->app = new AlipayWap($config);
        }
        else{
            $this->app = new AlipayPc($config);
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