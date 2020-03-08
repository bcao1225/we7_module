<?php

global $_GPC, $_W;

//如果是普通浏览器访问，或企业微信
if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'wxwork') === false) {
    message('请使用普通微信或企业微信打开');
    exit();
}

if (empty($_W['fans']['nickname'])) {
    mc_oauth_userinfo();

    $user = pdo_get('ims_book_store_user', ['openid' => $_W['fans']['openid']]);
    if ($user === false) {
        pdo_insert('ims_book_store_user', [
            'avatar' => $_W['fans']['avatar'],
            'nickname' => $_W['fans']['nickname'],
            'openid' => $_W['fans']['openid'],
        ]);
        $user = pdo_get('ims_book_store_user', ['id' => pdo_insertid()]);
    }
}

$user = pdo_get('ims_book_store_user', ['openid' => $_W['fans']['openid']]);

$relation_list = pdo_getall('ims_book_store_relation', ['user_id' => $user['id']]);

if (count($relation_list) === 0) {
    message('您不是此馆别的管理员');
}

foreach ($relation_list as $index => $relation) {
    if ($relation['guild_id'] !== $_GPC['id']) {

        if ($index === (count($relation_list) - 1)) {
            message('您不是此馆别的管理员');
            exit();
        }
        continue;
    }
}

switch ($_GPC['action']) {
    default:
        $guild = pdo_get('ims_book_store_guild', ['id' => $_GPC['id']]);
        include_once $this->template('index');
}