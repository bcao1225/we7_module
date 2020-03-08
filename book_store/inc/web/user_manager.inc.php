<?php

global $_W, $_GPC;

switch ($_GPC['action']) {
    /*更改用户的管别*/
    case 'update_guild':
        pdo_update('ims_book_store_user', ['admin' => $_GPC['guild_id']], ['id' => $_GPC['user_id']]);
        message('保存成功',$this->createWebUrl('user_manager'),'success');
        break;
    /*更改用户的状态*/
    case 'update':
        pdo_update('ims_book_store_user', ['is_pass' => $_GPC['is_pass']], ['id' => $_GPC['user_id']]);
        message('更改成功', $this->createWebUrl('user_manager'), 'success');
        break;
    default:
        $user_list = pdo_getall('ims_book_store_user');
        foreach ($user_list as $index=>$user){
            $user_list[$index]['guild'] = pdo_get('ims_book_store_guild',['id'=>$user['admin']]);
        }
        /*获取所有管别*/
        $guild_list = pdo_getall('ims_book_store_guild');
        include_once $this->template('user_manager');
}
