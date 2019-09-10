<?php

//创建了一个模块时，必须引入当前模块
require_once __DIR__.'/Movie.php';
require_once __DIR__.'/User.php';
require_once __DIR__.'/Investment.php';
require_once __DIR__.'/Actor.php';
require_once __DIR__.'/WxPort.php';
require_once __DIR__.'/Util.php';

defined('IN_IA') or exit('Access Denied');

class Api extends WeModuleWxapp{
    
    public $appid = 'wx4732bfa5eef2bf6d';
    public $app_secret = '7847059d28802e9812770b5561e1d57a';

    public $other_database = [
        'host' => '39.104.81.221', //数据库IP或是域名
        'username' => 'root', // 数据库连接用户名
        'password' => 'root', // 数据库连接密码
        'database' => 'jz_movie', // 数据库名
        'port' => 3306, // 数据库连接端口
        'charset' => 'utf8', // 数据库默认编码
        'pconnect' => 0, // 是否使用长连接
    ];

    public $db;

    public function __construct()
    {
        $this->db = new DB($this->other_database);
    }

    public static function instant(){
        global $_GPC;
        $clazz_name = ucfirst($_GPC['clazz']);
        $clazz = new $clazz_name;
        call_user_func([$clazz,$_GPC['do']]);
    }

    /**
     * 通过传入web_user_code获取用户信息
     */
    public function get_user($web_user_code = '')
    {
        return $this->db->get('web_user', ['web_user_code' => $web_user_code]);
    }

    /**
     * 获取小程序的access_token
     */
    public function get_access_token(){
        $content = cache_read('access_token');
        if($content['overtime']>time()){
            return $content['token'];
        }

        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->app_secret;
        $response = ihttp_get($url);
        $content = json_decode($response['content'],true);

        //将access_token缓存起来
        cache_write('access_token',
            [
                'token'=>$content['access_token'],
                'overtime'=>time()+$content['expires_in']
            ]
        );

        return $content['access_token'];
    }
}