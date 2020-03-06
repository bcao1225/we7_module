<?php

global $_W, $_GPC;

switch ($_GPC['action']) {
    /*更改用户的状态*/
    case 'update':
        pdo_update('ims_book_store_user', ['is_pass' => $_GPC['is_pass']], ['id' => $_GPC['user_id']]);
        message('更改成功', $this->createWebUrl('user_manager'), 'success');
        break;
    default:
        $user_list = pdo_getall('ims_book_store_user');
        include_once $this->template('user_manager');
}
