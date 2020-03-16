<?php
namespace Qspay\Alipay;

use Qspay\Alipay\Common\AlipayNotify;
use Qspay\Alipay\Common\AlipaySubmit;

class Alipay{
    protected $_config;

    public function __construct($p_config = '') {
        $config = $this->getConfig();
        if($p_config){
            $config = array_merge($config, $p_config);
            $config['seller_id'] = $config['partner'];
        }
        $this->_config = $config;
        $this->_alipay_submit = new AlipaySubmit($config);
        $this->_alipay_notify = new AlipayNotify($config);
    }

    public function notify(){
        $r = $this->_alipay_notify->verifyNotify();
        if($r===true){
            return $this->_input();
        }
        else {
            throw new \Exception("alipay notify verify error");
        }
    }

    protected function _input(){
        $param['out_trade_no'] = $_POST['out_trade_no'];
        $buyer_info['buyer_email'] = $_POST['buyer_email'];
        $buyer_info['buyer_id'] = $_POST['buyer_id'];
        $buyer_info['out_trade_no'] = $_POST['out_trade_no'];
        $buyer_info['trade_no'] = $_POST['trade_no'];
        $buyer_info['trade_status'] = $_POST['trade_status'];
        $param['buyer_info'] = $buyer_info;
        $param['trade_status'] = $_POST['trade_status'] == 'TRADE_FINISHED' || $_POST['trade_status'] == 'TRADE_SUCCESS' ? true : false;
        return $param;
    }
}