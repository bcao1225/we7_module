<?php

global $_GPC, $_W;

$activity = pdo_get('ims_argue_routine_activity', ['id' => $_GPC['activity_id']]);

switch ($_GPC['action']) {
    /*基本设置*/
    case 'base':
        if ($_W['ispost']) {
            $data = [
                'title' => $_GPC['title'],
                'intro' => $_GPC['intro'],
                'virtual_user' => $_GPC['virtual_user'],
            ];

            if ($activity !== false) {
                pdo_update('ims_argue_routine_activity', $data, ['id' => $activity['id']]);
                message('更新成功', $this->createWebUrl('activity_setting') . '&action=base&activity_id=' . $activity['id']);
            } else {
                $data['create_time'] = time();
                pdo_insert('ims_argue_routine_activity', $data);
                message('保存成功', $this->createWebUrl('activity_setting') . '&action=base&activity_id=' . pdo_insertid());
            }
        }
        include_once $this->template('activity/setting/base_setting');
        break;
    /*正反方设置*/
    case 'both':
        if ($_W['ispost']) {
            pdo_update('ims_argue_routine_activity', [
                /*正方*/
                'square' => $_GPC['square'],
                'square_color' => $_GPC['square_color'],
                /*反方*/
                'no_square' => $_GPC['no_square'],
                'no_square_color' => $_GPC['no_square_color'],
                /*开始时间*/
                'start_time' => $_GPC['time']['start'],
                /*结束时间*/
                'end_time' => $_GPC['time']['end'],
            ], ['id' => $_GPC['id']]);

            message('保存成功', $this->createWebUrl('activity_setting') . '&action=both&activity_id=' . $_GPC['id']);
        }

        $activity['time'] = ['start' => $activity['start_time'], 'end' => $activity['end_time']];
        include_once $this->template('activity/setting/both_setting');
        break;
    /*广告设置*/
    case 'advertising':
        if ($_W['ispost']) {
            pdo_update('ims_argue_routine_activity', [
                /*广告*/
                'advertising_img' => $_GPC['advertising_img'],
                'advertising_url' => $_GPC['advertising_url'],
            ], ['id' => $_GPC['activity_id']]);

            message('保存成功', $this->createWebUrl('activity_setting') . '&action=advertising&activity_id=' . $_GPC['activity_id']);
        }
        include_once $this->template('activity/setting/advertising_setting');
        break;
    /*奖金池*/
    case 'bonus':
        if ($_W['ispost']) {
            pdo_update('ims_argue_routine_activity', [
                /*奖金池*/
                'bonus_pools' => $_GPC['bonus_pools'],
                'bonus_name' => $_GPC['bonus_name'],
                'bonus_desc' => $_GPC['bonus_desc'],
                'bonus_content'=>$_GPC['bonus_content']
            ], ['id' => $_GPC['activity_id']]);
            message('保存成功', $this->createWebUrl('activity_setting') . '&action=bonus&activity_id=' . $_GPC['activity_id']);
        }
        include_once $this->template('activity/setting/bonus_setting');
        break;
    /*奖金池*/
    case 'share':
        if ($_W['ispost']) {
            pdo_update('ims_argue_routine_activity', [
                /*分享*/
                'share_title' => $_GPC['share_title'],
                'share_body' => $_GPC['share_body'],
                'share_img' => $_GPC['share_img'],
            ], ['id' => $_GPC['activity_id']]);
            message('保存成功', $this->createWebUrl('activity_setting') . '&action=share&activity_id=' . $_GPC['activity_id']);
        }
        include_once $this->template('activity/setting/share_setting');
        break;
}
