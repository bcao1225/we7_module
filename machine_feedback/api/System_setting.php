<?php
require_once __DIR__ . '/Api.php';

class System_setting extends Api
{
    public function get_system_setting()
    {
        $system_setting = pdo_getall('ims_machine_feedback_system_setting')[0];
        $this->result(0, '获取成功', $system_setting);
    }
}