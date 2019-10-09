<?php
/**
 * gather_feedback模块微站定义
 *
 * @author QQRPazWaNPgW
 * @url
 */
require_once __DIR__.'/web/WebRoot.php';

defined('IN_IA') or exit('Access Denied');

class Gather_feedbackModuleSite extends WeModuleSite {

    public function __call($name, $arguments)
    {
        WebRoot::instance();
    }

    public function doWebQuestion_all(){

    }
}