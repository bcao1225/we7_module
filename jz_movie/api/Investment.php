<?php

require_once __DIR__.'/Api.php';

//投资信息
defined('IN_IA') or exit('Access Denied');

class Investment extends Api{

    /*获取用户投资的信息*/
    public function get_investment()
    {
        global $_GPC, $_W;
        $list = $this->db->getall('web_user_to_movie', ['web_user_code' => $_GPC['web_user_code']]);
        foreach ($list as $key => $item) {
            $list[$key]['movie'] = $this->db->get('movie', ['movie_code' => $item['movie_code']]);
        }

        $this->result(0, '获取成功', $list);
    }

    /*向电影投资*/
    public function movie_by_investment()
    {
        global $_GPC, $_W;
        $user_to_movie = $this->db->get('web_user_to_movie',
            [
                'web_user_code' => $_GPC['web_user_code'],
                'movie_code' => $_GPC['movie_code']
            ]);
        if ($user_to_movie) {
            $this->db->update('web_user_to_movie',
                [
                    'money' => intval($user_to_movie['money']) + intval($_GPC['money']),
                    'update_time' => date('Y-m-d H:i:s', time())
                ],
                ['web_user_to_movie' => $user_to_movie['web_user_to_movie']]);
        } else {
            $this->db->insert('web_user_to_movie', [
                'web_user_code' => $_GPC['web_user_code'],
                'movie_code' => $_GPC['movie_code'],
                'web_user_to_movie' => random(2,true).time() . random(3, true),
                'money' => $_GPC['money'],
                'create_time' => date('Y-m-d H:i:s', time()),
            ]);
        }

        /*将当前用户的体验金减少*/
        $user = $this->get_user($_GPC['web_user_code']);
        $this->db->update('web_user', ['experience_of_gold' => $user['experience_of_gold'] - $_GPC['money']], ['web_user_code' => $_GPC['web_user_code']]);

        /*将当前电影的募集金额增加*/
        $movie = $this->db->get('movie', ['movie_code' => $user_to_movie['movie_code']]);

        $money1 = (float)$movie['yet_collect'];
        $money2 = (float)$_GPC['money'];
        $movie['yet_collect'] = $money1+$money2;

        $this->db->query("UPDATE movie SET yet_collect=:yet_collect WHERE movie_code=:movie_code"
            , [
                ':yet_collect' => $movie['yet_collect'],
                ':movie_code' => $movie['movie_code']
            ]);


        $this->result(0, '投资成功', ['movie'=>$movie,'user'=>$this->get_user($user_to_movie['web_user_code'])]);
    }

    /**
     * 增加当前募集的百分比
     */
    public function set_percentage(){
        global $_GPC,$_W;
        //保留两位小数
        $_GPC['movie_schedule_percent'] = round($_GPC['movie_schedule_percent'],2);
        $this->db->query("UPDATE movie SET movie_schedule_percent=:movie_schedule_percent WHERE movie_code=:movie_code",
            [':movie_schedule_percent'=>$_GPC['movie_schedule_percent'],':movie_code'=>$_GPC['movie_code']]
        );
        $this->result(0,'保存成功',[]);
    }
}