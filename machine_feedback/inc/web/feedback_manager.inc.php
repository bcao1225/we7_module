<?php

global $_W, $_GPC;

switch ($_GPC['action']) {
    case 'delete':
        break;
    default:
        $submit_list = pdo_getall('ims_machine_feedback_submit', ['machine_id' => $_GPC['machine_id']]);
        foreach ($submit_list as $submit_key => $submit) {
            $submit_list[$submit_key]['imgs'] = explode(',',$submit['imgs']);
            $user = pdo_get('ims_machine_feedback_user', ['id' => $submit['user_id']]);
            $submit_list[$submit_key]['user'] = $user;
        }
        include_once $this->template('feedback/feedback_manager');
}