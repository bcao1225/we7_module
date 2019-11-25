<?php

global $_GPC, $_W;

$user_list = [];

switch ($_GPC['action']) {
    case 'query':
        if($_GPC['type']==='0'){
            $user_list = pdo_getall('ims_gather_feedback_user',['is_submit'=>0]);
        }else{
            $user_list = pdo_getall('ims_gather_feedback_user',['is_submit'=>1]);
        }
        break;
    case 'delete':
        pdo_delete('ims_gather_feedback_user',['id'=>$_GPC['id']]);
        break;
    default:
        $user_list = pdo_getall('ims_gather_feedback_user');
        break;
}

include_once $this->template('user/user_manager');


