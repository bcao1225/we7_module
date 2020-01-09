<?php

global $_GPC, $_W;

//活动接口文件
switch ($_GPC['action']) {
    //获取所有活动
    case 'get_activity_list':
        $activity_list = pdo_getall('ims_argue_routine_activity');
        /*将参加活动的人数和虚拟人数相加，并转换为以万为单位的数字*/
        foreach ($activity_list as $activity_key => $activity) {
            $activity_list[$activity_key]['count'] = get_count($activity);
        }

        exit(json_encode($activity_list));
    //获取一个活动的数据
    case 'get_activity':
        $activity = pdo_get('ims_argue_routine_activity', ['id' => $_GPC['activity_id']]);
        $activity['count'] = get_count($activity);
        $activity['advertising_img'] = tomedia($activity['advertising_img']);
        exit(json_encode($activity));
}

function get_count($activity)
{
    //实际参加本次活动的人数
    $practical_count = pdo_fetchall('SELECT COUNT(*) as count FROM ims_argue_routine_user WHERE activity_id=' . $activity['id'])['count'];
    $count = $practical_count + $activity['virtual_user'];
    return number_format($count / 10000, 1);
}
