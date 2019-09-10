<?php

/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2018/4/20

 * Time: 14:30

 */



class Application

{

    /**

     * site web 入口

     */

    public static function run(){

        global $_GPC,$_W;

        $r = str_replace('//', '/', trim($_GPC['r'], '/'));

        $routes = explode('.', $r);

        $segs = count($routes);

        $method = 'index';

        $root = APPLIPATH.'classes/web/';

        switch ($segs)

        {

            case 1: $file = $root . $routes[0] . '.php';

                if (is_file($file))

                {

                    $class = ucfirst($routes[0]);

                }

                else if (is_dir($root . $routes[0]))

                {

                    $file = $root . $routes[0] . '/index.php';

                    $class = 'Index';

                }

                else

                {

                    $method = $routes[0];

                    $file = $root . 'index.php';

                    $class = 'Index';

                }

                $_W['action'] = $routes[0];

                break;

            case 2: $_W['action'] = $routes[0] . '.' . $routes[1];

                $file = $root . $routes[0] . '/' . $routes[1] . '.php';

                if (is_file($file))

                {

                    $class = ucfirst($routes[0]).'_'.ucfirst($routes[1]);

                }

                else if (is_dir($root . $routes[0] . '/' . $routes[1]))

                {

                    $class = ucfirst($routes[0]).'_'.ucfirst($routes[1]).'_Index';

                }

                else

                {

                    $file = $root . $routes[0] . '.php';

                    if (is_file($file))

                    {

                        $method = $routes[1];

                        $class = ucfirst($routes[0]);

                    }

                    else if (is_dir($root . $routes[0]))

                    {

                        $class = ucfirst($routes[0]).'_'.ucfirst($routes[1]);

                    }

                    else

                    {

                        $class = 'Index';

                    }

                }

                $_W['action'] = $routes[0] . '.' . $routes[1];

                break;

            case 3: $_W['action'] = $routes[0] . '.' . $routes[1] . '.' . $routes[2];

                $file = $root . $routes[0] . '/' . $routes[1] . '/' . $routes[2] . '.php';

                if (is_file($file))

                {

                    $class = ucfirst($routes[0]).'_'.ucfirst($routes[1]).'_'.ucfirst($routes[2]);

                } else if (is_dir($root . $routes[0] . '/' . $routes[1] . '/' . $routes[2]))

                {

                    $class = ucfirst($routes[0]).'_'.ucfirst($routes[1]).'_'.ucfirst($routes[2]).'_Index';

                } else {

                    $file = $root . $routes[0] . '/' . $routes[1] . '.php';

                    if (is_file($file))

                    {

                        $class = ucfirst($routes[0]).'_'.ucfirst($routes[1]);

                        $method = $routes[2];

                    }

                    else if (is_dir($root . $routes[0] . '/' . $routes[1]))

                    {

                        $class = ucfirst($routes[0]).'_'.ucfirst($routes[1]).'_Index';

                        $method = 'index';

                    }

                }

                break;

        }



        $_W['routes'] = $r;

        $_W['controller'] = $routes[0];

        $class = 'Web_'.$class;

        if(!class_exists($class)){

            show_message('控制器 ' . $class . '未找到!');

        }

        $instance = new $class();

        if (!(method_exists($instance, $method)))

        {

            show_message('控制器 ' . $_W['controller'] . ' 方法 ' . $method . ' 未找到!');

        }

        $instance->$method();

        exit();

    }



    /**

     * wxapp api 入口

     */

    public static function api(){

        global $_GPC,$_W;

        $r = str_replace('//', '/', trim($_GPC['r'], '/'));

        $routes = explode('.', $r);

        $segs = count($routes);

        $method = 'index';

        $root = APPLIPATH.'classes/api/';

        switch ($segs)

        {

            case 1: $file = $root . $routes[0] . '.php';

                if (is_file($file))

                {

                    $class = ucfirst($routes[0]);

                }

                else if (is_dir($root . $routes[0]))

                {

                    $file = $root . $routes[0] . '/index.php';

                    $class = 'Index';

                }

                else

                {

                    $method = $routes[0];

                    $file = $root . 'index.php';

                    $class = 'Index';

                }

                $_W['action'] = $routes[0];

                break;

            case 2: $_W['action'] = $routes[0] . '.' . $routes[1];

                $file = $root . $routes[0] . '/' . $routes[1] . '.php';



                if (is_file($file))

                {

                    $class = ucfirst($routes[0]).'_'.ucfirst($routes[1]);

                }

                else if (is_dir($root . $routes[0] . '/' . $routes[1]))

                {

                    $class = ucfirst($routes[0]).'_'.ucfirst($routes[1]).'_Index';

                }

                else

                {

                    $file = $root . $routes[0] . '.php';

                    if (is_file($file))

                    {

                        $method = $routes[1];

                        $class = ucfirst($routes[0]);

                    }

                    else if (is_dir($root . $routes[0]))

                    {

                        $class = ucfirst($routes[0]).'_'.ucfirst($routes[1]);

                    }

                    else

                    {

                        $class = 'Index';

                    }

                }

                $_W['action'] = $routes[0] . '.' . $routes[1];

                break;

            case 3: $_W['action'] = $routes[0] . '.' . $routes[1] . '.' . $routes[2];





                $file = $root . $routes[0] . '/' . $routes[1] . '/' . $routes[2] . '.php';



                if (is_file($file))

                {

                    $class = ucfirst($routes[0]).'_'.ucfirst($routes[1]).'_'.ucfirst($routes[2]);

                } else if (is_dir($root . $routes[0] . '/' . $routes[1] . '/' . $routes[2]))

                {

                    $class = ucfirst($routes[0]).'_'.ucfirst($routes[1]).'_'.ucfirst($routes[2]).'_Index';

                } else {

                    $file = $root . $routes[0] . '/' . $routes[1] . '.php';

                    if (is_file($file))

                    {

                        $class = ucfirst($routes[0]).'_'.ucfirst($routes[1]);

                        $method = $routes[2];

                    }

                    else if (is_dir($root . $routes[0] . '/' . $routes[1]))

                    {

                        $class = ucfirst($routes[0]).'_'.ucfirst($routes[1]).'_Index';

                        $method = 'index';

                    }

                }

                break;

        }



        $_W['routes'] = $r;

        $_W['controller'] = $routes[0];

        $class = 'Api_'.$class;



        if(!class_exists($class)){

            echo json_encode(['code'=>'400','message' => '控制器 ' . $_W['controller'] .' 未找到!','data'=>'']);

            exit;

        }

        $instance = new $class();

        if (!(method_exists($instance, $method)))

        {

            echo json_encode(['code'=>'400','message' => '控制器 ' . $_W['controller'] . ' 方法 ' . $method . ' 未找到!','data'=>'']);

            exit;

        }

        $instance->$method();

        exit();

    }

}