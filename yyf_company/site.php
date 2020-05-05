<?php


/**
 * 精美企业公司官网小程序模块微站定义
 *
 * @author 易福源码网 www.efwww.com
 * @url
 */

defined('IN_IA') or exit('Access Denied');


class Yyf_companyModuleSite extends WeModuleSite
{
    public function doWebGetWebDakuan(){
        $oureder_list = pdo_getall('ims_z_order');
        foreach ($oureder_list as $index=>$item){
            $user = pdo_get('ims_z_user',['id'=>$item['user_id']]);
            $oureder_list[$index]['name'] =$user['name'];
            $oureder_list[$index]['tel'] = $user['tle'];
            $oureder_list[$index]['grade'] = $user['grade'];
            $oureder_list[$index]['professional'] = $user['professional'];
            $oureder_list[$index]['unit'] = $user['unit'];
            $oureder_list[$index]['Identity'] = $user['Identity'];
            $oureder_list[$index]['carttime'] = date('Y-m-s',$item['carttime']);
        }

        exit(json_encode($oureder_list));
    }

    public function doWebTabBar()
    {
        require_once dirname(__FILE__) . '/lib/lib_tabbar.php';
    }

    public function doMobileDitu()
    {

        global $_GPC, $_W;
        // '[{"icon":"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1573143110723&di=4dfbb76fcc7c8ac45cb5bc90bd7c7103&imgtype=0&src=http%3A%2F%2Fimg3.duitang.com%2Fuploads%2Fitem%2F201408%2F24%2F20140824154439_3KYMH.png","position":{"P":"35.025","Q":"118.40768","lat":"35.025","lng":"118.40768"},"extData":"966"},{"icon":"https://ss1.bdstatic.com/70cFvXSh_Q1YnxGkpoWK1HF6hhy/it/u=1160398029,1105229134&fm=26&gp=0.jpg","position":{"P":"35.0260","Q":"118.40000","lat":"35.0260","lng":"118.40000"},"extData":"967"},{"icon":"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1573143110723&di=4dfbb76fcc7c8ac45cb5bc90bd7c7103&imgtype=0&src=http%3A%2F%2Fimg3.duitang.com%2Fuploads%2Fitem%2F201408%2F24%2F20140824154439_3KYMH.png","position":{"P":"35.027","Q":"118.41000","lat":"35.027","lng":"118.41000"},"extData":"968"},{"icon":"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1573192245967&di=d53f472e98d8ffe22888f3cbf5ababa3&imgtype=0&src=http%3A%2F%2Fimg3.duitang.com%2Fuploads%2Fitem%2F201607%2F31%2F20160731225634_s4kzH.thumb.700_0.png","position":{"P":"35.023061","Q":"118.407798","lat":"35.023061","lng":"118.407798"},"extData":"000"}]'
        $user_arr = pdo_fetchall("select * from ims_z_user where status=0 and poster is not null and lng > 1  and lat > 1");
        //var_dump($user_arr);
        $user_arr0 = array();
        //var_dump($user_arr);die;
        foreach ($user_arr as $key => $value) {
            $user_arr0[$key]['icon'] = $user_arr[$key]['photo'];
            //$user_arr0[$key]['position']['P'] = $user_arr[$key]['lat'];
            //$user_arr0[$key]['position']['Q'] = $user_arr[$key]['lng'];
            //$user_arr0[$key]['position']['lat'] = $user_arr[$key]['lat'];
            //$user_arr0[$key]['position']['lng'] = $user_arr[$key]['lng'];
            $user_arr0[$key]['position'] = [$user_arr[$key]['lng'], $user_arr[$key]['lat']];
            $user_arr0[$key]['extData'] = $user_arr[$key]['id'];
        }

        $user_arr = json_encode($user_arr0);


        $user_arr1 = pdo_fetchall("select * from ims_z_user where status=0  and poster is not null and lng > 1  and lat > 1");

        $user_arr0_1 = array();
        foreach ($user_arr1 as $key => $value) {
            if ($user_arr1[$key]['openid'] == $_GPC['openid']) {
                $user_arr0_1[$key]['icon'] = $_W['siteroot'] . "/chuanqi/girl.png";
            } else {
                $user_arr0_1[$key]['icon'] = $_W['siteroot'] . "/chuanqi/boy.png";
            }
            //$user_arr0_1[$key]['position']['P'] = $user_arr1[$key]['lat'];
            //$user_arr0_1[$key]['position']['Q'] = $user_arr1[$key]['lng'];
            //$user_arr0_1[$key]['position']['lat'] = $user_arr1[$key]['lat'];
            //$user_arr0_1[$key]['position']['lng'] = $user_arr1[$key]['lng'];

            if (isset($lng) && isset($lat)) {

                $radLat1 = deg2rad($lat); //deg2rad()函数将角度转换为弧度
                $radLat2 = deg2rad($value['lat']);
                $radLng1 = deg2rad($lng);
                $radLng2 = deg2rad($value['lng']);
                $a = $radLat1 - $radLat2;
                $b = $radLng1 - $radLng2;
                $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137;
                $sum += $s;
                $lng = $value['lng'];
                $lat = $value['lat'];
                if ($key == 0) {
                    $sum = 0;
                }
            } else {

                $lng = $value['lng'];
                $lat = $value['lat'];
                $sum = 0;
            }
            $user_arr1[$key]['juli'] = $s;
            $user_arr0[$key]['position'] = [$user_arr[$key]['lng'], $user_arr[$key]['lat']];
            $user_arr0_1[$key]['extData'] = $user_arr1[$key]['id'];

        }
        //$sum = pdo_fetch("select sum(sum) as sum from ims_z_user where 1");
        //$sum = $sum['sum'];
        $sum = number_format($sum, 2);
        $count = pdo_fetch("select sum(sum) as sum from ims_z_user where 1");
        // var_dump($count);die;
        // var_dump($user_arr0_1);
        // var_dump($_GPC['openid']);
        $user_arr_1 = json_encode($user_arr0_1);

        $user_info = pdo_fetch("select * from ims_z_user ", array(":openid" => $_GPC['openid']));

        //var_dump( $user_info);die;
        include $this->template('ditu');


    }

    public function doMobileCaijian($url)
    {
        load()->func('file');
        //图片裁剪
        $src_path = 'https://xcx.bg580.cn/attachment/hys/1167330347577085.jpg';
        $ext = pathinfo($src_path, PATHINFO_EXTENSION);
        $rand_name = time() . "11." . $ext;
        $rand_name1 = time() . "." . $ext;
        file_image_thumb($src_path, IA_ROOT . '/attachment/caijian/' . $rand_name1, 300);
        file_image_crop(IA_ROOT . '/attachment/caijian/' . $rand_name1, IA_ROOT . '/attachment/caijian/' . $rand_name, 343, 195);
        return 'https://' . $_SERVER['HTTP_HOST'] . '/attachment/caijian/' . $rand_name;

    }

    public function doMobileCeshi()
    {
        $img_info = getimagesize('https://xcx.bg580.cn/attachment/hys/5769716817707318.png');
        print_r($img_info);
    }

    public function doMobileUser()
    {

        global $_GPC, $_W;
        $id = $_GPC['id'];

        $user_info = pdo_fetchall("select * from ims_z_user where id=:id", array(":id" => $id));

        foreach ($user_info as $key => $value) {
            $poster = pdo_get('ims_z_poster', array('user_id' => $value['id']));
            $user_info[$key]['max'] = $poster['id'];
            $user_info[$key]['tle'] = substr($user_info[$key]['tle'], 0, 3) . '****' . substr($user_info[$key]['tle'], 7);
            $user_infotoo = pdo_fetch("select * from ims_z_user where status=0 and lng > 1 and lat > 1 and id < " . $id . " order by id desc");
            //var_dump( $user_infotoo);die;
            // var_dump($value['lng']);die;
            $radLat1 = deg2rad($user_infotoo['lat']); //deg2rad()函数将角度转换为弧度
            $radLat2 = deg2rad($value['lat']);
            $radLng1 = deg2rad($user_infotoo['lng']);
            $radLng2 = deg2rad($value['lng']);
            $a = $radLat1 - $radLat2;
            $b = $radLng1 - $radLng2;
            $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137;

            if (empty($user_infotoo)) {
                $s = 0;
            }
            $user_info[$key]['juli'] = number_format($s, 2);


        }
        return json_encode($user_info);
    }

    public function doWebAdsense()
    {


        require_once dirname(__FILE__) . '/lib/lib_adsense.php';


    }

    public function doWebDiy()
    {


        include $this->template('diy');


    }

    public function doWebNavExplain()
    {


        global $_W, $_GPC;


        $url = MODULE_URL . 'images/';


        include $this->template('navexplain');


    }

    public function doWebTitleExplain()
    {


        global $_W, $_GPC;


        $url = MODULE_URL . 'images/';


        include $this->template('titleexplain');


    }

    public function doWebSysinfo()
    {


        global $_W, $_GPC;


        $uniacid = $_W['uniacid'];


        load()->func('tpl');


        $result = pdo_fetch("SELECT * FROM " . tablename('yyf_company_sysinfo') . " where `uniacid`='$uniacid' order by id desc limit 1");


        $lng = $result['jing'];


        $lat = $result['wei'];


        $position = array('lng' => $lng, 'lat' => $lat);


        if (checksubmit()) {


            $data = array();


            $data['notice'] = $_GPC['notice'];


            $data['phone'] = $_GPC['phone'];


            $position = $_GPC['position'];


            $arr = explode(',', $position);


            $data['jing'] = $arr[0];


            $data['wei'] = $arr[1];


            $data['email'] = $_GPC['email'];


            $data['qq'] = $_GPC['qq'];


            $data['address'] = $_GPC['address'];


            $data['copyright'] = $_GPC['copyright'];


            $data['uniacid'] = $_W['uniacid'];


            $data['name'] = $_GPC['name'];


            $data['title'] = $_GPC['title'];


            $data['message_email'] = $_GPC['message_email'];


            $data['message_title'] = $_GPC['message_title'];


            $data['smtp_email'] = $_GPC['smtp_email'];


            $data['smtp_key'] = $_GPC['smtp_key'];


            $result = pdo_fetch("SELECT `id` FROM " . tablename('yyf_company_sysinfo') . " where `uniacid`='$uniacid' order by id desc limit 1");


            if ($result['id']) {


                $res = pdo_update('yyf_company_sysinfo', $data, array('id' => $result['id']));


            } else {


                $res = pdo_insert('yyf_company_sysinfo', $data);


            }


            message('修改成功');


        }


        include $this->template('sysinfo');


    }

    public function doWebIndexStyle()
    {


        global $_W, $_GPC;


        $uniacid = $_W['uniacid'];


        load()->func('tpl');


        $item = pdo_fetch("SELECT * FROM " . tablename('yyf_company_style') . " where `uniacid`='$uniacid' order by id desc limit 1");


        $appimg = MODULE_URL . '/images/app.jpg';


        $contactimg = MODULE_URL . '/images/contact.jpg';


        if (checksubmit()) {


            $_GPC['slide_close'] == 'on' ? $data['slide_close'] = 1 : $data['slide_close'] = 0;


            $_GPC['nav_close'] == 'on' ? $data['nav_close'] = 1 : $data['nav_close'] = 0;


            $_GPC['notice_close'] == 'on' ? $data['notice_close'] = 1 : $data['notice_close'] = 0;


            $_GPC['custom_close'] == 'on' ? $data['custom_close'] = 1 : $data['custom_close'] = 0;


            $data['contact_logo'] = $_GPC['contact_logo'];


            $data['tcolor'] = $_GPC['tcolor'];


            $data['horn'] = $_GPC['horn'];


            $data['contact_name'] = $_GPC['contact_name'];


            $data['contact_background'] = $_GPC['contact_background'];


            $data['uniacid'] = $uniacid;


            $data['slide_height'] = $_GPC['slide_height'];


            $data['nav_style'] = $_GPC['nav_style'];


            $data['title_style'] = $_GPC['title_style'];


            if ($item['id']) {


                $res = pdo_update('yyf_company_style', $data, array('id' => $item['id']));


            } else {


                $res = pdo_insert('yyf_company_style', $data);


            }


            message('修改成功');


        }


        include $this->template('indexstyle');


    }

    public function doWebSmtp()
    {


        global $_W, $_GPC;


        $img1 = MODULE_URL . '/images/smtp1.jpg';


        $img2 = MODULE_URL . '/images/smtp2.jpg';


        $error = "";


        if (!extension_loaded('openssl')) {


            $error = "系统检测到您当前的服务器并没有开启openssl扩展，无法发送邮件！登录自己的服务器，找到php.ini文件，检查php.ini中;extension=php_openssl.dll是否存在， 如存在删除掉前面的注释符';'然后保存文件，重启apache或者服务器）";


        }


        include $this->template('smtp');


    }

    public function doWebCateInfo()
    {


        global $_W, $_GPC;


        $img = MODULE_URL . '/images/';


        include $this->template('cateinfo');


    }

    public function doWebMember()
    {

        global $_W, $_GPC;
        if (!empty($_POST['name']) && empty($_POST['tel'])) {
            $where0 = $_POST['name'];
            $list = pdo_fetchall("select  * from `ims_z_user` where `name` like '%{$where0}%'  order by id desc");
        } else if (empty($_POST['name']) && !empty($_POST['tel'])) {
            $where1 = $_POST['tel'];
            $list = pdo_fetchall("select  * from `ims_z_user` where `tle` like '%{$where1}%' order by id desc");
        } else if (!empty($_POST['name']) && !empty($_POST['tel'])) {
            $where0 = $_POST['name'];
            $where1 = $_POST['tel'];
            $list = pdo_fetchall("select  * from `ims_z_user` where `tle` like '%{$where1}%' and `name` like '%{$where0}%'  order by id desc");
        } else if (empty($_POST['name']) && empty($_POST['tel'])) {
            $uniacid = $_W['uniacid'];
            $participators = pdo_fetchall("SELECT `id` FROM " . tablename('z_user') . " where 1 ORDER BY `id` DESC");
            $total = count($participators);
            if (!isset($_GPC['page'])) {
                $pageindex = 1;
            } else {
                $pageindex = intval($_GPC['page']);
            }
            $pagesize = 15;
            $pager = pagination($total, $pageindex, $pagesize);
            $p = ($pageindex - 1) * 15;
            $list = pdo_fetchall("select  * from `ims_z_user` where 1 order by id desc limit " . $p . "," . $pagesize);
        }


        include $this->template('member');
    }

    public function doWebConfig()
    {

        global $_W, $_GPC;
        if ($_POST) {
            $data['text'] = $_POST['a1'];
            pdo_update('z_config', $data, array('id' => 1));
            $data['text'] = $_POST['a2'];
            pdo_update('z_config', $data, array('id' => 2));
            $data['text'] = $_POST['a3'];
            pdo_update('z_config', $data, array('id' => 3));
            $data['text'] = $_POST['a4'];
            pdo_update('z_config', $data, array('id' => 4));
            $data['text'] = $_POST['a5'];
            pdo_update('z_config', $data, array('id' => 5));
        }
        $a1 = pdo_get('z_config', array('id' => 1));
        $a2 = pdo_get('z_config', array('id' => 2));
        $a3 = pdo_get('z_config', array('id' => 3));
        $a4 = pdo_get('z_config', array('id' => 4));
        $a5 = pdo_get('z_config', array('id' => 5));
        include $this->template('config');
    }

    public function doWebAddnews()
    {


        global $_W, $_GPC;


        load()->func('tpl');


        $uniacid = $_W['uniacid'];


        $list = pdo_fetchall("select `id`,`name`,`type` from " . tablename('yyf_company_category') . " where `uniacid`='$uniacid' and `type`<>'3' and `fid`='0' order by sortrank desc");


        foreach ($list as $key => $value) {


            $fid = $value['id'];


            $sonArr = pdo_fetchall("select `id`,`name`,`type` from " . tablename('yyf_company_category') . " where `fid`=$fid order by sortrank desc");


            $list[$key]['son'] = $sonArr;


        }


        if (checksubmit()) {


            $data = array();


            $data['title'] = $_GPC['title'];


            $data['cid'] = $_GPC['cid'];


            $data['thumb'] = $_GPC['thumb'];


            $data['addtime'] = strtotime($_GPC['addtime']);


            $data['content'] = $_GPC['content'];


            $data['uniacid'] = $_W['uniacid'];


            $data['sortrank'] = $_GPC['sortrank'];


            $data['videosrc'] = $_GPC['videosrc'];


            $data['appid'] = $_GPC['appid'];


            $data['pageaddress'] = $_GPC['pageaddress'];


            $res = pdo_insert('yyf_company_news', $data);


            message('添加成功', $this->createWebUrl('News'));


        }


        include $this->template('addnews');


    }

    public function doWebNews()
    {


        global $_W, $_GPC;


        $uniacid = $_W['uniacid'];


        $newObj = tablename('yyf_company_news');


        $catObj = tablename('yyf_company_category');


        $participators = pdo_fetchall("SELECT `id` FROM " . tablename('yyf_company_news') . " where `uniacid`='$uniacid' ORDER BY `id` DESC");


        $total = count($participators);


        if (!isset($_GPC['page'])) {
            $pageindex = 1;
        } else {
            $pageindex = intval($_GPC['page']);
        }


        $pagesize = 15;


        $pager = pagination($total, $pageindex, $pagesize);


        $p = ($pageindex - 1) * 15;


        $list = pdo_fetchall("select $newObj.*,$catObj.name from $newObj left join $catObj on $newObj.cid=$catObj.id where $newObj.`uniacid`='$uniacid' order by id desc limit " . $p . "," . $pagesize);


        foreach ($list as $key => $value) {


            $list[$key]['addtime'] = date('Y-m-d', $list[$key]['addtime']);


        }


        include $this->template('news');


    }

    public function doWebEditNews($id = 0)
    {


        global $_W, $_GPC;


        load()->func('tpl');


        $uniacid = $_W['uniacid'];


        $list = pdo_fetchall("select `id`,`name`,`type` from " . tablename('yyf_company_category') . " where `uniacid`='$uniacid' and `type`<>'3' and `fid`='0' order by sortrank desc");


        foreach ($list as $key => $value) {


            $fid = $value['id'];


            $sonArr = pdo_fetchall("select `id`,`name`,`type` from " . tablename('yyf_company_category') . " where `fid`=$fid order by sortrank desc");


            $list[$key]['son'] = $sonArr;


        }


        if ($_GPC['id'] && !checksubmit()) {


            $id = $_GPC['id'];


            $data = pdo_get('yyf_company_news', array('id' => $id, 'uniacid' => $uniacid));


            $data['addtime'] = date('Y-m-d', $data['addtime']);


        }


        if (checksubmit()) {


            $data = array();


            $data['title'] = $_GPC['title'];


            $data['cid'] = $_GPC['cid'];


            $data['thumb'] = $_GPC['thumb'];


            $data['addtime'] = strtotime($_GPC['addtime']);


            $data['content'] = $_GPC['content'];


            $data['sortrank'] = $_GPC['sortrank'];


            $data['videosrc'] = $_GPC['videosrc'];


            $data['appid'] = $_GPC['appid'];


            $data['pageaddress'] = $_GPC['pageaddress'];


            $id = $_GPC['aid'];


            $res = pdo_update('yyf_company_news', $data, array('id' => $id));


            message('修改成功', $this->createWebUrl('News'));


        }


        include $this->template('editnews');


    }

    public function doWebDelete($id = 0)
    {


        global $_W, $_GPC;


        $uniacid = $_W['uniacid'];


        if ($_GPC['id']) {


            $id = $_GPC['id'];


            $data = pdo_delete('yyf_company_news', array('id' => $id, 'uniacid' => $uniacid));


            message('删除成功', $this->createWebUrl('News'));


        }


    }

    public function doWebBgimg()
    {

        global $_W, $_GPC;

        $list = pdo_fetchall("select * from " . tablename('z_bgimg') . " where 1 order by id desc");
        foreach ($list as $key => $item) {
            $query = pdo_fetchall("select * from " . tablename('z_citytype') . " where id = " . $item['type_id']);
            $list[$key]['typename'] = $query[0]['name'];
        }
        include $this->template('bgimg');
    }

    public function doWebBgimgAdd()
    {

        global $_W, $_GPC;

        if (checksubmit()) {

            $data['status'] = 0;
            $data['src'] = $_GPC['thumb'];
            $data['src2'] = $_GPC['src2'];
            $data['src3'] = $_GPC['src3'];
            $data['type_id'] = $_GPC['bgtype'];

            $res = pdo_insert('z_bgimg', $data);

            message('添加成功', $this->createWebUrl('bgimg'));

        }
        $result = pdo_fetchall("select * from " . tablename('z_citytype') . " where 1 order by `id` desc");

        include $this->template('bgimgadd');

    }

    public function doWebBgimgEdit()
    {

        global $_W, $_GPC;

        $id = $_GPC['id'];
        $uniacid = $_W['uniacid'];
        $data = pdo_get('z_bgimg', array('id' => $id));
        $query = pdo_get('z_citytype', array('id' => $data['type_id']));
        $result = pdo_fetchall("select * from " . tablename('z_citytype') . " where id != {$data['type_id']} order by `id` desc");
        if (checksubmit()) {
            $data = array();
            $data['src'] = $_GPC['thumb'];
            $data['src2'] = $_GPC['src2'];
            $data['src3'] = $_GPC['src3'];
            $data['type_id'] = $_GPC['bgtype'];
            $res = pdo_update('z_bgimg', $data, array('id' => $id));
            message('修改成功', $this->createWebUrl('bgimg'));
        }
        include $this->template('bgimgedit');
    }

    public function doWebBgimgDel($id = 0)
    {

        global $_W, $_GPC;

        $uniacid = $_W['uniacid'];
        if ($_GPC['id']) {
            $id = $_GPC['id'];
            $data = pdo_delete('z_bgimg', array('id' => $id));
            message('删除成功', $this->createWebUrl('bgimg'));
        }
    }

    public function doWebSlide()
    {


        global $_W, $_GPC;


        $uniacid = $_W['uniacid'];


        if (checksubmit()) {


            foreach ($_GPC['sortrank'] as $key => $value) {


                $data['sortrank'] = $value;


                $id = $_GPC['id'][$key];


                $res = pdo_update('yyf_company_slide', $data, array('id' => $id, 'uniacid' => $uniacid));


            }


            message('排序成功', $this->createWebUrl('slide'));


        }


        $list = pdo_fetchall("select `id`,`images`,`sortrank` from " . tablename('yyf_company_slide') . " where `uniacid`='$uniacid' order by sortrank desc");


        //var_dump($list);


        include $this->template('slide');


    }

    public function doWebEditSlide()
    {


        global $_W, $_GPC;


        load()->func('tpl');


        $id = $_GPC['id'];


        $uniacid = $_W['uniacid'];


        $data = pdo_get('yyf_company_slide', array('id' => $id, 'uniacid' => $uniacid));


        if (checksubmit()) {


            $data = array();


            $data['id'] = $_GPC['id'];


            $data['images'] = $_GPC['thumb'];


            $data['sortrank'] = $_GPC['sortrank'];


            $data['aid'] = $_GPC['aid'];


            $res = pdo_update('yyf_company_slide', $data, array('id' => $id));


            message('修改成功', $this->createWebUrl('slide'));


        }


        include $this->template('editslide');


    }

    public function doWebAddSlide()
    {


        global $_W, $_GPC;


        $uniacid = $_W['uniacid'];


        load()->func('tpl');


        if (checksubmit()) {


            $data = array();


            $data['sortrank'] = $_GPC['sortrank'];


            $data['images'] = $_GPC['thumb'];


            $data['uniacid'] = $uniacid;


            $data['aid'] = $_GPC['aid'];


            $res = pdo_insert('yyf_company_slide', $data);


            message('添加成功', $this->createWebUrl('Slide'));


        }


        include $this->template('addslide');


    }

    public function doWebDeleteSlide($id = 0)
    {


        global $_W, $_GPC;


        $uniacid = $_W['uniacid'];


        if ($_GPC['id']) {


            $id = $_GPC['id'];


            $data = pdo_delete('yyf_company_slide', array('id' => $id, 'uniacid' => $uniacid));


            message('删除成功', $this->createWebUrl('Slide'));


        }


    }

    public function doWebAddCat()
    {


        global $_W, $_GPC;


        $uniacid = $_W['uniacid'];


        load()->func('tpl');


        $isson = false;


        if ($_GPC['son'] == 'son') {


            $res = pdo_getcolumn('yyf_company_category', array('fid' => 0, 'uniacid' => $uniacid, 'type !=' => '3'), 'id', 1);


            if (!empty($res)) {


                $cats = pdo_getall('yyf_company_category', array('fid' => 0, 'uniacid' => $uniacid, 'type !=' => '3'), array('id', 'name'));


                $isson = true;


            } else {


                message('请先添加顶级分类', $this->createWebUrl('category'));


            }


        }


        if (checksubmit()) {


            $data['name'] = $_GPC['name'];


            $data['sortrank'] = $_GPC['sortrank'];


            $data['thumb'] = $_GPC['thumb'];


            $data['type'] = $_GPC['type'];


            $_GPC['type'] == '3' ? $data['content'] = $_GPC['content'] : $data['content'] = '';


            $_GPC['isshow'] == 'on' ? $data['isshow'] = 1 : $data['isshow'] = 0;


            $_GPC['isshow_nav'] == 'on' ? $data['isshow_nav'] = 0 : $data['isshow_nav'] = 1;//这是由于之前的不合理设置，导致两个开关相反


            $data['uniacid'] = $uniacid;


            $data['show_num'] = trim($_GPC['show_num']);


            if (!empty($_GPC['son'])) {


                $data['fid'] = $_GPC['son'];


                $id = $data['fid'];


                $type = pdo_getcolumn('yyf_company_category', array('id' => $id, 'uniacid' => $uniacid), 'type', 1);


                $data['type'] = $type;


            }


            $res = pdo_insert('yyf_company_category', $data);


            if ($res) {


                message('添加分类成功', $this->createWebUrl('category'));


            }


        }


        $imgdesc = MODULE_URL . 'images/imgdesc.jpg';


        include $this->template('addcat');


    }

    public function doWebDelCattype()
    {

        global $_W, $_GPC;

        $uniacid = $_W['uniacid'];

        if ($_GPC['id']) {

            $id = $_GPC['id'];

            $data = pdo_delete('z_citytype', array('id' => $id));

            message('删除成功', $this->createWebUrl('catetype'));

        }

    }

    public function doWebEditCattype()
    {

        global $_W, $_GPC;

        if ($_GPC['id'] && !checksubmit()) {
            $id = $_GPC['id'];
            $data = pdo_get('z_citytype', array('id' => $id));
        }
        if (checksubmit()) {
            $data = array();
            $data['name'] = $_GPC['name'];
            $data['sorting'] = $_GPC['sortrank'];
            $id = $_GPC['id'];
            $res = pdo_update('z_citytype', $data, array('id' => $id));
            message('修改成功', $this->createWebUrl('catetype'));
        }
        $imgdesc = MODULE_URL . 'images/imgdesc.jpg';
        include $this->template('editcattype');


    }

    public function doWebAddCattype()
    {

        global $_W, $_GPC;

        if (checksubmit()) {
            $data['name'] = $_POST['name'];
            $data['sorting'] = $_POST['sortrank'];
            $data['carttime'] = date('Y-m-d', time());
            $res = pdo_insert('z_citytype', $data);
            if ($res) {
                message('添加分类成功', $this->createWebUrl('catetype'));
            }
        }
        $imgdesc = MODULE_URL . 'images/imgdesc.jpg';
        include $this->template('addcattype');
    }

    public function doWebCatetype()
    {

        global $_W, $_GPC;

        $uniacid = $_W['uniacid'];
        $isson = false;
        if (!empty($_GPC['son'])) {
            $isson = true;
            $fid = $_GPC['son'];
            $result = pdo_fetchall("select * from " . tablename('z_citytype') . " where 1 order by `id` desc");
        } else {
            $result = pdo_fetchall("select * from " . tablename('z_citytype') . " where 1 order by `id` desc");
        }
        include $this->template('catetype');
    }

    public function doWebCategory()
    {


        global $_W, $_GPC;


        $uniacid = $_W['uniacid'];


        if (checksubmit()) {


            foreach ($_GPC['sortrank'] as $key => $value) {


                $data['sortrank'] = $value;


                $id = $_GPC['id'][$key];


                $res = pdo_update('yyf_company_category', $data, array('id' => $id, 'uniacid' => $uniacid));


            }


            message('排序成功', $this->createWebUrl('category'));


        }


        $isson = false;


        if (!empty($_GPC['son'])) {


            $isson = true;


            $fid = $_GPC['son'];


            $result = pdo_fetchall("select * from " . tablename('yyf_company_category') . " where `uniacid`='$uniacid' and `fid`='$fid' order by `sortrank` desc");


        } else {


            $result = pdo_fetchall("select * from " . tablename('yyf_company_category') . " where `uniacid`='$uniacid' and `fid`='0' order by `sortrank` desc");


        }


        include $this->template('category');


    }

    public function doWebEditCat()
    {


        global $_W, $_GPC;


        load()->func('tpl');


        $uniacid = $_W['uniacid'];


        if ($_GPC['id'] && !checksubmit()) {


            $id = $_GPC['id'];


            $data = pdo_get('yyf_company_category', array('id' => $id, 'uniacid' => $uniacid));


            $isson = false;


            if ($_GPC['son'] == 'son') {


                $cats = pdo_getall('yyf_company_category', array('fid' => 0, 'uniacid' => $uniacid, 'type !=' => '3'), array('id', 'name'));


                $isson = true;


            }


        }


        if (checksubmit()) {


            $data = array();


            $data['name'] = $_GPC['name'];


            $data['sortrank'] = $_GPC['sortrank'];


            $data['thumb'] = $_GPC['thumb'];


            $data['type'] = $_GPC['type'];


            $_GPC['type'] == '3' ? $data['content'] = $_GPC['content'] : $data['content'] = '';


            $_GPC['isshow'] == 'on' ? $data['isshow'] = 1 : $data['isshow'] = 0;


            $_GPC['isshow_nav'] == 'on' ? $data['isshow_nav'] = 0 : $data['isshow_nav'] = 1;


            $data['show_num'] = trim($_GPC['show_num']);


            $id = $_GPC['id'];


            if (!empty($_GPC['son'])) {


                $data['fid'] = $_GPC['son'];


            }


            $res = pdo_update('yyf_company_category', $data, array('id' => $id));


            message('修改成功', $this->createWebUrl('category'));


        }


        $imgdesc = MODULE_URL . 'images/imgdesc.jpg';


        include $this->template('editcat');


    }

    public function doWebDelCat()
    {


        global $_W, $_GPC;


        $uniacid = $_W['uniacid'];


        if ($_GPC['id']) {


            $id = $_GPC['id'];


            $data = pdo_delete('yyf_company_category', array('id' => $id, 'uniacid' => $uniacid));


            message('删除成功', $this->createWebUrl('category'));


        }


    }

    public function doWebChoiceCatImg()
    {


        global $_W, $_GPC;


        $url = MODULE_URL . 'images/';


        include $this->template('choicecatimg');


    }

    public function doWebQuestion()
    {


        global $_W, $_GPC;


        include $this->template('question');


    }

    public function doWebTemplets()
    {


        global $_W, $_GPC;


        include $this->template('templets');


    }

    public function doWebForm()
    {


        global $_W, $_GPC;


        $uniacid = $_W['uniacid'];


        $formObj = tablename('yyf_company_formvalue');


        $participators = pdo_fetchall("SELECT `id` FROM " . tablename('yyf_company_formvalue') . " where `uniacid`='$uniacid' ORDER BY `id` DESC");


        $total = count($participators);


        if (!isset($_GPC['page'])) {
            $pageindex = 1;
        } else {
            $pageindex = intval($_GPC['page']);
        }


        $pagesize = 15;


        $pager = pagination($total, $pageindex, $pagesize);


        $p = ($pageindex - 1) * 15;


        $list = pdo_fetchall("select * from $formObj where `uniacid`='$uniacid' order by id desc limit " . $p . "," . $pagesize);


        foreach ($list as $key => $value) {


            $list[$key]['addtime'] = date('Y-m-d  H:i', $list[$key]['addtime']);


        }


        include $this->template('form');


    }

    public function doWebFormRead()
    {


        global $_W, $_GPC;


        load()->func('tpl');


        $id = $_GPC['id'];


        $uniacid = $_W['uniacid'];


        $data = pdo_get('yyf_company_formvalue', array('id' => $id, 'uniacid' => $uniacid));


        //print_r($data);die();


        pdo_update('yyf_company_formvalue', array('read' => '1'), array('id' => $id, 'uniacid' => $uniacid));


        include $this->template('formread');


    }

    public function doWebFormDelete($id = 0)
    {


        global $_W, $_GPC;


        $uniacid = $_W['uniacid'];


        if ($_GPC['id']) {


            $id = $_GPC['id'];


            $data = pdo_delete('yyf_company_formvalue', array('id' => $id, 'uniacid' => $uniacid));


            message('删除成功', $this->createWebUrl('form'));


        }


    }

    public function doWebFormClose()
    {


        global $_W, $_GPC;


        include $this->template('formclose');


    }

    public function doWebFormConfig()
    {


        global $_W, $_GPC;


        $uniacid = $_W['uniacid'];


        load()->func('tpl');


        $url = MODULE_URL . 'images/';


        $result = pdo_fetch("SELECT * FROM " . tablename('yyf_company_form') . " where `uniacid`='$uniacid' order by id desc limit 1");


        $v = $result;


        if (checksubmit()) {


            // print_r($_GPC);die();


            $data = array();


            $data['templet'] = $_GPC['templet'];


            $data['thumb'] = $_GPC['thumb'];


            $data['catname'] = $_GPC['catname'];


            $data['t1name'] = $_GPC['t1name'];


            $data['t1show'] = isset($_GPC['t1show']) ? $_GPC['t1show'] = 1 : '';


            $data['t1full'] = isset($_GPC['t1full']) ? $_GPC['t1full'] = 1 : '';


            $data['t2name'] = $_GPC['t2name'];


            $data['t2show'] = isset($_GPC['t2show']) ? $_GPC['t2show'] = 1 : '';


            $data['t2full'] = isset($_GPC['t2full']) ? $_GPC['t2full'] = 1 : '';


            $data['t3name'] = $_GPC['t3name'];


            $data['t3show'] = isset($_GPC['t3show']) ? $_GPC['t3show'] = 1 : '';


            $data['t3full'] = isset($_GPC['t3full']) ? $_GPC['t3full'] = 1 : '';


            $data['t4name'] = $_GPC['t4name'];


            $data['t4show'] = isset($_GPC['t4show']) ? $_GPC['t4show'] = 1 : '';


            $data['t4full'] = isset($_GPC['t4full']) ? $_GPC['t4full'] = 1 : '';


            $data['rname'] = $_GPC['rname'];


            $data['rshow'] = isset($_GPC['rshow']) ? $_GPC['rshow'] = 1 : '';


            $data['rfull'] = isset($_GPC['rfull']) ? $_GPC['rfull'] = 1 : '';


            $data['rvalue'] = $_GPC['rvalue'];


            $data['cname'] = $_GPC['cname'];


            $data['cshow'] = isset($_GPC['cshow']) ? $_GPC['cshow'] = 1 : '';


            $data['cfull'] = isset($_GPC['cfull']) ? $_GPC['cfull'] = 1 : '';


            $data['cvalue'] = $_GPC['cvalue'];


            $data['aname'] = $_GPC['aname'];


            $data['ashow'] = isset($_GPC['ashow']) ? $_GPC['ashow'] = 1 : '';


            $data['afull'] = isset($_GPC['afull']) ? $_GPC['afull'] = 1 : '';


            $data['desc'] = $_GPC['desc'];


            $data['interval'] = $_GPC['interval'];


            $data['successtext'] = $_GPC['successtext'];


            if ($result['id']) {


                $res = pdo_update('yyf_company_form', $data, array('id' => $result['id']));


            } else {


                $data['uniacid'] = $uniacid;


                $res = pdo_insert('yyf_company_form', $data);


            }


            if ($res) {


                message('修改成功');


            }


        }
        include $this->template('formconfig');
    }

    public function doWebXiangcechakan()
    {
        global $_W, $_GPC;
        $list = pdo_fetchall("select  * from `ims_z_poster` where 1  order by id desc");

        $list_json = json_encode($list);


        if (!empty($_POST['select'])) {
            $where = $_POST['select'];
            $list = pdo_fetchall("select  * from `ims_z_poster` where `user_id`  = '{$where}'  order by id desc");
        } else {
            $uniacid = $_W['uniacid'];
            $participators = pdo_fetchall("SELECT `id` FROM " . tablename('z_poster') . " where 1 ORDER BY `id` DESC");
            $total = count($participators);
            if (!isset($_GPC['page'])) {
                $pageindex = 1;
            } else {
                $pageindex = intval($_GPC['page']);
            }
            $pagesize = 15;
            $pager = pagination($total, $pageindex, $pagesize);
            $p = ($pageindex - 1) * 15;
            $list = pdo_fetchall("select  * from `ims_z_poster` where 1 and `user_id` is not null order by id desc limit " . $p . "," . $pagesize);
        }
        foreach ($list as $key => $value) {
            /*$list[$key]['poster'] = $_W['siteroot'].'attachment/hb/'.$list[$key]['src'];*/
            $list[$key]['carttime'] = date('Y-m-d H:m:s', $list[$key]['carttime']);
        }

        foreach ($list as $index => $item) {
            $list[$index]['user'] = pdo_get('ims_z_user', ['id' => $item['user_id']]);
            $list[$index]['poster'] = $_W['siteroot'] . 'attachment/hb/' . $item['src'];
            /*旗手传旗名次*/
            $list[$index]['qishou'] = pdo_get('ims_z_chuanqi', ['jie_id' => $item['user']['id']]);
        }

        foreach ($list as $index => $item) {
            /*旗手传旗名次*/
            $list[$index]['qishou'] = pdo_get('ims_z_chuanqi', ['jie_id' => $item['user']['id']]);
        }

        /*获取当前用户选择的主题*/
        foreach ($list as $index => $item) {
            $bg_img = pdo_get('ims_z_bgimg', ['id' => $item['user']['bgimg_id']]);
            $list[$index]['bg_img'] = tomedia($bg_img['src3']);
        }

        include $this->template('xiangcechakan');
    }

    public function doWebJuanxianjilu()
    {
        global $_W, $_GPC;

        if (!empty($_POST['name']) && empty($_POST['tel'])) {
            $where0 = $_POST['name'];
            $list = pdo_fetchall("select  *,a.id as aid,a.price as aprice from `ims_z_order` as a left join `ims_z_user` as b on a.user_id=b.id where b.`name` like '%{$where0}%'  order by a.id desc");
        } else if (empty($_POST['name']) && !empty($_POST['tel'])) {
            $where1 = $_POST['tel'];
            $list = pdo_fetchall("select  *,a.id as aid,a.price as aprice from `ims_z_order` as a left join `ims_z_user` as b on a.user_id=b.id where b.`tle` like '%{$where1}%'  order by a.id desc");
        } else if (!empty($_POST['name']) && !empty($_POST['tel'])) {
            $where0 = $_POST['name'];
            $where1 = $_POST['tel'];
            $list = pdo_fetchall("select  *,a.id as aid,a.price as aprice from `ims_z_order` as a left join `ims_z_user` as b on a.user_id=b.id where b.`tle` like '%{$where1}%' and b.`name` like '%{$where0}%'  order by a.id desc");
        } else if (empty($_POST['name']) && empty($_POST['tel'])) {
            $uniacid = $_W['uniacid'];
            $participators = pdo_fetchall("SELECT `id` FROM " . tablename('z_order') . " where 1 and  a.type = 1 ORDER BY `id` DESC");
            $total = count($participators);
            if (!isset($_GPC['page'])) {
                $pageindex = 1;
            } else {
                $pageindex = intval($_GPC['page']);
            }
            $pagesize = 15;
            $pager = pagination($total, $pageindex, $pagesize);
            $p = ($pageindex - 1) * 15;
            $list = pdo_fetchall("select *,a.id as aid,a.price as aprice,a.carttime as acarttime from `ims_z_order` as a left join `ims_z_user` as b on a.user_id=b.id where 1 and  a.type = 1 order by a.id desc limit " . $p . "," . $pagesize);
        }
        foreach ($list as $key => $value) {
            $list[$key]['acarttime'] = date('Y-m-d H:m:s', $list[$key]['acarttime']);
        }
        include $this->template('juanxianjilu');
    }

    public function doWebXianxiadakuan()
    {
        global $_W, $_GPC;

        $list = pdo_getall('ims_z_oredercard');
        foreach ($list as $key => $value) {
            $list[$key]['acarttime'] = date('Y-m-d H:m:s', $list[$key]['carttime']);
        }

        $copeList = json_decode(json_encode($list));
        foreach ($list as $key => $value) {
            $list[$key]['user'] = pdo_get('ims_z_user', ['id' => $value['user_id']]);
        }

        if ($_W['ispost']) {
            $err = pdo_update('ims_z_oredercard',
                [
                    'status' => 1,
                    'price' => $_GPC['price']
                ],
                ['id' => $_GPC['id']]
            );
            message('保存成功', $this->createWebUrl('xianxiadakuan'), 'success');
        }

        include $this->template('xianxiadakuan');
    }

    public function doWebShoudao()
    {
        global $_W, $_GPC;

        $status = $_GPC['status'];
        $id = $_GPC['id'];
        if ($status == 1) {
            $status_end = 0;
        } else {
            $status_end = 1;
        }
        pdo_update("z_oredercard", array("status" => $status_end), array("id" => $id));

    }

    public function doWebChuanditongji()
    {
        global $_W, $_GPC;
        if (!empty($_POST['name']) && empty($_POST['tel'])) {
            $where0 = $_POST['name'];
            $list = pdo_fetchall("select  * from `ims_z_user` where `name` like '%{$where0}%'  order by id desc");
        } else if (empty($_POST['name']) && !empty($_POST['tel'])) {
            $where1 = $_POST['tel'];
            $list = pdo_fetchall("select  * from `ims_z_user` where `tle` like '%{$where1}%' order by id desc");
        } else if (!empty($_POST['name']) && !empty($_POST['tel'])) {
            $where0 = $_POST['name'];
            $where1 = $_POST['tel'];
            $list = pdo_fetchall("select  * from `ims_z_user` where `tle` like '%{$where1}%' and `name` like '%{$where0}%'  order by id desc");
        } else if (empty($_POST['name']) && empty($_POST['tel'])) {
            $uniacid = $_W['uniacid'];
            $participators = pdo_fetchall("SELECT `id` FROM " . tablename('z_user') . " where 1 ORDER BY `id` DESC");
            $total = count($participators);
            if (!isset($_GPC['page'])) {
                $pageindex = 1;
            } else {
                $pageindex = intval($_GPC['page']);
            }
            $pagesize = 15;
            $pager = pagination($total, $pageindex, $pagesize);
            $p = ($pageindex - 1) * 15;
            $list = pdo_fetchall("select  * from `ims_z_user` where 1 order by id desc limit " . $p . "," . $pagesize);
        }
        $sum = 0;
        foreach ($list as $key => $value) {

            $chuan_sum = pdo_fetch("select count(chuan_id) as chuan_sum from ims_z_chuanqi where chuan_id = " . $list[$key]['id']);
            $sum += $list[$key]['sum'];
            $list[$key]['chuan_sum'] = $chuan_sum['chuan_sum'];

        }
        $ren_sum = pdo_fetch("select count(id) as id from ims_z_user where status = 0");

        include $this->template('chuanditongji');

    }

    public function doWebChuanqi_tongji()
    {
        global $_W, $_GPC;
        $id = $_GPC['id'];


        $list_ci = pdo_fetchall("select  * from `ims_z_user` where chuanqi_id = :chuanqi_id order by id desc ", array(":chuanqi_id" => $id));
        foreach ($list_ci as $key => $value) {
            $list_ci[$key]['carttime'] = date('Y-m-d H:m:s', $list_ci[$key]['carttime']);
            $list_ci[$key]['ren_sum_ci'] = count($list_ci);
        }

        return json_encode($list_ci);
    }
}