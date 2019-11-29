<?php
namespace Qspay;

use Exception;

class Qspay{

    public static function instance($type, $config){
        if(!class_exists("\\Qspay\\" . ucfirst($type))){
            throw new Exception('无效支付类型');
        }

        $class = "\\Qspay\\" . ucfirst($type);
        return new $class($config);
    }
}