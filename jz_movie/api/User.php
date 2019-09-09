<?php

require_once __DIR__.'/Api.php';

defined('IN_IA') or exit('Access Denied');

class User extends Api{

    /*传入微信用户信息，换取openid*/
    public function set_userInfo()
    {
        global $_W, $_GPC;
        /*通过openid查询当前数据库是否有用户*/
        $user = $this->db->get('web_user', ['openid' => $_W['openid']]);

        /*如果当前用户存在，则将当前用户信息直接返回出来*/
        if ($user) {
            $this->result(0, '保存成功', $user);
        }

        $this->db->insert('web_user', [
            'web_user_code' => random(2,true).time() . random(3, true),
            'openid' => $_W['openid'],
            'logo' => $_GPC['avatarUrl'],
            'nickname' => $_GPC['nickName'],
            'sex' => $_GPC['gender'],
            'create_time' => date('Y-m-d H:i:s', time()),
            'experience_of_gold' => 50000
        ]);

        $this->result(0, '保存成功', $this->db->get('web_user', ['openid' => $_W['openid']]));
    }

    /**
     * 通过openid获取用户信息
     */
    public function openid_by_user(){
        global $_GPC;
        $this->result(0,'获取成功',$this->db->get('web_user',['openid'=>$_GPC['openid']]));
    }

    //更新当前用户登录时间
    public function set_upload_time()
    {
        global $_W, $_GPC;
        $this->db->update('web_user', ['last_longin_time' => date('Y-m-d H:i:s', time())], ['web_user_code' => $_GPC['web_user_code']]);
        $this->result(0, '更新成功', $this->get_user($_GPC['web_user_code']));
    }

    /*更新用户手机号码*/
    public function set_phone()
    {
        global $_W, $_GPC;
        $this->db->update('web_user', ['phone' => $_GPC['phone']], ['web_user_code' => $_GPC['web_user_code']]);
        $this->result(0, '更新成功', $this->get_user($_GPC['web_user_code']));
    }

    /**
     * 解密手机号，暂定方法
     */
    public function decode_phone(){
        include_once __DIR__."/../decode_phone/wxBizDataCrypt.php";

        global $_GPC,$_W;

        $pc = new WXBizDataCrypt($this->appid, $_GPC['sessionKey']);
        $errCode = $pc->decryptData($_GPC['encryptedData'], $_GPC['iv'], $data);

        $this->result(0,'获取成功',$data);
    }

    /**
     * 传入wx.login获取code，换取session_key
     */
    public function get_session_key(){
        global $_GPC;
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$this->appid.'&secret='.$this->app_secret.'&js_code='.$_GPC['code'].'&grant_type=authorization_code';
        $response = ihttp_get($url);
        $this->result(0,'获取成功',json_decode($response['content']));
    }
}
