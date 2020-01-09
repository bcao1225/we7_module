<?php

global $_W, $_GPC;

switch ($_GPC['action']) {
    case 'red_packet':
        $user = pdo_get('ims_argue_routine_user', ['id' => $_GPC['user_id']]);

        $content = $this->send_redpacket($_GPC['activity_id'], $user['openid'], $_GPC['money']);

        if($content['return_msg']==='发送成功'){
            pdo_insert('ims_argue_routine_red_packet', [
                'user_id' => $user['id'],
                'money' => $_GPC['money'],
                'order_number' => $content['send_listid'],
                'create_time' => time()
            ]);

            message('红包发送成功', $this->createWebUrl('red_packet_record'), 'success');
        }
        break;
    default:
        $argue_list = pdo_fetchall('SELECT * FROM ims_argue_routine_user WHERE activity_id=' . $_GPC['activity_id'] . ' AND viewpoint=1');
        $no_argue_list = pdo_fetchall('SELECT * FROM ims_argue_routine_user WHERE activity_id=' . $_GPC['activity_id'] . ' AND viewpoint=0');
        $percent = $this->percent($_GPC['activity_id']);
        $user_list = [$argue_list, $no_argue_list];
        /*当前活动*/
        $activity = pdo_get('ims_argue_routine_activity', ['id' => $_GPC['activity_id']]);

        include_once $this->template('user/user_manager');
        break;
}