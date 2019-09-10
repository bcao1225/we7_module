<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/20
 * Time: 11:24
 */

class Core {

    const PRODUCTION  = 10;
    const STAGING     = 20;
    const TESTING     = 30;
    const DEVELOPMENT = 40;
    /**
     * @var  boolean  模块验证
     */
    public static $safe_mode = FALSE;
    /**
     * @var  array   域名验证
     */
    public static $hostnames = array();

    /**
     * @var  string  根目录
     */
    public static $base_url = '/';

    /**
     * @var  string  默认文件
     */
    public static $index_file = 'index.php';
    /**
     * @var  Config  配置文件
     */
    public static $config;
    /**
     * @var  boolean  初始化
     */
    protected static $_init = FALSE;

    /**
     * @var  array   模块
     */
    protected static $_modules = array();
    /**
     * @var  array   默认路径
     */
    protected static $_paths = array(SYSPATH,APPLIPATH);
    /**
     * @var  array   文件路径
     */
    protected static $_files = array();

    /**
     * 框架初始化
     */
    public static function init(){
        global $_GPC,$_W;
        // 插件初始化
        if($_GPC['plugin']){
            self::$_paths = array_merge([],self::$_paths);
        }
    }




    public static function auto_load($class, $directory = 'classes')
    {
        // Transform the class name according to PSR-0
        $class     = ltrim($class, '\\');
        $file      = '';
        $namespace = '';

        if ($last_namespace_position = strripos($class, '\\'))
        {
            $namespace = substr($class, 0, $last_namespace_position);
            $class     = substr($class, $last_namespace_position + 1);
            $file      = str_replace('\\', DIRECTORY_SEPARATOR, $namespace).DIRECTORY_SEPARATOR;
        }

        $file .= str_replace('_', DIRECTORY_SEPARATOR, $class);
        $file = strtolower($file);
        //echo $file;exit;

        if ($path = Core::find_file($directory, $file))
        {
            // Load the class file
            require $path;

            // Class has been found
            return TRUE;
        }

        // Class is not in the filesystem
        return FALSE;
    }

    /**
     * 查找文件
     * @param $dir
     * @param $file
     * @param null $ext
     * @param bool $array
     * @return array|bool|string
     */
    public static function find_file($dir, $file, $ext = NULL, $array = FALSE)
    {
        if ($ext === NULL)
        {
            $ext = '.php';
        }
        elseif ($ext)
        {
            $ext = ".{$ext}";
        }
        else
        {
            $ext = '';
        }

        $path = $dir.DIRECTORY_SEPARATOR.$file.$ext;
        if ($array OR $dir === 'config') {
            $paths = array_reverse(Core::$_paths);
            $found = array();
            foreach ($paths as $dir)
            {
                if (is_file($dir.$path))
                {
                    $found[] = $dir.$path;
                }
            }
        } else {
            $found = FALSE;

            foreach (Core::$_paths as $dir)
            {
                if (is_file($dir.$path))
                {
                    $found = $dir.$path;
                    break;
                }
            }
        }
        return $found;
    }

    /**
     * 初始化模块（后期添加）
     * @param array|NULL $modules
     * @return array
     */
    public static function modules(array $modules = NULL)
    {
        if ($modules === NULL) {
            return Core::$_modules;
        }
        $paths = array(APPPATH,SYSPATH,APPLIPATH);

        foreach ($modules as $name => $path) {
            if (is_dir($path)) {
                $paths[] = $modules[$name] = realpath($path) . DIRECTORY_SEPARATOR;
            }

        }
        $paths[] = SYSPATH;

        Core::$_paths = $paths;

        Core::$_modules = $modules;

        foreach (Core::$_modules as $path)
        {
            $init = $path.'init.php';
            if (is_file($init))
            {
                require_once $init;
            }
        }
        return Core::$_modules;

    }
}