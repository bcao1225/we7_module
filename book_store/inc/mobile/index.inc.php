<?php

global $_GPC, $_W;

switch ($_GPC['action']) {
    default:
        include_once $this->template('index');
}