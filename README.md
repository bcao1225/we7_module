# we7_module
微擎模块大全

* v_prize 抽奖模块，对应小程序前端：[抽奖小程序](https://github.com/qq3245096941/wxapp_prize)
* jz_movie 江左模拟盘模块，对应小程序前端：[江左模拟盘](https://github.com/qq3245096941/jz_movie_weapp)
* monai_market 二手车模块，外包项目，没有前端

***
#``jz_movie``模块将wxapp文件进行模块划分
```
<?php

require_once __DIR__."/api/Api.php";

defined('IN_IA') or exit('Access Denied');

class Jz_movieModuleWxapp extends WeModuleWxapp
{
    public function __construct()
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
###json_decode转换字符串问题

```
$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->app_secret;
$response = ihttp_get($url);
$content = json_decode($response['content'],true); //这里后面传入true，将字符串转换成数组
```

