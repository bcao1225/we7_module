<?php

global $_GPC, $_W;

switch ($_GPC['action']) {
    //添加或修改活动
    case 'edit_activity':
        if ($_W['ispost']) {
            pdo_insert('ims_argue_routine_activity', [
                'id' => $_GPC['id'],
                'title' => $_GPC['title'],
                'intro' => $_GPC['intro'],
                'virtual_user' => $_GPC['virtual_user'],
                /*正方*/
                'square' => $_GPC['square'],
                'square_color'=>$_GPC['square_color'],
                /*反方*/
                'no_square' => $_GPC['no_square'],
                'no_square_color'=>$_GPC['no_square_color'],
                /*开始时间*/
                'start_time' => $_GPC['time']['start'],
                /*结束时间*/
                'end_time' => $_GPC['time']['end'],
                'create_time' => time()
            ], true);

            message('保存成功', $this->createWebUrl('activity_manager'), 'success');
        }

        $activity = pdo_get('ims_argue_routine_activity', ['id' => $_GPC['id']]);
        $activity['time'] = ['start' => $activity['start_time'], 'end' => $activity['end_time']];

        include_once $this->template('activity/activity_setting');
        break;
    default:
        $activity_list = pdo_getall('ims_argue_routine_activity');
        foreach ($activity_list as $activity_key => $activity) {
            $activity_list[$activity_key]['qrcode'] = $this->make_qrcode(
                $_W['siteroot'].'app/'.$this->createMobileUrl('index') . '&template=activity&activity_id=' . $activity['id']
            );
        }
        include_once $this->template('activity/activity_manager');
        break;
}

