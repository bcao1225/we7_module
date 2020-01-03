<?php

//机器类
require_once __DIR__ . '/Api.php';
defined('IN_IA') or exit('Access Denied');

class Machine extends Api
{
    /*获取机器数据*/
    public function machine_message()
    {
        global $_GPC, $_W;
        $machine = pdo_get('ims_machine_feedback_machine', ['id' => $_GPC['id']]);
        $imgs = iunserializer($machine['imgs']);

        foreach ($imgs as $img_key => $img) {
            $imgs[$img_key] = tomedia($img);
        }
        $machine['imgs'] = $imgs;
        $this->result(0, '获取成功', $machine);
    }
}