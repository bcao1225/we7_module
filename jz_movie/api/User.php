<?php

require_once __DIR__ . '/Api.php';

defined('IN_IA') or exit('Access Denied');

class User extends Api
{

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
            'web_user_code' => random(2, true) . time() . random(3, true),
            'openid' => $_W['openid'],
            'logo' => $_GPC['avatarUrl'],
            'nickname' => $_GPC['nickName'],
            'sex' => $_GPC['gender'],
            'create_time' => date('Y-m-d H:i:s', time()),
            'experience_of_gold' => 50000,
            'user_type'=>2
        ]);

        $this->result(0, '保存成功', $this->db->get('web_user', ['openid' => $_W['openid']]));
    }

    /**
     * 更新用户信息
     */
    public function update_user(){
        global $_GPC;
        $this->db->update('web_user',['nickname'=>$_GPC['nickname'],'logo'=>$_GPC['avatarUrl']],['openid'=>$_GPC['openid']]);
        $this->result(0,'更新成功',$this->get_user($_GPC['openid']));
    }

    /**
     * 通过openid获取用户信息
     */
    public function openid_by_user()
    {
        global $_GPC;
        $this->result(0, '获取成功', $this->get_user($_GPC['openid']));
    }

    //更新当前用户登录时间
    public function set_upload_time()
    {
        global $_W, $_GPC;
        $this->db->update('web_user', ['last_longin_time' => date('Y-m-d H:i:s', time())], ['openid' => $_GPC['openid']]);
        $this->result(0, '更新成功', $this->get_user($_GPC['openid']));
    }

    /*更新用户手机号码*/
    public function set_phone()
    {
        global $_W, $_GPC;
        $this->db->update('web_user', ['phone' => $_GPC['phone']], ['openid' => $_GPC['openid']]);
        $this->result(0, '更新成功', $this->get_user($_GPC['openid']));
    }

    /**
     * 获取用户的收入明细
     */
    public function get_money_detail()
    {
        global $_GPC;
        $income_list = [];

        $user = $this->db->get('web_user', ['openid'=>$_GPC['openid']]);


        //讲注册时送的50000块钱填入数组
        array_unshift($income_list,['money'=>50000,'message'=>'注册所得','create_time'=>$user['create_time']]);

        /**
         * 获取当前用户的支出明细
         */
        $list = $this->db->fetchall('SELECT * FROM web_user_to_movie WHERE web_user_code = :web_user_code ORDER BY create_time DESC'
            ,[':web_user_code'=>$user['web_user_code']]);

        /**
         * 获取当前用户的支出明细
         */

        foreach ($list as $key=>$item){
            $list[$key]['movie'] = $this->db->get('movie',['movie_code'=>$item['movie_code']]);
        }

        /**
         * 获取当前用户的收入明细。
         * 1、判断当前每条数据对应的movie是否是分红状态，如果是，才将当前收入信息展示
         */
        foreach ($list as $key=>$item){
            if($item['movie']['movie_schedule']==6){
                array_push($income_list,$item);
            }
        }

        //第一个参数为支出明细，第二个为收入明细
        $this->result(0,"获取成功",[$list,$income_list]);
    }

    /**
     * 解密手机号
     */
    public function decode_phone()
    {
        include_once __DIR__ . "/../decode_phone/wxBizDataCrypt.php";

        global $_GPC, $_W;

        $pc = new WXBizDataCrypt($this->appid, $_GPC['sessionKey']);
        $errCode = $pc->decryptData($_GPC['encryptedData'], $_GPC['iv'], $data);

        if($data==null){
            $message = '访问量过大，获取手机号失败，请重试';
        }else{
            $message = '获取成功';
        }

        $this->result(0, $message, $data);
    }

    /**
     * 传入wx.login获取code，换取session_key
     */
    public function get_session_key()
    {
        global $_GPC;
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $this->appid . '&secret=' . $this->app_secret . '&js_code=' . $_GPC['code'] . '&grant_type=authorization_code';
        $response = ihttp_get($url);
        $this->result(0, '获取成功', json_decode($response['content']));
    }
}
