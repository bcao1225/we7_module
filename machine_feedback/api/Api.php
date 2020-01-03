<?php
require_once __DIR__ . '/File.php';
require_once __DIR__ . '/Machine.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Feedback.php';

defined('IN_IA') or exit('Access Denied');

class Api extends WeModuleWxapp
{
    public static $account_api;
    public $appid = 'wxf351d059a8f5b8b2';
    public $app_secret = '54f36b52ba0aced0153d30faad85dc33';

    public static function instant()
    {
        global $_GPC;

        $clazz_name = ucfirst($_GPC['clazz']);
        $clazz = new $clazz_name;
        call_user_func([$clazz, $_GPC['do']]);
    }
}