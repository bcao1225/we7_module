<?php

global $_W, $_GPC;

/*获取粉丝信息*/
if (empty($_W['fans']['nickname'])) {
    mc_oauth_userinfo();
}

/*此文件专门用于路径跳转*/
switch ($_GPC['template']) {
    case 'activity':
        $activity = pdo_get('ims_argue_routine_activity', ['id' => $_GPC['activity_id']]);
        /*自定义分享*/
        $_share = array(
            'desc' => $activity['share_body'],
            'title' => $activity['share_title'],
            'imgUrl' => tomedia($activity['share_img']),
        );

        include_once $this->template('activity');
        break;
    default:
        include_once $this->template('index');
}


