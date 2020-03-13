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
} else {
    $user = pdo_get('ims_book_store_user', ['openid' => $_W['fans']['openid']]);
}

switch ($_GPC['action']) {
    /*扫描添加馆别管理员二维码*/
    case 'add_admin':
        $arr = ['user_id' => $user['id'], 'guild_id' => $_GPC['guild_id']];

        $relation = pdo_get('ims_book_store_relation', $arr);
        if ($relation) {
            message('当前用户已是此馆别的管理员');
            exit();
        }
        pdo_insert('ims_book_store_relation', $arr);
        message('添加管理员成功');

        break;
    /*扫描进入具体馆别的二维码*/
    default:
        $relation_list = pdo_getall('ims_book_store_relation', ['user_id' => $user['id']]);

        if (count($relation_list) === 0) {
            message('您不是此馆别的管理员');
            exit();
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
        $guild = pdo_get('ims_book_store_guild', ['id' => $_GPC['id']]);
        include_once $this->template('index');
}