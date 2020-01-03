<?php

//用户类
require_once __DIR__ . '/Api.php';
defined('IN_IA') or exit('Access Denied');

class User extends Api
{
    //登录
    public function login()
    {
        global $_GPC;
        $data = ihttp_get('https://api.weixin.qq.com/sns/jscode2session?appid=' . $this->appid . '&secret=' . $this->app_secret . '&js_code=' . $_GPC['code'] . '&grant_type=authorization_code');
        $content = json_decode($data['content'], true);

        //如果openid在用户中已经存在
        $user = pdo_get('ims_machine_feedback_user', ['openid' => $content['openid']]);

        if ($user) {
            $this->result(0, '获取成功', $user);
        }

        //如果不存在则将信息保存在数据库中
        $submit = [
            'nickname' => $_GPC['nickname'],
            'avatar' => $_GPC['avatar'],
            'session_key' => $content['session_key'],
            'openid'=>$content['openid'],
            'create_time' => time()
        ];

        //将用户信息保存在数据库，包括session_key,openid
        pdo_insert('ims_machine_feedback_user', $submit);

        $user = pdo_get('ims_machine_feedback_user',['openid'=>$content['openid']]);

        $this->result(0, '保存成功', $user);
    }

    //获取已存在的用户信息
    public function get_user_data()
    {
        global $_W, $_GPC;
        $user = pdo_get('ims_machine_feedback_user', ['openid' => $_GPC['openid']]);
        $this->result(0, '获取成功', $user);
    }
}