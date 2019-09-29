<?php
//获取小程序基本信息
require_once __DIR__.'/Base.php';

class Essential extends Base{
    /**
     * 获取小程序基本信息，包括轮播图，文字，首页导航按钮图片路径等
     */
    public function get_message(){
        global $_GPC,$_W;

        $list = [];

        //轮播图
        $banner = pdo_get('zhls_sun_banner', ['uniacid' => $_W['uniacid']]);
        $banner['lb_imgs'] = explode(',', $banner['lb_imgs']);
        foreach ($banner['lb_imgs'] as $key=>$item){
            $banner['lb_imgs'][$key] = tomedia($item);
        }
        array_push($list,$banner);

        //首页自定义图标
        $shopData = pdo_get('zhls_sun_system', ['uniacid' => $_W['uniacid']]);
        $shopData['service_num'] = $shopData['service_num'] + 1;
    }
}