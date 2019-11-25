<?php

global $_GPC,$_W;

switch ($_GPC['action']){
    case '':
        break;
    default:
        include_once $this->template('user/user_manager');
        break;
}
