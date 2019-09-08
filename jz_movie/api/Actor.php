<?php
require_once __DIR__.'/Api.php';

//投资信息
defined('IN_IA') or exit('Access Denied');

class Actor extends Api{
    /**
     * 通过传入code获取演员信息
     */
    public function get_actor()
    {
        global $_GPC, $_W;
        $actor = $this->db->get('showman', ['showman_code' => $_GPC['code']]);

        $this->result(0, '获取成功', $actor);
    }
}