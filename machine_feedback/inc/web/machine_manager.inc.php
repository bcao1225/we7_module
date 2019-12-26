<?php

global $_W,$_GPC;

switch ($_GPC['action']){
    /*添加机器*/
    case 'add':
        include_once $this->template('machine/add_machine');
        break;
    default:
        include_once $this->template('machine_manager');
        break;
}
