<?php
/**
 * jz_movie模块小程序接口定义
 *
 * @author QQRPazWaNPgW
 * @url
 */
defined('IN_IA') or exit('Access Denied');

class Jz_movieModuleWxapp extends WeModuleWxapp {

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

    public function __construct(){
        $this->db = new DB($this->other_database);
    }

    /**
     * 获取所有的电影
     */
    public function doPageMovie_all(){
        global $_W,$_GPC;
        if($_GPC['movie_schedule']){
            $list = $this->db->getall('movie',['movie_schedule'=>$_GPC['movie_schedule']]);
        }else{
            $list = $this->db->getall('movie');
        }

        $this->result(0,"获取成功",$list);
    }

    /**
     * 搜索具体电影
     */
    public function doPageSearch_movie(){
        global $_GPC,$_W;
        $sql = "SELECT * FROM movie WHERE movie_name LIKE '%".$_GPC['search']."%'";
        $this->result(0,"搜索成功",$this->db->fetchall($sql));
    }

    /**
     * 通过电影code获取电影
     */
    public function doPageCode_by_movie(){
        global $_GPC,$_W;

        $movie = $this->db->get("movie",['movie_code'=>$_GPC['code']]);

        /*获取导演*/
        $movie['director_code'] =
            $this->db->get("showman",['showman_code'=>$movie['director_code']],'showman_name');

        /*获得监制*/
        $movie['film_producer_code'] =
            $this->db->get("showman",['showman_code'=>$movie['film_producer_code']],'showman_name');

        /*制片人*/
        $movie['producer_code'] =
            $this->db->get("showman",['showman_code'=>$movie['producer_code']],'showman_name');

        /*出品公司*/
        $production_company_code_list = explode(",",$movie['production_company_code']);
        $sql = "SELECT * FROM showman WHERE showman_code IN (";
        foreach ($production_company_code_list as $key=>$item){
            if(end($production_company_code_list)==$item){
                $sql = $sql."'$item'";
            }else{
                $sql = $sql."'$item'".',';
            }
        }
        $sql = $sql.')';
        $movie['production_company_code'] = $this->db->fetchall($sql);

        /*演员*/
        $actor_list = explode(",",$movie['actor_code']);
        $sql = "SELECT * FROM showman WHERE showman_code IN (";
        foreach ($actor_list as $key=>$item){
            if(end($actor_list)==$item){
                $sql = $sql."'$item'";
            }else{
                $sql = $sql."'$item'".',';
            }
        }
        $sql = $sql.')';
        $movie['actor_code'] = $this->db->fetchall($sql);

        /*电影标签*/
        $tag_list = explode(",",$movie['movie_tag_code']);
        $sql = "SELECT * FROM movie_tag WHERE movie_tag_code IN (";
        foreach ($tag_list as $key=>$item){
            if(end($tag_list)==$item){
                $sql = $sql."'$item'";
            }else{
                $sql = $sql."'$item'".',';
            }
        }
        $sql = $sql.')';
        $movie['movie_tag_code'] = $this->db->fetchall($sql);

        $this->result(0,"获取成功",$movie);
    }

    /*传入微信用户信息，换取openid*/
    public function doPageSet_userInfo(){
        global $_W,$_GPC;

        /*通过openid查询当前数据库是否有用户*/
        $user = $this->db->get('web_user',['openid'=>$_W['openid']]);

        /*如果当前用户存在，则将当前用户信息直接返回出来*/
        if($user){
            $this->result(0,'保存成功',$user);
        }

        $this->db->insert('web_user',[
            'web_user_code'=>time().random(3,true),
            'openid'=>$_W['openid'],
            'logo'=>$_GPC['avatarUrl'],
            'nickname'=>$_GPC['nickName'],
            'sex'=>$_GPC['gender'],
            'create_time'=>date('Y-m-d H:i:s',time()),
            'money'=>50000
        ]);

        $this->result(0,'保存成功',$this->db->get('web_user',['openid'=>$_W['openid']]));
    }

    //更新当前用户登录时间
    public function doPageSet_upload_time(){
        global $_W,$_GPC;
        $this->db->update('web_user',['last_longin_time'=>date('Y-m-d H:i:s',time())],['web_user_code'=>$_GPC['web_user_code']]);
        $this->result(0,'更新成功',$this->get_user($_GPC['web_user_code']));
    }

    /*更新用户手机号码*/
    public function doPageSet_phone(){
        global $_W,$_GPC;
        $this->db->update('web_user',['phone'=>$_GPC['phone']],['web_user_code'=>$_GPC['web_user_code']]);
        $this->result(0,'更新成功',$this->get_user($_GPC['web_user_code']));
    }

    /*获取当前用户投资的信息*/
    public function doPageGet_investment(){
        global $_GPC,$_W;
        $list = $this->db->getall('web_user_to_movie',['web_user_code'=>$_GPC['web_user_code']]);
        foreach ($list as $key=>$item){
            $list[$key]['movie'] = $this->db->get('movie',['movie_code'=>$item['movie_code']]);
        }

        $this->result(0,'获取成功',$list);
    }

    /*向电影投资*/
    public function doPageMovie_by_investment(){
        global $_GPC,$_W;
        $user_to_movie = $this->db->get('web_user_to_movie',
            [
                'web_user_code'=>$_GPC['web_user_code'],
                'movie_code'=>$_GPC['movie_code']
            ]);
        if($user_to_movie){
            $this->db->update('web_user_to_movie',
                [
                    'money'=>intval($user_to_movie['money'])+intval($_GPC['money']),
                    'update_time'=>date('Y-m-d H:i:s',time())
                ],
                ['web_user_to_movie'=>$user_to_movie['web_user_to_movie']]);
        }else{
            $this->db->insert('web_user_to_movie',[
                'web_user_code'=>$_GPC['web_user_code'],
                'movie_code'=>$_GPC['movie_code'],
                'web_user_to_movie'=>time().random(3,true),
                'money'=>$_GPC['money'],
                'create_time'=>date('Y-m-d H:i:s',time()),
            ]);
        }

        /*将当前用户的体验金减少*/
        $user = $this->get_user($_GPC['web_user_code']);
        $this->db->update('web_user',['money'=>$user['money']-$_GPC['money']],['web_user_code'=>$_GPC['web_user_code']]);

        $this->result(0,'投资成功',$this->get_user($_GPC['web_user_code']));
    }

    /**
     * 通过传入web_user_code获取用户信息
     */
    public function get_user($web_user_code=''){
        return $this->db->get('web_user',['web_user_code'=>$web_user_code]);
    }
}