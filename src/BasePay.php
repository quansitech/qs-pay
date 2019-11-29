<?php
namespace Qspay;

use Mobile_Detect;
use Exception;
use Closure;

abstract class BasePay{

    protected $require_key = [];

    public function __construct($config){
        $need_keys = array_diff_key(array_flip($this->require_key), $config);
        $error_keys = [];
        foreach($need_keys as $k => $v){
            array_push($error_keys, $k);
        }

        if($error_keys){
            throw new Exception('config缺少' . join(',', $error_keys));
        }

        $this->setConfig($config);
    }

    protected abstract function setConfig($config);

    public function pay($order){
        $detect = new Mobile_Detect;
        if($detect->isMobile()){
            return $this->wapPay($order);
        }
        else{
            return $this->pcPay($order);
        }
    }

    public abstract function wapPay($order);

    public abstract function pcPay($order);

    public abstract function notifyHandle(Closure $cb);

}