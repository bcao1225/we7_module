<?php

global $_GPC, $_W;

$red_packet_list = pdo_fetchall('SELECT * FROM ims_argue_routine_red_packet ORDER BY create_time DESC');

foreach ($red_packet_list as $key => $item) {
    $red_packet_list[$key]['user'] = pdo_get('ims_argue_routine_user', ['id' => $item['user_id']]);
}

include_once $this->template('red_packet_record');