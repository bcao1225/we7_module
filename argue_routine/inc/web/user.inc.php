<?php

global $_W, $_GPC;

switch ($_GPC['action']) {
    /*删除当前活动中某个点赞的user*/
    case 'delete':
        /*先删除当前点赞和踩的记录*/
        pdo_delete('ims_argue_routine_like', ['user_id' => $_GPC['user_id']]);
        /*然后删除当前用户*/
        pdo_delete('ims_argue_routine_user', ['id' => $_GPC['user_id']]);
        message('删除成功', $this->createWebUrl('user') . '&activity_id=' . $_GPC['activity_id']);
        break;
    /*发送红包*/
    case 'red_packet':
        $user = pdo_get('ims_argue_routine_user', ['id' => $_GPC['user_id']]);

        $content = $this->send_redpacket($_GPC['activity_id'], $user['openid'], $_GPC['money']);

        if ($content['return_msg'] === '发放成功') {
            pdo_insert('ims_argue_routine_red_packet', [
                'user_id' => $user['id'],
                'money' => $_GPC['money'],
                'order_number' => $content['send_listid'],
                'create_time' => time()
            ]);

            /*将当前奖金池的奖金减少*/
            pdo_query('UPDATE ims_argue_routine_activity SET bonus_pools=bonus_pools-' . $_GPC['money'] . ' WHERE id=' . $_GPC['activity_id']);

            message('红包发送成功', $this->createWebUrl('red_packet_record'), 'success');
        } else {
            message($content['return_msg'], $this->createWebUrl('user') . '&activity_id=' . $_GPC['activity_id'], 'error');
        }
        break;
    default:
        $argue_list = pdo_fetchall('SELECT * FROM ims_argue_routine_user WHERE activity_id=' . $_GPC['activity_id'] . ' AND viewpoint=1');

        /*获取点赞数*/
        foreach ($argue_list as $key => $item) {
            if ($item['comment'] === '') continue;
            $argue_list[$key]['like'] =
                count(pdo_getall('ims_argue_routine_like', ['user_id' => $item['id'], 'like_or_dislike' => 1]));
        }

        $no_argue_list = pdo_fetchall('SELECT * FROM ims_argue_routine_user WHERE activity_id=' . $_GPC['activity_id'] . ' AND viewpoint=0');

        /*获取反方点赞数*/
        foreach ($argue_list as $key => $item) {
            if ($item['comment'] === '') continue;
            $no_argue_list[$key]['like'] =
                count(pdo_getall('ims_argue_routine_like', ['user_id' => $item['id'], 'like_or_dislike' => 1]));
        }

        $percent = $this->percent($_GPC['activity_id']);
        $user_list = [$argue_list, $no_argue_list];

        /*当前活动*/
        $activity = pdo_get('ims_argue_routine_activity', ['id' => $_GPC['activity_id']]);

        include_once $this->template('user/user_manager');
        break;
}