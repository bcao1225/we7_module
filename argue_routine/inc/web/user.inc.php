<?php

global $_W, $_GPC;

switch ($_GPC['action']) {
    default:
        $argue_list = pdo_fetchall('SELECT * FROM ims_argue_routine_user WHERE activity_id=' . $_GPC['activity_id'] . ' AND viewpoint=1');
        $no_argue_list = pdo_fetchall('SELECT * FROM ims_argue_routine_user WHERE activity_id=' . $_GPC['activity_id'] . ' AND viewpoint=0');

        $user_list = [$argue_list, $no_argue_list];
        include_once $this->template('user/user_manager');
        break;
}
