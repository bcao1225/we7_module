<?php

global $_GPC, $_W;

//活动接口文件
switch ($_GPC['action']) {
    //获取所有活动
    case 'get_activity_list':
        $activity_list = pdo_getall('ims_argue_routine_activity');
        /*将参加活动的人数和虚拟人数相加，并转换为以万为单位的数字*/
        foreach ($activity_list as $activity_key => $activity) {
            $activity_list[$activity_key] = tidy_activity($activity);
        }
        exit(json_encode($activity_list));
    //获取一个活动的数据
    case 'get_activity':
        $activity = pdo_get('ims_argue_routine_activity', ['id' => $_GPC['activity_id']]);
        exit(json_encode(tidy_activity($activity)));
}

/**
 * 整理活动数据
 * @param array $activity
 * @return array 整理后的互动
 */
function tidy_activity($activity)
{
    /*背景图*/
    $activity['back_img'] = tomedia($activity['back_img']);
    /*全部参加人数，虚拟人数加实际人数*/
    $activity['count'] = count(pdo_getall('ims_argue_routine_user', ['activity_id' => $activity['id']])) + $activity['virtual_user'];
    $activity['advertising_img'] = tomedia($activity['advertising_img']);
    /*分享图片*/
    $activity['share_img'] = tomedia($activity['share_img']);
    /*开始时间时间戳*/
    $activity['start_time_timestamp'] = strtotime($activity['start_time']);
    /*结束时间时间戳*/
    $activity['end_time_timestamp'] = strtotime($activity['end_time']);
    /*将奖励规则解码*/
    $activity['bonus_content'] = htmlspecialchars_decode($activity['bonus_content']);

    return $activity;
}
