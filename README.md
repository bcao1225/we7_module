# we7_module
微擎模块大全

* v_prize 抽奖模块，对应小程序前端：[抽奖小程序](https://github.com/qq3245096941/wxapp_prize)
* jz_movie 江左模拟盘模块，对应小程序前端：[江左模拟盘](https://github.com/qq3245096941/jz_movie_weapp)
* monai_market 二手车模块，外包项目，没有前端
* zhls_sun 律师小程序，对应前端：[律师小程序](https://github.com/qq3245096941/zhls_sun_weapp)
* gather_feedback 收集反馈，公众号程序，h5页面
* machine_feedback 斐力机械，小程序，对应前端：[斐力科技](https://github.com/qq3245096941/fljx)
* argue_routine 辩论，公众号程序，h5页面，使用cdn引入vue到每个页面中，实现``vue``和``微擎``进行整合。
* book_store 图片仓库，公众号程序

***
# ``jz_movie``模块将wxapp文件进行模块划分
```
<?php

require_once __DIR__."/api/Api.php";

defined('IN_IA') or exit('Access Denied');

class Jz_movieModuleWxapp extends WeModuleWxapp
{
    public function __destruct()  //这里不能调用构造函数，因为有些常量必须要类初始化完毕后才会初始化。
    {
        Api::instant();
    }
}
```
调用``Api``类的``instant``方法
```
<?php

//创建了一个模块时，必须引入当前模块
require_once __DIR__.'/Movie.php';
require_once __DIR__.'/User.php';
require_once __DIR__.'/Investment.php';
require_once __DIR__.'/Actor.php';

defined('IN_IA') or exit('Access Denied');

class Api extends WeModuleWxapp{
    public static function instant(){
        global $_GPC;
        $clazz_name = ucfirst($_GPC['clazz']);
        $clazz = new $clazz_name;
        call_user_func([$clazz,$_GPC['do']]);
    }
}
```
前端这么调用
```
app.util.request({
        url: 'entry/wxapp/get_investment',
        data: {
          web_user_code: data.web_user_code,
          clazz:'investment'
        },
        success(res){
          console.log(res)
        }
```
调用``wxapp``文件的``get_investment``方法，这个方法在``investment``类中。
***
### json_decode转换字符串问题

```
$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->app_secret;
$response = ihttp_get($url);
$content = json_decode($response['content'],true); //这里后面传入true，将字符串转换成数组
```
### 调用模板消息方式注意事项
```
$data = [
    'touser'=>$web_user['openid'],
    'template_id'=>'...',
    'form_id'=>$web_user['form_id'],
    'page'=>'/jz_movie/pages/user/user',
    'emphasis_keyword'=>'keyword1.DATA',
    'data'=>[
        'keyword1'=>[
            'value'=>$earnings['earnings']
        ],
        'keyword2'=>[
            'value'=>date('Y-m-d H:i:s',time())
        ]
    ]
];

//推送文本消息
ihttp_post($url,json_encode($data)); //php调用模板消息发送，是需要将数组转换成json字符串的
```
***
### 定义微擎公众号模块，手机端主页二维码定义
```
<bindings>
    <cover>
        <entry title="功能封面" do="index" state="" direct="false" />
    </cover>
</bindings>
```

### 微擎模块引入第三方js库的问题
一般使用``require.js``来加载第三方库。下面例子是加载``echarts``。``[]``里面可以填写本地路径或cdn路径
```
require(['{MODULE_URL}lib/echarts.min.js'],function (echarts) {
    //...执行操作
}
```
### 微信网页实现自定义分享
微擎封装了分享内容详情查看：[自定义分享](https://s.w7.cc/index.php?c=wiki&do=view&id=1&list=390)

但是微擎有几处错误

> 头部需这样引入
```
<head>
    <meta charset="UTF-8">
    <title>{$system_setting['title']}</title>
    {php register_jssdk(true)} //头部需如此引入
</head>
```

> ```wx```对象需配置

* 首先在php中获取jssdk签名包
* ```
  $signPackage = $_W['account']['jssdkconfig'];//微擎封装好的jssdk签名包的内容
  ```
* 并在html文件中进行配置，即可使用
  ```
    wx.config({
        debug: false, //这里设置为true时，微信分享功能每个步骤都会弹框。
        appId: '{$signPackage["appId"]}',
        timestamp: '{$signPackage["timestamp"]}',
        nonceStr: '{$signPackage["nonceStr"]}',
        signature: '{$signPackage["signature"]}',
        jsApiList: [
            'checkJsApi',
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'onMenuShareWeibo',
            'onMenuShareQZone'
        ]
    });

    wx.ready(function () {
        wx.onMenuShareAppMessage({
            title: "{$system_setting['share_title']}",
            desc: "{$system_setting['share_desc']}",
            link: document.location.href,
            imgUrl: "{php echo tomedia($system_setting['share_img'])}",
            success: function () {
            },
            cancel: function () {
            }
        });
    });
  ```
可参考别人的帖子：[在微擎调用微信JSSDK实现分享功能](https://blog.csdn.net/zhemejinnameyuanxc/article/details/81258584);

### 微擎生成二维码，转换成字符串
```
/*生成二维码*/
function make_qrcode($url = '')
{
    load()->library('qrcode');
    //由于phpQrcode类直接返回到浏览器，所以需要利用php缓冲器阻止他直接返回到浏览器，然后捕捉到二维码的图片流
    ob_start();//开启缓冲区
    QRcode::png($url, false, 'L', 10, 1);//生成二维码
    header('Content-Type:text/html'); //生成二维码后设置响应头
    $img = ob_get_contents();//获取缓冲区内容
    ob_end_clean();//清除缓冲区内容
    return 'data:image/jpg;base64,' . chunk_split(base64_encode($img));
}
```
在前端设置
```
<img width="130" src="{$activity['qrcode']}" alt="">
```
***
### 设置```echarts```饼图默认高亮
```
/*最大扇形块对应的名称*/
const max_pie_name = pie_data.reduce((init,currentValue)=>{
    if(Number.parseInt(init.value)>Number.parseInt(currentValue.value)){
        return init;
    }
    return currentValue;
});
//排序功能，获取在数组中value值最大的对象
```

```
/*设置饼图默认选中的块，此块为饼图最大值的块*/
pie_echarts.dispatchAction({
    type: 'highlight',
    seriesName:'feedback_pie',
    name:max_pie_name.name
});

pie_echarts.on('mouseover', (e)=>{
    //当检测到鼠标悬停事件，取消默认选中高亮
    pie_echarts.dispatchAction({
        type: 'downplay',
        seriesName:'feedback_pie',
        name:max_pie_name.name
    });
    //高亮显示悬停的那块
    pie_echarts.dispatchAction({
        type: 'highlight',
        seriesName:'feedback_pie',
        dataIndex: e.dataIndex
    });
});

pie_echarts.on('mouseout', (e)=>{
    //将之前高亮的取消高亮
    pie_echarts.dispatchAction({
        type: 'downplay',
        seriesName:'feedback_pie',
        dataIndex: e.dataIndex
    });

    //检测鼠标移出后显示之前默认高亮的那块
    pie_echarts.dispatchAction({
        type: 'highlight',
        seriesName:'feedback_pie',
        name:max_pie_name.name
    });
});
```

# 微擎和小程序前端上传交互
```
/*图片上传*/
public function doPageUpload_img(){
    load()->func('file');

    /*图片名称*/
    $img_name = time().'.jpeg';
    //保存上传文件
    file_move($_FILES['upload_img']['tmp_name'], MODULE_ROOT . '/lib/upload_img/'.$img_name);

    $this->result(0,'上传成功',$img_name);
}
```

```
 wx.uploadFile({
      filePath: file.file.path,
      name: 'upload_img',
      url: app.util.url('entry/wxapp/upload_img') + 'm=machine_feedback',
      success(res){
        console.log(res);
      },
      fail(error){
        console.log(error);
      }
    })
```

# 微擎调用微信api获取小程序
```
$response = $this->account_api->getCodeUnlimit('id=' . $machine['id'], 'page/index/index', 150);
$machine_list[$key]['qrcode'] = base64_encode($response);前端需将base64编码
```
在前端中调用
```
<img style="margin: 0 auto;display: block" width="90" src="data:image/png;base64,{$machine['qrcode']}"
```

```
扫描二维码时，参数获取样子
async onLoad(options) {
        console.log(options);  //{scene:id%3D13}
        //判断是否是扫码进入
        if (options.scene === undefined) {
            wx.redirectTo({
                url: '/page/error/error'
            });
            return;
        }
        this.setData({
            id: decodeURIComponent(options.scene).split("=")[1] /需要通过这个方法解码
        });
}
```
***
# vue和微擎模板组合，主要用于需要大量ajax撑起的前端页面
[vue组合微擎模板](https://www.miaowenzhao.cn/particulars/22)

# 微信现金红包开发
```php
//网络请求，主要作用于现金红包发放
function postData($url, $postfields)
{
    global $_W;

    $ch = curl_init();
    $params[CURLOPT_URL] = $url;    //请求url地址
    $params[CURLOPT_HEADER] = false; //是否返回响应头信息
    $params[CURLOPT_RETURNTRANSFER] = true; //是否将结果返回
    $params[CURLOPT_FOLLOWLOCATION] = true; //是否重定向
    $params[CURLOPT_POST] = true;
    $params[CURLOPT_POSTFIELDS] = $postfields;
    $params[CURLOPT_SSL_VERIFYPEER] = false;
    $params[CURLOPT_SSL_VERIFYHOST] = false;
    //以下是证书相关代码
    $params[CURLOPT_SSLCERTTYPE] = 'PEM';
    $params[CURLOPT_SSLCERT] = MODULE_ROOT . '/lib/apiclient_cert.pem';
    $params[CURLOPT_SSLKEYTYPE] = 'PEM';
    $params[CURLOPT_SSLKEY] = MODULE_ROOT . '/lib/apiclient_key.pem';

    curl_setopt_array($ch, $params); //传入curl参数

    $content = curl_exec($ch); //执行
    curl_close($ch); //关闭连接
    return $content;
}

/**
 * 发送红包
 * @param int|string $activity_id 指定活动id
 * @param string $openid 用户的openid
 * @param int $money 发送红包金额，单位为元
 * @return array|mixed|string
 */
public function send_redpacket($activity_id, $openid, $money)
{
    global $_W;

    $activity = pdo_get('ims_argue_routine_activity', ['id' => $activity_id]);

    /*获取签名*/
    $arr = $this->setSign(
        [
            'nonce_str' => random(32),
            'mch_billno' => random(28, true),
            /*商户id*/
            'mch_id' => $_W['uniaccount']['setting']['payment']['wechat']['mchid'],
            /*公众号appid*/
            'wxappid' => $_W['uniaccount']['key'],
            /*商户名称*/
            'send_name' => $activity['bonus_name'],
            /*用户openid*/
            're_openid' => $openid,
            /*发放金额，单位为分*/
            'total_amount' => $money * 100,
            /*总人数*/
            'total_num' => 1,
            /*祝福语*/
            'wishing' => $activity['bonus_desc'],
            /*ip地址*/
            'client_ip' => CLIENT_IP,
            /*活动名称*/
            'act_name' => '猜灯谜抢红包活动',
            /*备注*/
            'remark' => '猜越多得越多，快来抢！',
            'scene_id' => 'PRODUCT_1'
        ]
    );
    $xml = array2xml($arr);
    $content = $this->postData('https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack', $xml);
    return xml2array($content);
}

/**
 * 获取带签名的数组，获取签名
 * @param array $arr
 * @return array
 */
public function setSign($arr)
{
    global $_W;
    //去除空值
    $arr = array_filter($arr);
    if (isset($arr['sign'])) {
        unset($arr['sign']);
    }
    //按照键名字典排序
    ksort($arr);
    //生成url格式的字符串
    $str = urldecode(http_build_query($arr)) . '&key=' . $_W['uniaccount']['setting']['payment']['wechat']['apikey'];
    $arr['sign'] = strtoupper(md5($str));
    return $arr;
}
```
# AMD写法
```javascript
//定义模块
define(['依赖模块路径，本地路径和网络路径都可以'],()=>{
    return {
        template:`div`,
        mounted(){
            console.log('123');
        }
    }
});
```
```javascript
//引入模块
require(['https://cdn.jsdelivr.net/npm/vue/dist/vue.js'], (Vue) => {
    //...
})
```
# 前端判断是否是微信客户端打开网页
```php
//如果是普通浏览器访问
if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
    message('请使用微信打开', '', 'error');
}
```
# bootstrap弹出层的使用
```javascript
$(function () {
    $("[data-toggle='popover']").popover(); //激活

    $(".book_box_{$index} span").each((index, element) => {
        $(element).popover({
            trigger:'click',
            placement:'auto',
            title:'请选择类型',
            html:true, //必须设置才能使用html
            content:`<div>123</div>`
        });
    });
});
```





