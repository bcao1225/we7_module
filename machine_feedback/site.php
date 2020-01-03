<?php
/**
 * machine_feedback模块微站定义
 *
 * @author QQRPazWaNPgW
 * @url
 */
defined('IN_IA') or exit('Access Denied');

class Machine_feedbackModuleSite extends WeModuleSite
{
    //微信api和当前小程序信息
    public $account_api;
    public $access_token;

    public function __construct()
    {
        //初始化微信api
        $this->account_api = WeAccount::create();

        $appid = $this->account_api->account->key;
        $app_secret = $this->account_api->account->secret;

        $data = ihttp_get('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $app_secret);
        /*缓存access_token*/
        cache_write('access_token', json_decode($data['content'], true)['access_token']);
    }
}