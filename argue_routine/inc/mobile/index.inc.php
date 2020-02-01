<?php

global $_W, $_GPC;

//如果是普通浏览器访问
if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
    message('请使用微信打开', '', 'error');
}


/*获取粉丝信息*/
if (empty($_W['fans']['nickname'])) {
    mc_oauth_userinfo();
}

/*此文件专门用于路径跳转*/
switch ($_GPC['template']) {
    case 'activity':
        $activity = pdo_get('ims_argue_routine_activity', ['id' => $_GPC['activity_id']]);
        $_share = array(
            'desc' => $activity['share_body'],
            'title' => $activity['share_title'],
            'imgUrl' => tomedia($activity['share_img'])
        );          include_once $this->template('activity');
        break;
    default:
        include_once $this->template('index');
}


