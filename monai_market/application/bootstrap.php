<?php
if(!defined('IN_IA')) {
    exit('Access Denied');
}

/**
 * 应用绝对路径
 */
define('MODEL_PATH',IA_ROOT.'/addons/'.MODEL_NAME.'/');

/**
 * 应用url
 */
define('MODEL_URL',$_W['siteroot'].'addons/'.MODEL_NAME.'/');

/**
 * 应用路径
 */
define('MODEL_LOCAL','../addons/'.MODEL_NAME.'/');

/**
 * 静态文件
 */
define('MODEL_STATIC', MODEL_URL.'static/');

/**
 * 微信公众平台
 */
define('MODEL_AUTH_URL','https://mp.weixin.qq.com/');

/**
 * 系统路径
 */
define('SYSPATH',MODEL_PATH.'system/');

/**
 * 应用路径
 */
define('APPLIPATH',MODEL_PATH.'application/');

/**
 * 兼容微擎方法
 */
require_once MODEL_PATH . 'system/functions.php';

/**
 * 核心代码
 */
require SYSPATH . '/classes/core.php';

/**
 * 类的自动加载
 */
spl_autoload_register(array('Core', 'auto_load'));

/**
 * 开启php.ini 的自动加载配置
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

/**
 * 框架初始化
 */
Core::init(array(

));

/**
 * 模块初始化

Core::modules(array(
    // 'auth'       => MODPATH.'auth',       // Basic authentication
    // 'cache'      => MODPATH.'cache',      // Caching with multiple backends
    // 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
    // 'database'   => MODPATH.'database',   // Database access
));
 */