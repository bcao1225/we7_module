<?php
/**
 * machine_feedback模块小程序接口定义
 *
 * @author QQRPazWaNPgW
 * @url
 */
require_once __DIR__."/api/Api.php";

defined('IN_IA') or exit('Access Denied');

class Machine_feedbackModuleWxapp extends WeModuleWxapp
{
    public function __destruct()
    {
        Api::instant();
    }
}