<?php

global $_GPC, $_W;

//如果是普通浏览器访问
if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
    message('请使用微信客户端打开', '', 'error');
}

if (empty($_W['fans']['nickname'])) {
    mc_oauth_userinfo();
}

$user = pdo_get('ims_book_store_user', ['openid' => $_W['fans']['openid']]);

if (!$user) {
    pdo_insert('ims_book_store_user', [
        'avatar' => $_W['fans']['avatar'],
        'nickname' => $_W['fans']['nickname'],
        'openid' => $_W['fans']['openid'],
    ]);
}

if($user['is_pass']==='0'){
    message('您没有权限访问');
    exit();
}


switch ($_GPC['action']) {
    default:
        $guild = pdo_get('ims_book_store_guild', ['id' => $_GPC['id']]);
        include_once $this->template('index');
}