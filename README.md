# we7_module
微擎模块大全

* v_prize 抽奖模块，对应小程序前端：[抽奖小程序](https://github.com/qq3245096941/wxapp_prize)
* jz_movie 江左模拟盘模块，对应小程序前端：[江左模拟盘](https://github.com/qq3245096941/jz_movie_weapp)
* monai_market 二手车模块，外包项目，没有前端
* zhls_sun 律师小程序，对应前端：[律师小程序](https://github.com/qq3245096941/zhls_sun_weapp)
* gather_feedback 收集反馈，公众号程序，h5页面


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





