# qspay

![lincense](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)
![Pull request welcome](https://img.shields.io/badge/pr-welcome-green.svg?style=flat-square)

## 介绍
银联、微信、支付宝的H5支付场景的接口封装开发包。简化各种不同支付场景的接入和学习成本，统一化接入流程。

## 安装

```php
composer require tiderjian/qs-pay
```

### 微信支付

#### PC
```php
$config = [
    'app_id'             => **************,
    'mch_id'             => **************,
    'key'                => **************,
    'cert_path'          => ***************,
    'key_path'           => ***************,
    'notify_url'         => *************
];

$pay = Qspay::instance('wepay', $config);

$result = $pay->pay([
    'body' => '测试',
    'out_trade_no' => time(),
    'total_fee' => 1
]);

//PC端的场景返回的$result 是一个wx扫码的地址，可将其转成二维码，放在网页让用户打开微信扫码
```

#### 微信端
```php
$config = [
    'app_id'             => **************,
    'mch_id'             => **************,
    'key'                => **************,
    'cert_path'          => ***************,
    'key_path'           => ***************,
    'notify_url'         => *************
];

$pay = Qspay::instance('wepay', $config);

$result = $pay->pay([
    'body' => '测试',
    'out_trade_no' => time(),
    'total_fee' => 1,
    'openid' => session('test_openid')
]);

******************************
JS调用代码  ，将上面返回的$result传递给js使用

<script type="text/javascript" charset="utf-8">
    function onBridgeReady(){
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest', {$result},
            function(res){
                if(res.err_msg == "get_brand_wcpay_request:ok" ){
                    alert('支付成功');
                    window.location = '/home/index/wxpaysuccess';
                }
                else{
                    alert('支付失败');
                }
            });
    }
    if (typeof WeixinJSBridge == "undefined"){
        if( document.addEventListener ){
            document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
        }else if (document.attachEvent){
            document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
            document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
        }
    }else{
        onBridgeReady();
    }

</script>
//
```

#### notify
```php
$config = [
    'app_id'             => **************,
    'mch_id'             => **************,
    'key'                => **************,
    'cert_path'          => ***************,
    'key_path'           => ***************
];

$pay = Qspay::instance('wepay', $config);
$pay->notifyHandle(function($notify_param){
    //处理业务逻辑
    //返回 true 表示处理成功
    //返回 错误字符串表示处理失败
});
```

### 支付宝


#### 新版接口 PC AND WAP
程序自动判断PC和WAP的使用场景
````php
$config = [
    'interface_type' => Qspay\Alipay::ALIPAY_INTERFACE_TYPE_DONATE_NEW,
    // 沙箱模式
    'debug'       => false,
    // 应用ID
    'appid'       => ***************,
    // 支付宝公钥(1行填写)
    'public_key'  => ***************,
    // 应用私钥(1行填写)
    'private_key' => ********************,
    // 支付成功通知地址
    'notify_url'  => ***************,
    // 支付成功后返回的地址
    'return_url' => **********
];

$pay = Qspay::instance('alipay', $config);

echo $pay->pay([
    'out_trade_no' => time(), // 商户订单号
    'total_amount' => '0.01',    // 支付金额
    'subject'      => '支付订单描述', // 支付订单描述
]);
````

#### 新版接口 notify
```php
$config = [
    'interface_type' => Qspay\Alipay::ALIPAY_INTERFACE_TYPE_DONATE_NEW,
    // 沙箱模式
    'debug'       => false,
    // 应用ID
    'appid'       => ***************,
    // 支付宝公钥(1行填写)
    'public_key'  => ***************,
    // 应用私钥(1行填写)
    'private_key' => ********************,
    // 支付成功通知地址
    'notify_url'  => ***************
];

$pay = Qspay::instance('alipay', $config);
$pay->notifyHandle(function($notify_param){
    //处理业务逻辑
    //返回 true 表示处理成功
    //返回 错误字符串表示处理失败
});
```

#### 旧版接口 PC AND WAP
```php
$config = [
    'interface_type' => Qspay\Alipay::ALIPAY_INTERFACE_TYPE_DONATEV3,
    //合作伙伴身份(PID)
    'partner' => ***************,
    //MD5密钥
    'key'  => *************,
    // 支付宝登录邮箱
    'seller_email' => ********************,
    //采用传输协议 http or https
    'transport' => ****,
    // 支付成功回调通知地址
    'notify_url'  => ***************,
    // 支付成功返回地址
    'return_url' => **************
];

$pay = Qspay::instance('alipay', $config);

echo $pay->pay([
    'out_trade_no' => time(), // 商户订单号
    'total_amount' => '0.01',    // 支付金额
    'subject'      => '支付订单描述', // 支付订单描述
    'body' => '订单详情', //支付订单详情
]);
```

#### 旧版接口 notify
```php
$config = [
    'interface_type' => Qspay\Alipay::ALIPAY_INTERFACE_TYPE_DONATEV3,
    //合作伙伴身份(PID)
    'partner' => ***************,
    //MD5密钥
    'key'  => *************,
    // 支付宝登录邮箱
    'seller_email' => ********************,
    //采用传输协议 http or https
    'transport' => ****,
    // 支付成功回调通知地址
    'notify_url'  => ***************,
    // 支付成功返回地址
    'return_url' => **************
];

$pay = Qspay::instance('alipay', $config);
$pay->notifyHandle(function($notify_param){
    //处理业务逻辑
    //返回 true 表示处理成功
    //返回 错误字符串表示处理失败
});
```


### 银联

#### PC AND WAP
```php
$config = [
    'merId' => ***********,
    'notifyUrl' => ****************, //后台通知
    'signCertPath' => ***************, //签名证书
    'signCertPwd' => **********,  //签名密码
];

$qspay = Qspay::instance('unionpay', $config);

$order = [];
$order['out_trade_no'] = date('YmdHis');
$order['amount'] = 1;
$order['returnUrl'] = ******; //支付成功返回地址

echo  $qspay->pay($order);
```

#### notify
```php
 $config = [
    'merId' => '826440159420001',
    'signCertPath' => ***************, //签名证书
    'signCertPwd' => **********,  //签名密码
    'verifyRootCertPath' => *************, //root证书
    'verifyMiddleCertPath' => *************** //middle证书
];

$qspay = Qspay::instance('unionpay', $config);

$qspay->notifyHandle(function($post_data){
    //处理业务逻辑
    //返回 true 表示处理成功
    //返回 错误字符串表示处理失败
});
```

## 设置订单超时时间

#### 支付宝(旧版)
在pay方法参数中加入it_b_pay参数，具体参数设置请查看[支付宝API文档](https://opendocs.alipay.com/open/62/104743)
```php
$pay->pay([
    'out_trade_no' => time(), // 商户订单号
    'total_amount' => '0.01',    // 支付金额
    'subject'      => '支付订单描述', // 支付订单描述
    'return_url' => '**********',
    'it_b_pay'   => '10m',
]);
```

#### 支付宝(新版)
在pay方法参数中加入time_expire参数，具体参数设置请查看[支付宝API文档](https://opendocs.alipay.com/apis/api_1/alipay.trade.wap.pay)
```php
$pay->pay([
    'out_trade_no' => time(), // 商户订单号
    'total_amount' => '0.01',    // 支付金额
    'subject'      => '支付订单描述', // 支付订单描述
    'return_url' => '**********',
    'time_expire'   => '2016-12-31 10:05',
]);
```

#### 微信
在pay方法参数中加入time_expire参数，具体参数设置请查看[微信API文档](https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_1)
```php
$pay->pay([
    'body' => '测试',
    'out_trade_no' => time(),
    'total_fee' => 1,
    'openid' => session('test_openid'),
    'time_expire' => '20091227091010',
]);
```

## lincense
[MIT License](https://github.com/tiderjian/lara-for-tp/blob/master/LICENSE.MIT) 

