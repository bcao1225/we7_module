<?php

require_once __DIR__ . '/Api.php';

defined('IN_IA') or exit('Access Denied');

class WxPort extends Api{

    //发送模板消息
    public function tm_send(){
        //未超时，超时后才发送模板消息
        if(cache_read('tm_send_time')!=''&&cache_read('tm_send_time')>=time()){
            $this->result(0,'当前未到发送模板消息的时间',cache_read('tm_send_time'));
        }

        //设置新的6天后发送模板消息
        cache_write('tm_send_time',time()+6*24*60*60);

        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$this->get_access_token();

        $web_user_list = $this->db->getall('web_user');
        $earnings_list = $this->db->fetchall('SELECT web_user_code,sum(earnings) as earnings FROM web_user_to_movie GROUP BY web_user_code');

        foreach ($web_user_list as $web_user){
            if($web_user['form_id']=='') continue;

            foreach ($earnings_list as $earnings){
                if($earnings['web_user_code']==$web_user['web_user_code']){
                    $data = [
                        'touser'=>$web_user['openid'],
                        'template_id'=>'h2xlDJ5yYlBrSgOIa6ZaAeukyPFwSjB2gGpZhgR_d7M',
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
                    ihttp_post($url,json_encode($data));
                    break;
                }

            }
        }
    }
}

