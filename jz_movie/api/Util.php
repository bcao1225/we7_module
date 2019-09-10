<?php

require_once __DIR__ . '/Api.php';

defined('IN_IA') or exit('Access Denied');

class Util extends Api
{

    public $url = 'http://www.w7.com/app/index.php?i=2&t=0&v=1.0&from=wxapp&c=entry&a=wxapp&do=get_data&state=we7sid-c2ffcb788de0872d146bd0f4fa39bf8f&m=jz_movie&sign=9b88067cda3a27823e2c83a88e92d521&table_name=web_user_to_movie&clazz=Util';
    /**
     * 获取某一个数据库的所有信息，将信息返回出去
     */
    public function get_data(){
        global $_GPC;
        exit(var_dump($this->db->getall($_GPC['table_name'])));
    }
}
