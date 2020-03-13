<?php

global $_W, $_GPC;

switch ($_GPC['action']) {
    /*生成添加馆别管理员的二维码，ajax请求*/
    case 'create_admin':
        $url = $this->make_qrcode($_W['siteroot'] . 'app/' . $this->createMobileUrl('index') . '&action=add_admin&guild_id=' . $_GPC['guild_id']);
        $this->result(0, '获取成功', $url);
        break;
    /*更改用户的管别*/
    case 'update_guild':
        /*保存在关系表中*/
        foreach ($_GPC['guild_id'] as $guild_id) {
            pdo_insert('ims_book_store_relation', ['guild_id' => $guild_id, 'user_id' => $_GPC['user_id']]);
        }
        message('保存成功', $this->createWebUrl('user_manager'), 'success');
        break;
    default:
        $guild_list = pdo_getall('ims_book_store_guild');
        $user_list = pdo_getall('ims_book_store_user');

        foreach ($user_list as $index => $user) {
            $relation_list = pdo_getall('ims_book_store_relation', ['user_id' => $user['id']]);

            $arr = [];
            foreach ($relation_list as $relation) {
                array_push($arr, pdo_get('ims_book_store_guild', ['id' => $relation['guild_id']]));
            }

            $user_list[$index]['guild_list'] = $arr;
        }

        include_once $this->template('user_manager');
}
