<?php

global $_W, $_GPC;

$root_url = [
    'url' => $_W['siteroot'] . 'app/' . $this->createMobileUrl('index'),
    'qrcode' => $this->make_qrcode($root_url['url'])
];

switch ($_GPC['action']) {
    /*添加馆别*/
    case 'add':
        if ($_W['ispost']) {
            $arr = ['id' => $_GPC['guild_id'], 'name' => $_GPC['guild_name']];
            $rest = pdo_insert('ims_book_store_guild', $arr);

            if ($rest) {
                message('保存成功', $this->createWebUrl('guild_manager'), 'success');
            } else {
                message('具有相同编号的馆别', $this->createWebUrl('guild_manager'), 'error');
            }
        }
        include_once $this->template('add_guild');
        break;
    /*移除用户权限*/
    case 'delete_admin':
        pdo_delete('ims_book_store_relation', ['guild_id' => $_GPC['guild_id'], 'user_id' => $_GPC['user_id']]);
        message('删除成功', $this->createWebUrl('guild_manager'), 'success');
        break;
    /*删除会馆*/
    case 'delete':
        pdo_delete('ims_book_store_guild', ['id' => $_GPC['id']]);
        message('删除成功', $this->createWebUrl('guild_manager'), 'success');
        break;
    default:
        $guild_list = pdo_getall('ims_book_store_guild');

        foreach ($guild_list as $index => $item) {
            $qr_code_url = $_W['siteroot'] . 'app/' . $this->createMobileUrl('index') . '&action=qrcode_guild&guild_id=' . $item['id'];
            $guild_list[$index]['qr_code_url'] = $qr_code_url;
            $guild_list[$index]['qr_code'] = $this->make_qrcode($qr_code_url);

            /*关系表*/
            $relation_list = pdo_getall('ims_book_store_relation', ['guild_id' => $item['id']]);

            $user_list = [];
            foreach ($relation_list as $relation) {
                array_push($user_list, pdo_get('ims_book_store_user', ['id' => $relation['user_id']]));
            }

            $guild_list[$index]['user_list'] = $user_list;
        }

        include_once $this->template('guild_manager');
}
