<?php

require_once __DIR__."/api/Api.php";


/**
 * jz_movie模块小程序接口定义
 *
 * @author QQRPazWaNPgW
 * @url
 */
defined('IN_IA') or exit('Access Denied');

class Jz_movieModuleWxapp extends WeModuleWxapp
{
    public function __construct()
    {
        Api::instant();
    }
}