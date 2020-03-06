<?php

global $_W, $_GPC;

switch ($_GPC['action']) {
    case 'add':
        if ($_W['ispost']) {
            pdo_insert('ims_book_store_type',
                [
                    'id' => $_GPC['id'],
                    'name' => $_GPC['name'],
                    'color' => $_GPC['color']
                ], true);
            message('保存成功', $this->createWebUrl('type_manager'), 'success');
        }

        $type = pdo_get('ims_book_store_type', ['id' => $_GPC['id']]);

        include_once $this->template('type/add_type');
        break;
    /*是否禁用状态*/
    case 'hidden':
        pdo_update('ims_book_store_type', ['hidden' => $_GPC['hidden']], ['id' => $_GPC['id']]);
        message('修改成功', $this->createWebUrl('type_manager'), 'success');
        break;
    default:
        $type_list = pdo_getall('ims_book_store_type');
        include_once $this->template('type/type_manager');
}