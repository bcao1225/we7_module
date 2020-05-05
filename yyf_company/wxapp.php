<?php
defined('IN_IA') or exit('Access Denied');

class Yyf_companyModuleWxapp extends WeModuleWxapp
{
    /*获取用户信息*/
    public function doPageUserInfo()
    {
        global $_GPC, $_W;
        $this->result(0, '获取成功', ['user' => pdo_get('ims_z_user', ['openid' => $_GPC['openid']])]);
    }

    public function doPagewchatLogin()
    {

        /*
         * 查询微信openid
         */
        $js_code = $_GET['js_code'];
        $appid = trim('wx9ee789cbafe90acd');
        $appsecret = trim('2b9ed14b77a19fdb6a2b4c46440143f1');
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret=' . $appsecret . '&js_code=' . $js_code . '&grant_type=authorization_code';
        $wx_res = file_get_contents($url);
        $wx_res_arr = json_decode($wx_res, true);
        if (empty($wx_res_arr['openid'])) {
            return $this->jsonResponse(0, [], "未能获取到openid");
        }
        $data['nickname'] = $_GET['wchat_user_name'];
        $data['photo'] = $_GET['wchat_user_avatar'];
        $data['openid'] = $wx_res_arr['openid'];
        $querey = pdo_get('z_user', array('openid' => $wx_res_arr['openid']));
        if (empty($querey)) {
            $res = pdo_insert('z_user', $data);
        }
        $userinfo = pdo_get('z_user', array('openid' => $wx_res_arr['openid']));
        $move['status'] = 1;
        $move['userinfo'] = $userinfo;
        $move['type'] = '登录成功';
        return json_encode($move);
    }

    public function doPagesilde()
    {
        /*
         * 首页图片查询
         */
        $silde = pdo_fetchall("select * from " . tablename('yyf_company_slide') . " where 1 order by `sortrank` desc");
        $move['status'] = 1;
        $move['silde'] = $silde;
        $move['type'] = '查询成功';
        return json_encode($move);
    }

    public function zhuanhuan($url)
    {

        $filename = 'https://xcx.bg580.cn/attachment/hys/5769716817707318.png';
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        switch ($extension) {
            case 'png':
                $image = imagecreatefrompng($filename);
                break;
            case 'gif':
                $image = imagecreatefromgif($filename);
                break;

        }
        $filenames = 'ceshi.jpg';
        header("Content-type: text/html; charset=utf-8");
        $res = imagejpeg($image, $filenames);
        if ($res) {
            //echo '转换图片成功';
            return $filenames;
        } else {
            // echo '转换图片失败';die;
            return $filename;
        }
    }

    public function caijian($url)
    {
        load()->func('file');
        //图片裁剪
        $src_path = $url;
        $ext = pathinfo($src_path, PATHINFO_EXTENSION);
        if ($ext == 'png') {
            $src_path = $this->zhuanhuan($src_path);
        }
        $ext = pathinfo($src_path, PATHINFO_EXTENSION);
        $rand_name = time() . "11." . $ext;
        $rand_name1 = time() . "." . $ext;
        file_image_thumb($src_path, IA_ROOT . '/attachment/caijian/' . $rand_name1, 300);
        file_image_crop(IA_ROOT . '/attachment/caijian/' . $rand_name1, IA_ROOT . '/attachment/caijian/' . $rand_name, 343, 195);
        return $src_path;
    }

    /*获取用户海报*/
    public function doPageUserHaibao()
    {
        global $_GPC, $_W;

        $user = pdo_get('ims_z_user', ['openid' => $_GPC['openid']]);
        $this->result(0, '获取成功', ['haibao' => $_W['siteroot'] . 'attachment/hb/' . $user['poster']]);
    }

    public function doPagehaibao()
    {
        /*
         * 海报内容查询
         */

        $userinfo = pdo_get('z_user', array('openid' => $_GET['openid']));

        $queryimg = pdo_get('z_bgimg', array('id' => $userinfo['bgimg_id']));
        $code = $this->codeimg($_GET['openid']);
        $fileimg = '../attachment/photo/' . date("YmdHis") . ".";
        $photo = $this->upload_headimg($userinfo['photo'], $fileimg);
        //$imgurl = $this->caijian($userinfo['image']);
        $imgurl = $userinfo['image'];
        $max = pdo_fetch("SELECT id  FROM ims_z_poster where user_id =" . $userinfo['id']);
        if ($max['id']) {
            $max['max'] = $max['id'];
        } else {
            $max = pdo_fetch("SELECT (max(id)+1) as max  FROM ims_z_poster where 1");
        }
        $sum = pdo_fetch("select sum(sum) as sum from ims_z_user");
        $move['status'] = 1;
        //$move['photo'] = $photo;
        $move['photo'] = substr($photo, 3);

        //$move['code'] = $code;
        $move['code'] = substr($code, 4);
        $move['userinfo'] = $userinfo;
        $move['userinfo']['max'] = $max['max'] ? $max['max'] : "1";
        //$move['userinfo']['image'] = 'https://'.$_SERVER['HTTP_HOST'].'/attachment/caijian2/'.$imgurl;
        $move['userinfo']['image'] = $imgurl;
        $move['userinfo']['sum'] = $sum['sum'];
        $move['userinfo']['bgimg'] = $queryimg['src'];
        $move['userinfo']['bgimg2'] = $queryimg['src2'];
        $move['userinfo']['bgimg3'] = $queryimg['src3'];
        $move['type'] = '查询成功';
        $img_info = getimagesize($imgurl);
        $move['userinfo']['image_width'] = $img_info[0];
        $move['userinfo']['image_height'] = $img_info[1];
        return json_encode($move);
    }

    function upload_headimg($headimgurl, $fileimg)
    {
        $header = array(
            'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:45.0) Gecko/20100101 Firefox/45.0',
            'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
            'Accept-Encoding: gzip, deflate',);
        $url = $headimgurl;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        $data = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($code == 200) {//把URL格式的图片转成base64_encode格式的！
            $imgBase64Code = "data:image/jpeg;base64," . base64_encode($data);
        }
        $img_content = $imgBase64Code;//图片内容
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img_content, $result)) {
            $type = $result[2];//得到图片类型png?jpg?gif?
            $new_file = $fileimg . $type;
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $img_content)))) {

                return $new_file;
            }
        }
        return false;
    }

    public function doPagecard()
    {
        /*
         * 开户银行查询
         */
        $config['a1'] = pdo_get('z_config', array('id' => 3));
        $config['a2'] = pdo_get('z_config', array('id' => 4));
        $config['a3'] = pdo_get('z_config', array('id' => 5));
        $move['status'] = 1;
        $move['silde'] = $config;
        $move['type'] = '查询成功';
        return json_encode($move);
    }

    public function doPagebgimg()
    {
        /*
         *  背景图片背景分类查询
         */
        $bgimg = pdo_fetchall("select * from " . tablename('z_bgimg') . " where 1 ");
        $citytype = pdo_fetchall("select * from " . tablename('z_citytype') . " where 1 order by sorting asc");
        $quereyuser = pdo_get('z_user', array('openid' => $_GET['openid']));
        if ($quereyuser['tle']) {
            $move['status'] = 0;
            $move['type'] = '该用户已填写信息';
        } else {
            $move['status'] = 1;
            $move['silde']['bgimg'] = $bgimg;
            $move['silde']['citytype'] = $citytype;
            $move['type'] = '查询成功';
        }

        return json_encode($move);
    }

    public function doPageWchatpay()
    {
        /*
         * 线上支付生成订单
         */
        $openid = $_GET['openid'];
        $quereyuser = pdo_get('z_user', array('openid' => $_GET['openid']));
        $data['user_id'] = $quereyuser['id'];
        $data['carttime'] = time();
        $data['status'] = 0;
        $data['type'] = 0;
        $data['pay_type'] = 0;
        $data['price'] = $_GET['money'];
        $data['ordernumlist'] = time() . rand(1000, 9999);
        $quereyorder = pdo_get('z_order', array('ordernumlist' => $data['ordernumlist']));
        $move['status'] = 1;
        $move['order'] = $quereyorder;
        $move['type'] = '查询成功';
        return json_encode($move);
    }

    /*线下打款生成数据*/
    public function doPageformared()
    {
        global $_GPC,$_W;
        $user = pdo_get('ims_z_user',['openid'=>$_GPC['openid']]);
        pdo_insert('ims_z_oredercard',[
            'user_id'=>$user['id'],
            'status'=>0,
            'name'=>$_GPC['name'],
            'tel'=>$_GPC['tel'],
            'wx'=>$_GPC['wxh'],
            'text'=>$_GPC['text'],
            'price'=>0,
            'carttime'=>time()
        ]);

        $this->result(0,'记录成功',pdo_get('ims_z_oredercard',['id'=>pdo_insertid()]));
    }

    public function doPageaddform()
    {
        global $_GPC, $_W;

        $openid = $_GPC['openid'];
        /*
         * 修改个人资料
         */

        $user_info = pdo_fetch("select * from ims_z_user where openid=:openid", array(":openid" => $openid));

        $user_infotoo = pdo_fetch("select * from ims_z_user where status=0 and lng > 1 and lat > 1 and id < " . $user_info['id'] . " order by id desc");

        $radLat1 = deg2rad($user_infotoo['lat']); //deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($_GET['latitude']);
        $radLng1 = deg2rad($user_infotoo['lng']);
        $radLng2 = deg2rad($_GET['longitude']);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137;
        /*当精度维度全为空时*/
        if (empty($user_infotoo['lat']) || empty($user_infotoo['lng'])) {
            $s = 0;
        }

        $sumjvli = number_format($s, 2);

        $data['grade'] = $_GET['nianji'];
        $data['zhuanye'] = $_GET['zhuanye'];
        $data['unit'] = $_GET['gongzuodanwei'];
        $data['Identity'] = $_GET['danwei'];
        $data['name'] = $_GET['xingming'];
        $data['tle'] = $_GET['tel'];
        $data['email'] = $_GET['email'];
        $data['wxh'] = $_GET['wxh'];
        $data['zengyan'] = $_GET['zengyan'];
        $data['gerenjieshao'] = $_GET['gerenjieshao'];
        $data['carttime'] = time();
//        $data['bgimg_id'] = $_GET['name'];
        $data['image'] = $_GET['img'];
        $data['bgimg_id'] = $_GET['beijing'];
        $data['lat'] = $_GET['latitude'];
        $data['lng'] = $_GET['longitude'];
        $data['sum'] = $sumjvli;
        // var_dump($sumjvli);die;
        $res = pdo_update('z_user', $data, array('openid' => $openid));
        $move['status'] = 1;
        $move['order'] = $data;
        $move['type'] = '修改成功成功';

        $user = pdo_get('z_user', array('openid' => $openid));

        $quereyuser = pdo_get('z_poster', array('user_id' => $user['id']));
        if (empty($quereyuser)) {
            $is['status'] = 0;
            $is['user_id'] = $user['id'];
            $is['carttime'] = time();
            pdo_insert('z_poster', $is);
        }

        return json_encode($move);
    }

    public function doPagegetLocation()
    {

        /*
         * 获取用户经纬度
         */
        $openid = $_GET['openid'];
        $quereyuser = pdo_get('z_user', array('openid' => $_GET['openid']));
        //$cid  = $quereyuser['id'] - 1;
        //  $quereyusers=pdo_get('z_user',array('id'=>$cid));
        // return json_encode($quereyusers);
        $data['lat'] = $_GET['latitude'];
        $data['lng'] = $_GET['longitude'];
        //if($quereyusers){
        //   $cc['lat'] = $quereyusers['lat'];
        //   $cc['lng'] = $quereyusers['lng'];
        //   $id = $quereyuser['id'];
        //  if(empty($quereyuser['sum'])){
        //   	 $data['sum'] = ($this->getDistance($data['lat'], $data['lng'], $cc['lat'], $cc['lng']));
        //    	 $data['sum'] = $data['sum'] /1000;
        //   }
        // }
        //  $res=pdo_update('z_user',$data,array('openid'=>$_GET['openid']));
        $data['openid'] = $_GET['openid'];
        $move['status'] = 1;
        $move['order'] = $data;
        $move['type'] = '添加成功';
        return json_encode($move);
    }

    function getdistance($lng1, $lat1, $lng2, $lat2)
    {
        // 将角度转为狐度
        $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
        return $s;
    }

    function cids($id)
    {
        $cid = $id - 1;
        $quereyusers = pdo_get('z_user', array('id' => $cid));
        if ($quereyusers['lat'] && $quereyusers['lng']) {
            return $quereyusers;
        } else {
            $quereyusers = $this->cids($cid);
        }
    }

    public function doPagefoundimg()
    {

        /*
         * 获取用户信息
         */
        $id = $_GET['id'];
        $quereyuser = pdo_get('z_user', array('id' => $id));
        $move['status'] = 1;
        $move['user'] = $quereyuser;
        $move['type'] = '添加成功';
        return json_encode($move);
    }

    public function doPageChuanqi()
    {
        /*
         * 传旗
         
         */
        // $users=pdo_get('z_user',array('openid'=>$_GET['cid']));
        $user = pdo_get('z_user', array('openid' => $_GET['openid']));

        $users = pdo_fetch("SELECT * from ims_z_user where lat > 0 and id < " . $user['id'] . " ORDER BY id desc limit 1");

        $query = pdo_get('z_chuanqi', array('chuan_id' => $users['id'], 'jie_id' => $user['id']));
        if (empty($query)) {
            $juli = $this->getdistance($user['lng'], $user['lat'], $users['lng'], $users['lat']);
            $juli = $juli / 1000;
            // $changbian =  sqrt(($users['lng'] - $user['lng'])^2 + ($users['lat'] - $user['lat'])^2);

            // if($changbian>10000){
            // 	$changbian = 0;
            // }
            // $sum = pdo_fetch("select sum(sum) as sum from ims_z_user where 1");
            $data['chuan_id'] = $users['id'];
            $data['jie_id'] = $user['id'];
            // $data['juli'] = $changbian;
            $data['createtime'] = time();
            $res = pdo_insert('z_chuanqi', $data);
            /*不是累加*/
            /*$update['sum'] = $users['sum'] + $juli;
            $res = pdo_update('z_user', $update, array('id' => $users['id']));*/
            $move['status'] = 1;
            $move['order'] = $data;
            $move['type'] = '添加成功';
        } else {
            $move['status'] = 0;
            $move['order'] = $query;
            $move['type'] = '添加失败';
        }

        return json_encode($move);

    }

    function codeimg($openid)
    {
        /*
         * 获取二维码
         */

        $appid = 'wx9ee789cbafe90acd';
        $appsecrit = '2b9ed14b77a19fdb6a2b4c46440143f1';
        $access_token = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecrit";
        $json = $this->httpRequest($access_token);
        $json = json_decode($json, true);

        if (isset($json['access_token']) && isset($json['expires_in'])) {
            $param = array(
                'scene' => $openid,
                'page' => 'yyf_company/pages/index/index',
            );
            $param = json_encode($param);
            $qcode = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=" . $json['access_token'];
            $result = $this->httpRequest($qcode, $param, "POST");
            $base64_image = "data:image/jpeg;base64," . base64_encode($result);
            $path = '../attachment/qdcode/';
            $erweima = $this->base64_image_content($base64_image, $path);
            return $erweima;
        }

    }

    public function base64_image_content($base64_image_content, $path)
    {
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
            $type = $result[2];
            $new_file = $path . "/";
//            return$base64_image_content;
            if (!file_exists($new_file)) {
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($new_file);
            }
            $new_file = $new_file . time() . ".{$type}";
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))) {
                return '/' . $new_file;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function httpRequest($url, $data = '', $method = 'GET')
    {

        /*
         * 发送请求
         */
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        if ($method == 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data != '') {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
        }

        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;

    }

    /*线上支付接口*/
    public function doPagePay()
    {
        global $_GPC, $_W;

        //构造支付参数
        $order = array(
            'tid' => intval($_GPC['orderid']), //订单号
            'user' => $_GPC['openid'], //用户OPENID
            'fee' => $_GPC['money'], //金额
            'title' => '活动募捐',
            'openid' => $_GPC['openid'], //用户OPENID
        );

        //生成支付参数，返回给小程序端
        $pay_params = $this->pay($order);
        if (is_error($pay_params)) {
            return $this->result(1, '支付失败1，请重试', $pay_params);
        }

        /*获取用户数据*/
        $user = pdo_get('ims_z_user',['openid'=>$_GPC['openid']]);

        $err = pdo_insert('ims_z_order',[
            'user_id'=>$user['id'],
            'carttime'=>time(),
            'status'=>0,
            'type'=>0,
            'price'=>$_GPC['money'],
            'ordernumlist'=>$_GPC['orderid']
        ]);

        return $this->result(0, '保存订单成功',$pay_params);
    }

    /*更改线上支付是否支付成功*/
    public function doPageUpDatePay(){
        global $_GPC,$_W;
        $user = pdo_get('ims_z_user',['openid'=>$_GPC['openid']]);
        pdo_update('ims_z_order',['type'=>1],['user_id'=>$user['id']]);
        $this->result(0,'支付成功',$user);
    }

    public function payResult($params)
    {
        if ($params['result'] == 'success' && $params['from'] == 'notify') {
            //此处会处理一些支付成功的业务代码
        }
        //因为支付完成通知有两种方式 notify，return,notify为后台通知,return为前台通知，需要给用户展示提示信息
        //return做为通知是不稳定的，用户很可能直接关闭页面，所以状态变更以notify为准
        //如果消息是用户直接返回（非通知），则提示一个付款成功
        //如果是JS版的支付此处的跳转则没有意义
        if ($params['from'] == 'return') {
            if ($params['result'] == 'success') {
                message('支付成功！', '../../app/' . url('mc/home'), 'success');
            } else {
                return $this->result(1, '支付失败，请重试');
            }
        }
    }


    public function doPageUpimage()
    {
        global $_GPC, $_W;

        $uptypes = array('image/jpg', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/gif', 'image/bmp', 'image/x-png');
        $max_file_size = 2000000; //上传文件大小限制, 单位BYTE
        $destination_folder = "../attachment/hys/"; //上传文件路径
        if (!is_uploaded_file($_FILES["upfile"]['tmp_name'])) //是否存在文件
        {
            echo "图片不存在!";
            exit;
        }
        $file = $_FILES["upfile"];
        // if ($max_file_size < $file["size"])
        //     //检查文件大小
        // {
        //     return "文件太大!";
        //    exit;
        // }
        if (!in_array($file["type"], $uptypes)) //检查文件类型
        {
            return "文件类型不符!" . $file["type"];
            exit;
        }
        if (!file_exists($destination_folder)) {
            mkdir($destination_folder);
        }
        $filename = $file["tmp_name"];
        $pinfo = pathinfo($file["name"]);
        $ftype = $pinfo['extension'];
        $destination = $destination_folder . str_shuffle(time() . rand(111111, 999999)) . "." . $ftype;
        if (file_exists($destination) && $overwrite != true) {
            return "同名文件已经存在了";
            exit;
        }
        if (!move_uploaded_file($filename, $destination)) {
            return "移动文件出错";
            exit;
        }
        $pinfo = pathinfo($destination);
        $fname = $pinfo['basename'];

        @require_once(IA_ROOT . '/framework/function/file.func.php');
        @$filename = $fname;
        @file_remote_upload($filename);

        return $fname;

    }

    public function doPageUpimage1()
    {
        global $_GPC, $_W;

        $uptypes = array('image/jpg', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/gif', 'image/bmp', 'image/x-png');
        $max_file_size = 2000000; //上传文件大小限制, 单位BYTE
        $destination_folder = "../attachment/hys/"; //上传文件路径
        if (!is_uploaded_file($_FILES["upfile"]['tmp_name'])) //是否存在文件
        {
            echo "图片不存在!";
            exit;
        }
        $file = $_FILES["upfile"];
        // if ($max_file_size < $file["size"])
        //     //检查文件大小
        // {
        //     return "文件太大!";
        //    exit;
        // }
        if (!in_array($file["type"], $uptypes)) //检查文件类型
        {
            return "文件类型不符!" . $file["type"];
            exit;
        }
        if (!file_exists($destination_folder)) {
            mkdir($destination_folder);
        }
        $filename = $file["tmp_name"];
        $pinfo = pathinfo($file["name"]);
        $ftype = $pinfo['extension'];
        $destination = $destination_folder . str_shuffle(time() . rand(111111, 999999)) . "." . $ftype;
        if (file_exists($destination) && $overwrite != true) {
            return "同名文件已经存在了";
            exit;
        }
        if (!move_uploaded_file($filename, $destination)) {
            return "移动文件出错";
            exit;
        }
        $pinfo = pathinfo($destination);
        $fname = $pinfo['basename'];

        @require_once(IA_ROOT . '/framework/function/file.func.php');
        @$filename = $fname;
        @file_remote_upload($filename);

        return $_W['siteroot'] . 'attachment/hys/' . $fname;

    }

    public function doPageUpimage2()
    {
        global $_GPC, $_W;

        $user = pdo_get('z_user', array('openid' => $_GPC['openid']));
        $poster = pdo_get('z_poster', array('user_id' => $user['id']));

        if (!empty($poster)) {

            $uptypes = array('image/jpg', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/gif', 'image/bmp', 'image/x-png');
            $max_file_size = 2000000; //上传文件大小限制, 单位BYTE
            $destination_folder = "../attachment/hb/"; //上传文件路径
            if (!is_uploaded_file($_FILES["upfile"]['tmp_name'])) //是否存在文件
            {
                echo "图片不存在!";
                exit;
            }
            $file = $_FILES["upfile"];
            // if ($max_file_size < $file["size"])
            //检查文件大小
            //  {
            //  return "文件太大!";
            //    exit;
            // }
            if (!in_array($file["type"], $uptypes)) //检查文件类型
            {
                return "文件类型不符!" . $file["type"];
                exit;
            }
            if (!file_exists($destination_folder)) {
                mkdir($destination_folder);
            }
            $filename = $file["tmp_name"];
            $pinfo = pathinfo($file["name"]);
            $ftype = $pinfo['extension'];
            $destination = $destination_folder . str_shuffle(time() . rand(111111, 999999)) . "." . $ftype;
            if (file_exists($destination) && $overwrite != true) {
                return "同名文件已经存在了";
                exit;
            }
            if (!move_uploaded_file($filename, $destination)) {
                return "移动文件出错";
                exit;
            }
            $pinfo = pathinfo($destination);
            $fname = $pinfo['basename'];

            @require_once(IA_ROOT . '/framework/function/file.func.php');
            @$filename = $fname;
            @file_remote_upload($filename);
            //此处会处理一些支付成功的业务代码
            $data['src'] = $fname;
            $data['status'] = 0;
            $data['user_id'] = $user['id'];
            $data['carttime'] = time();
            pdo_update('z_poster', $data, array('user_id' => $user['id']));
            //$res=pdo_update('z_user',$data,array('openid'=>$openid));
            return $fname;
        } else {
            return json_encode('该用户已生成');;
        }

    }

    public function doPageUpimage3()
    {
        global $_GPC, $_W;

        $openid = $_GPC['openid'];
        $user = pdo_get('z_user', array('openid' => $_GPC['openid']));

        if (empty($user['poster'])) {

            $uptypes = array('image/jpg', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/gif', 'image/bmp', 'image/x-png');
            $max_file_size = 2000000; //上传文件大小限制, 单位BYTE
            $destination_folder = "../attachment/hb/"; //上传文件路径
            if (!is_uploaded_file($_FILES["upfile"]['tmp_name'])) //是否存在文件
            {
                echo "图片不存在!";
                exit;
            }
            $file = $_FILES["upfile"];
            // if ($max_file_size < $file["size"])
            //检查文件大小
            //{
            //    return "文件太大!";
            //    exit;
            // }
            if (!in_array($file["type"], $uptypes)) //检查文件类型
            {
                return "文件类型不符!" . $file["type"];
                exit;
            }
            if (!file_exists($destination_folder)) {
                mkdir($destination_folder);
            }
            $filename = $file["tmp_name"];
            $pinfo = pathinfo($file["name"]);
            $ftype = $pinfo['extension'];
            $destination = $destination_folder . str_shuffle(time() . rand(111111, 999999)) . "." . $ftype;
            if (file_exists($destination) && $overwrite != true) {
                return "同名文件已经存在了";
                exit;
            }
            if (!move_uploaded_file($filename, $destination)) {
                return "移动文件出错";
                exit;
            }
            $pinfo = pathinfo($destination);
            $fname = $pinfo['basename'];

            @require_once(IA_ROOT . '/framework/function/file.func.php');
            @$filename = $fname;
            @file_remote_upload($filename);
            //此处会处理一些支付成功的业务代码
            $data['poster'] = $fname;
            $res = pdo_update('z_user', $data, array('openid' => $openid));
            return $fname;
        } else {
            return json_encode('该用户已生成');
        }

    }

}