<?php

global $_W, $_GPC;

switch ($_GPC['action']) {
    /*添加馆别*/
    case 'add':
        if ($_W['ispost']) {
            $arr = ['id' => $_GPC['guild_id'], 'name' => $_GPC['guild_name']];
            pdo_insert('ims_book_store_guild', $arr);
            message('保存成功', $this->createWebUrl('guild_manager'), 'success');
        }
        include_once $this->template('add_guild');
        break;
    /*删除会馆*/
    case 'delete':
        pdo_delete('ims_book_store_guild', ['id' => $_GPC['id']]);
        message('删除成功', $this->createWebUrl('guild_manager'), 'success');
        break;
    default:
        $guild_list = pdo_getall('ims_book_store_guild');

        foreach ($guild_list as $index => $item) {
            $qr_code_url = $_W['siteroot'] . 'app/' . $this->createMobileUrl('index').'&id='.$item['id'];
            $guild_list[$index]['qr_code_url'] = $qr_code_url;
            $guild_list[$index]['qr_code'] = $this->make_qrcode($qr_code_url);
        }

        include_once $this->template('guild_manager');
}
