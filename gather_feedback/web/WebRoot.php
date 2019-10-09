<?php

require_once __DIR__.'/Question.php';

class WebRoot extends WeModuleSite
{
    public static function instance()
    {
        global $_GPC;
        $clazz_name = ucfirst($_GPC['state']);
        $clazz = new $clazz_name;
        call_user_func([$clazz,$_GPC['do']]);
    }
}