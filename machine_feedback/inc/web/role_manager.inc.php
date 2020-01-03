<?php

global $_W, $_GPC;

switch ($_GPC['action']) {
    //权限变更
    case 'role':
        pdo_update('ims_machine_feedback_user', ['role' => $_GPC['role']], ['id' => $_GPC['user_id']]);
        message('变更成功', $this->createWebUrl('role_manager').'&role='.$_GPC['role'].'&action=select_role', 'success');
        break;
    //获取选中权限对应的用户
    case 'select_role':
        $user_list = pdo_getall('ims_machine_feedback_user', ['role' => $_GPC['role']]);
        foreach ($user_list as $user_key=>$user){
            $user_list[$user_key]['submit_list'] = pdo_getall('ims_machine_feedback_submit',['user_id'=>$user['id']]);
        }

        include_once $this->template('role/role_manager');
        break;
    default:
        $user_list = pdo_getall('ims_machine_feedback_user', ['role' => 1]);
        include_once $this->template('role/role_manager');
}

