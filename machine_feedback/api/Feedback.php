<?php

require_once __DIR__ . '/Api.php';
defined('IN_IA') or exit('Access Denied');

class Feedback extends Api
{
    //提交反馈
    public function submit()
    {
        global $_GPC;

        //通过openid获取id
        $user = pdo_get('ims_machine_feedback_user', ['openid' => $_GPC['openid']]);
        pdo_insert('ims_machine_feedback_submit', [
            'user_id' => $user['id'],
            'machine_id' => $_GPC['machine_id'],
            'imgs' => $_GPC['imgs'],
            'video' => $_GPC['video'],
            'text'=>$_GPC['text'],
            'create_time' => time()
        ]);

        $this->result(0, '提交成功', []);
    }
}