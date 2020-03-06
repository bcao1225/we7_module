<?php

global $_GPC, $_W;

switch ($_GPC['action']) {
    case 'get_all':
        $this->result(0, '获取成功', pdo_getall('ims_book_store_type'));
        break;

}
