<?php

global $_W, $_GPC;

switch ($_GPC['action']) {
    case 'add':
        if ($_W['ispost']) {
            pdo_insert('ims_book_store_type', ['name' => $_GPC['name'], 'color' => $_GPC['color']]);
            message('保存成功', $this->createWebUrl('type_manager'), 'success');
        }
        include_once $this->template('type/add_type');
        break;
    case 'delete':
        pdo_delete('ims_book_store_type', ['id' => $_GPC['id']]);
        message('删除成功', $this->createWebUrl('type_manager'), 'success');
        break;
    case 'update':
        $type = pdo_get('ims_book_store_type', ['id' => $_GPC['id']]);
        include_once $this->template('type/add_type');
        break;
    default:
        $type_list = pdo_getall('ims_book_store_type');
        include_once $this->template('type/type_manager');
}