<?php

global $_GPC, $_W;

switch ($_GPC['action']) {
    default:
        $activity_list = pdo_getall('ims_argue_routine_activity');
        foreach ($activity_list as $activity_key => $activity) {
            $url = $_W['siteroot'] . 'app/' . $this->createMobileUrl('index') . '&template=activity&activity_id=' . $activity['id'];

            $activity_list[$activity_key]['qrcode'] = $this->make_qrcode($url);
            $activity_list[$activity_key]['url'] = $url;

        }
        include_once $this->template('activity/activity_manager');
        break;
}

