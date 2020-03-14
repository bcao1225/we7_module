<?php

global $_GPC, $_W;

//如果是普通浏览器访问，或企业微信
if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false && strpos($_SERVER['HTTP_USER_AGENT'], 'wxwork') === false) {
    message('请使用普通微信或企业微信打开');
    exit();
}

if (empty($_W['fans']['nickname'])) {
    mc_oauth_userinfo();
}

$user = pdo_get('ims_book_store_user', ['openid' => $_W['fans']['openid']]);
if ($user === false) {
    pdo_insert('ims_book_store_user', [
        'avatar' => $_W['fans']['avatar'],
        'nickname' => $_W['fans']['nickname'],
        'openid' => $_W['fans']['openid'],
    ]);
    $user = pdo_get('ims_book_store_user', ['id' => pdo_insertid()]);
}


switch ($_GPC['action']) {
    /*导出当前馆别所有书架的书籍*/
    case 'export':
        $guild = pdo_get('ims_book_store_guild', ['id' => $_GPC['guild_id']]);

        $header = ['管藏地', '在馆状态', '书籍编号', '馆内剩余'];
        $index = ['guild_name', 'type_name', 'book_id', 'num'];
        $list = [];

        $bookrack_list = pdo_getall('ims_book_store_bookrack', ['guild_id' => $_GPC['guild_id']]);

        foreach ($bookrack_list as $bookrack) {

            $books = pdo_getall('ims_book_store_book', ['guild_id' => $guild['id'], 'bookrack_id' => $bookrack['id']]);
            foreach ($books as $book) {
                $arr = [];
                $arr['guild_name'] = $guild['name'];
                $arr['type_name'] = pdo_get('ims_book_store_type', ['id' => $book['type']])['name'];
                $bookrack_id = str_pad($bookrack['id'], 3, '0', STR_PAD_LEFT);
                $book_id = str_pad($book['id'], 3, '0', STR_PAD_LEFT);
                $arr['book_id'] = $guild['id'] . $bookrack_id . $book_id;
                $arr['num'] = 1;

                array_push($list, $arr);
            }
        }
        $this->createtable($list, $guild['name'], $header, $index);
        break;
    /*扫描添加馆别管理员二维码*/
    case 'add_admin':
        $arr = ['user_id' => $user['id'], 'guild_id' => $_GPC['guild_id']];

        $relation = pdo_get('ims_book_store_relation', $arr);
        if ($relation) {
            message('当前用户已是此馆别的管理员');
            exit();
        }
        pdo_insert('ims_book_store_relation', $arr);
        message('添加管理员成功', '', 'success');
        break;
    /*扫码进入馆别*/
    case 'qrcode_guild':
        $relation = pdo_get('ims_book_store_relation', ['guild_id' => $_GPC['guild_id'], 'user_id' => $user['id']]);

        if ($relation === false) {
            message('您不是此馆别的管理员');
            exit();
        }

        $guild = pdo_get('ims_book_store_guild', ['id' => $_GPC['guild_id']]);
        include_once $this->template('index');
        break;
    /*进入默认入口*/
    default:
        /*当前用户所管理的管别*/
        $relation_list = pdo_getall('ims_book_store_relation', ['user_id' => $user['id']]);

        if (count($relation_list) === 0) {
            message('你当前没有管理的管别');
            exit();
        }

        /*说明当前用户只管理了一个馆*/
        if (count($relation_list) === 1) {
            $guild = pdo_get('ims_book_store_guild', ['id' => $relation_list[0]['guild_id']]);
            include_once $this->template('index');
            exit();
        }

        include_once $this->template('select_guild');
}