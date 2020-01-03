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
            'voice' => $_GPC['record'],
            'text' => $_GPC['text'],
            'create_time' => time()
        ]);

        $this->result(0, '提交成功', []);
    }

    public function feedback_by_id()
    {
        global $_GPC, $_W;

        $feedback_list = pdo_fetchall('SELECT * FROM ims_machine_feedback_submit WHERE machine_id=' . $_GPC['id'] . ' ORDER BY create_time DESC');

        foreach ($feedback_list as $key => $feedback) {
            $feedback_list[$key]['user'] = pdo_get('ims_machine_feedback_user', ['id' => $feedback['user_id']]);
            $feedback_list[$key]['create_time'] = date('Y-m-d', $feedback['create_time']);
            $feedback_list[$key]['imgs'] = explode(',', $feedback['imgs']);
        }
        $this->result(0, '获取成功', $feedback_list);
    }
}