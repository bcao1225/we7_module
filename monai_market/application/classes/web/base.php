<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/20
 * Time: 15:14
 */

class Web_Base extends Page
{
    public function __construct($_init = true)
    {
        if ($_init) {
            $this->init();
        }
    }

    private function init()
    {
        global $_W;
        if (($_W['role'] != 'manager') && ($_W['role'] != 'founder') ) {
            $perm = Permission::instance()->check_perm($_W['routes']);
            $perm_type = Permission::instance()->getLogTypes(true);
            $perm_type_value = array();

            foreach ($perm_type as $val) {
                $perm_type_value[] = $val['value'];
            }

            $is_xxx = Permission::instance()->check_xxx($_W['routes']);

            if ($is_xxx) {
                if (!$perm) {
                    /* foreach ($is_xxx as $item) {
                        if (in_array($item, $perm_type_value)) {
                            $this->tomessage('你没有相应的权限查看','');
                        }
                    } */
                    if (in_array($is_xxx, $perm_type_value)) {
                        $this->tomessage('你没有相应的权限查看','');
                    }
                }
            }
            else {
                if (strexists($_W['routes'], 'edit')) {
                    if (!cv($_W['routes'])) {
                        $view = str_replace('edit', 'view', $_W['routes']);
                        $perm_view = cv($view);
                    }
                }
                else {
                    $main = $_W['routes'] . '.main';
                    $perm_main = cv($main);
                    if (!$perm_main && in_array($main, $perm_type_value)) {
                        $this->tomessage('你没有相应的权限查看','');
                    }
                    else {
                        if (!$perm && in_array($_W['routes'], $perm_type_value)) {
                            $this->tomessage('你没有相应的权限查看','');
                        }
                    }
                }

                if (isset($perm_view) && !$perm_view) {
                    $this->tomessage('你没有相应的权限查看','');
                }
            }
        }


    }
}