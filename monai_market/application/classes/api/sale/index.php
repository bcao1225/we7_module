<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/5
 * Time: 16:45
 */

if (!(defined('IN_IA')))
{
    exit('Access Denied');
}
class Api_Sale_Index extends WeModuleWxapp
{
    //获取卖车分类及类型
    public function saleindex()
    {

        global $_GPC, $_W;
        $uniacid=$_W['uniacid'];
        //查看类型（monai_market_class）
//        var_dump($uniacid);die;
        $class=pdo_getall('monai_market_class',array('uniacid'=>$uniacid),array(),'',array('sort DESC'));
        //获取公告
//        $notice=pdo_getall('monai_market_notice',array('uniacid'=>$uniacid),array(),'',array('id DESC'));
        //获取当前用户信息
        $user=pdo_get('monai_market_member',array('uniacid'=>$uniacid,'uid'=>$_GPC['uid']));
        if($user['status']==1){
            return $this->result(500, '您的账户已被拉黑，请联系管理员','');
        }
        if ($user){
            if($this->ismaxpost(1)){
                return $this->result(500, '当前无法继续发帖,请开通VIP','');
            }
        }
        return $this->result(0, '',array('class'=>$class,'user'=>$user));
    }

    //是否到达最大发帖量 1=卖车 2=求购
    public function ismaxpost($type=1){
        global $_GPC,$_W;
        $uniacid=$_W['uniacid'];
        $uid = $_GPC['uid'];
        $userinfo=pdo_get('monai_market_member',array('uniacid'=>$uniacid,'uid'=>$uid));
        if(empty($userinfo)){
            return true;
        }
        if($userinfo['isvip']==1&&$userinfo['end_time']>time()){
            return false;
        }
        if($type==1){
            $sql = "SELECT COUNT(*) FROM ".tablename('monai_market_car_detail')." WHERE uniacid={$uniacid} AND uid={$uid}";
        }else{
            $sql = "SELECT COUNT(*) FROM ".tablename('monai_market_gujia')." WHERE uniacid={$uniacid} AND uid={$uid}";
        }
        $count = pdo_fetchcolumn($sql);
        if($count >= 5){
            return true;
        }
        return false;
    }
    public function getthecarimg($img='')
    {
        global $_GPC;
        if($img==''){
            $img=$_GPC['img'];
        }
        ob_clean();
        $imgurl=tomedia($img);
        if(strpos($img,'.png') !==false){
            header("Content-type: image/png");
            $codeImage = imagecreatefrompng($imgurl);
            imagepng($codeImage);
            imagedestroy($codeImage);
            exit;
        }
        header("Content-type: image/jpeg");
        $codeImage = imagecreatefromjpeg($imgurl);
        imagejpeg($codeImage);
        imagedestroy($codeImage);
        exit;
    }
    //卖车--》向数据表插入数据
    public function insale()
    {
        global $_GPC, $_W;
        $uniacid=$_W['uniacid'];
        $uid=$_GPC['uid'];
        if($uid=='undefined' || !$uid || $uid==''){
            return $this->result(0, '405','');
        }
        //判断当前用户是否存在member表，不存在则进行插入
        $member=pdo_get('monai_market_member',array('uniacid'=>$uniacid,'uid'=>$uid));
        if($member['status']==1){
            return $this->result(500, '您的账户已被拉黑，请联系管理员','');
        }
        if(!$member){
            $vqmember=pdo_get('mc_members',array('uid'=>$uid));
            $inmemberdb=[
                'uniacid'=>$uniacid,
                'uid'=>$uid,
                'nickname'=>$vqmember['nickname'],
                'phone'=>$_GPC['phone'],
                'address'=>$_GPC['address'],
                'is_vip'=>2,
                'head_image'=>$vqmember['avatar'],
                'create_time'=>time()
            ];
            pdo_insert('monai_market_member',$inmemberdb);
            $memberid=pdo_insertid();
            if($memberid){
                $params=['page' => 'pages/store/index', 'scene' => $memberid];
                $qr=Qrcode::instance()->getwxacodeunlimit($params);
                @pdo_update('monai_market_member',array( 'qrcode' =>$qr['short'] ),array('id'=>$uid));
            }
        }
        if(!$member['qrcode']){
            $params=['page' => 'pages/store/index', 'scene' => $member['id']];
            $qr=Qrcode::instance()->getwxacodeunlimit($params);
            @pdo_update('monai_market_member',array( 'qrcode' =>$qr['short'] ),array('id'=>$uid));
        }
        //计算当前年限，将字符串类型的时间，转换为int，与当前时间对比计算年限
        $agenum=(time()-strtotime($_GPC['times1']))/(60*60*24*365);
        if(ceil($agenum)==$agenum){
            $agelimit=$agenum;
        }else{
            $agelimit=number_format($agenum,1);
            if($agelimit==intval($agelimit)){
                $agelimit=intval($agelimit);
            }
        }
        $class=pdo_get('monai_market_class',array('id'=>$_GPC['classid']));
        $images=json_decode(html_entity_decode($_GPC['carimgs']),true);

        // 根据经纬度获取省市区
        $province = '';
        $city = '';
        $district = '';
        if ($_GPC['mapy'] && $_GPC['mapx']) {
            $line = pdo_get('monai_market_info', array('uniacid'=>$_W['uniacid']), array('map_key'));
            if ($line['map_key']) {
                $res = file_get_contents("http://apis.map.qq.com/ws/geocoder/v1/?location={$_GPC['mapx']},{$_GPC['mapy']}&key={$line['map_key']}");
                $arr = json_decode($res, true);
                $province = $arr['result']['address_component']['province'];
                $city = $arr['result']['address_component']['city'];
                $district = $arr['result']['address_component']['district'];
            }
        }

        if ($member){
            if($this->ismaxpost(1)){
                return $this->result(500, '当前无法继续发帖,请开通VIP','');
            }
        }

        $identify = empty($_GPC['identify'])?random(8,true):$_GPC['identify'];
        $insaledb=[
            'uid'=>$uid,
            'uniacid'=>$uniacid,
            'price'=>$_GPC['price'],
            'agelimit'=>$agelimit,
            'year'=>$_GPC['times'],
            'brand'=>$_GPC['brand'],
            'brand2'=>$_GPC['brand2'],
            'brand3'=>$_GPC['brand3'],
            'km'=>$_GPC['kilometre'],
            'y'=>$_GPC['mapy'],
            'x'=>$_GPC['mapx'],
            'class'=>$_GPC['classid'],
            'class_name'=>$class['name'],
            'name'=>$_GPC['carname'],
            'status'=>1,
            'exhaust'=>$_GPC['exhaust'],
            'watchcar'=>$_GPC['watchcar'],
            'insurance'=>$_GPC['insurance'],
            'vehicletime'=>$_GPC['vehicletime'],
            'carimg'=>$images[0]['imgshort'],
            'caraddress'=>$_GPC['address'],
            'province' => $province,
            'city' => $city,
            'district' => $district,
            'username'=>$_GPC['username'],
            'gearbox'=>$_GPC['gearboxindex'],
            'phone'=>$_GPC['phone'],
            'create_time'=>time(),
            'synopsis' => $_GPC['synopsis'],
            'producttime' => $_GPC['producttime'],
            'identify' => $identify,
            'transnum' => $_GPC['transnum'],
            'effluentstand' => $_GPC['effluentstand'],
            'carcolor' => $_GPC['carcolor'],
            'cardes' => $_GPC['cardes'],
            'cardestype' => $_GPC['cardestype'],
        ];
        pdo_begin();
        $insale=pdo_insert('monai_market_car_detail',$insaledb);
        if(!$insale){
            pdo_rollback();
            return $this->result(500, '网络异常请稍候重试','');
        }
        $saleid=pdo_insertid();
        //向图片表插入图片信息
        $inimgdb=[
            'uniacid'=>$uniacid,
            'product_id'=>$saleid,
            'type'=>3,
            'create_time'=>time(),
            'car_title'=>$_GPC['carname']
        ];
        foreach($images as $i){
            if($i['imgshort']!=''){
                $inimgdb['img_patch']=$i['imgshort'];
                $inimgdb['intro']=$i['intro'];
                $inimg=pdo_insert('monai_market_image',$inimgdb);
                if(!$inimg){
                    pdo_rollback();
                    return $this->result(505, '汽车图片信息获取失败','');
                }
            }

        }
        pdo_commit();
        return $this->result(0, '提交成功',$saleid);
    }
    //获取用户手机号
    public function userphone()
    {
        global $_GPC, $_W;
        $account_api = WeAccount::create();
        $encrypt_data = $_GPC['encryptedData'];
        $iv = $_GPC['iv'];

        if (empty($_SESSION['session_key']) || empty($encrypt_data) || empty($iv)) {
            $account_api->result(1, '请先登录');
        }
        $phone = $account_api->pkcs7Encode($encrypt_data, $iv);
        if($phone && $phone['phoneNumber']){
            return $this->result(0, '',$phone['phoneNumber']);
        }
        return $this->result(500, '手机号码获取失败！请手动输入',$phone);
    }
    //获取用户分享的汽车单图
    public function getcarimgindex()
    {
        global $_GPC, $_W;
        $uniacid=$_W['uniacid'];
        $carid=$_GPC['carid'];
        $uid=$_GPC['uid'];
//        $img=pdo_get('monai_market_saleinfo',array('uniacid'=>$uniacid));
        $saleuser=pdo_get('monai_market_member',array('uniacid'=>$uniacid,'uid'=>$uid));
        if(!$saleuser['head_image'] || $saleuser['head_image']==''){
            $fans=pdo_get('mc_members',array('uid'=>$uid));
            if(!$fans){
                return $this->result(5000,'获取用户信息失败请重新授权','');
            }
            $saleuser['head_image']=$fans['avatar'];
            pdo_update('monai_market_member',array('head_image'=>$fans['avatar']),array('uniacid'=>$uniacid,'uid'=>$uid));
        }
        if(!$saleuser['qrcode'] || $saleuser['qrcode_time']<1528338159){
            $params=['page' => 'pages/store/index', 'scene' =>$uid];
            $qrcode=Qrcode::instance()->getwxacodeunlimit($params);
            $saleuser['qrcode']='';
            if($qrcode && $qrcode['all']){
                @pdo_update('monai_market_member',array( 'qrcode' => $qrcode['short']),array('user_id'=>$saleuser['user_id']));
                $saleuser['qrcode']=$qrcode['all'];
            }
        }else{
            $saleuser['qrcode']=tomedia($saleuser['qrcode']);
        }
        $images=isset($saleuser['store_img'])?tomedia($saleuser['store_img']):'/pages/image/store_bg.png';
        $info=pdo_get('monai_market_info',array('uniacid'=>$uniacid));
        return $this->result(0, '',[
            'carimg'=>$images,'qrcode'=>$saleuser['qrcode'],'headimg'=>$saleuser['head_image'],'info'=>$info,'enddelimg'=>[]]);
        //当汽车id为空时，查找uid下对应的汽车，如果没有汽车则提示请上传汽车后在推广
//        if(!$carid || $carid=='undefined'){
//            $sql='select * from '.tablename('monai_market_car_detail').' where uniacid='.$uniacid.' and uid='.$_GPC['uid'].' order by id desc';
//            $car=pdo_fetch($sql);
//            if(!$car){
//                return $this->result(500,'请发布汽车后再推广','');
//            }
//            $carid=$car['id'];
//        }
//        $sql='select * from '.tablename('monai_market_image').' where uniacid='.$uniacid.' and product_id='.$carid.' order by id desc';
//        $car=pdo_fetch($sql);
//        if(!$car){
//            return $this->result(0,'请发布汽车后再推广','');
//        }
//        $ifinstore['enddelimg']=[];
//        $car['img_patch']=tomedia($car['img_patch']);
//        if(strpos($car['img_patch'],$_W['siteroot']) ===false){
//            $car['img_patch']=$this->download($car['img_patch'],'images/temps/'.$_GPC['uid'].'/logo/'.date('his').'/');
//            $enddelimg[0]=$car['img_patch'];
//        }

//        $qr=pdo_get('monai_market_car_detail',array('uniacid'=>$uniacid,'id'=>$carid));
//        $saleuser=pdo_get('monai_market_member',array('uniacid'=>$uniacid,'uid'=>$qr['uid']));
//        if(!$saleuser['head_image'] || $saleuser['head_image']==''){
//            $fans=pdo_get('mc_members',array('uid'=>$qr['uid']));
//            if(!$fans){
//                return $this->result(5000,'获取用户信息失败请重新授权','');
//            }
//            $saleuser['head_image']=$fans['avatar'];
//            pdo_update('monai_market_member',array('head_image'=>$fans['avatar']),array('uniacid'=>$uniacid,'uid'=>$qr['uid']));
//        }
//        if(!$saleuser['qrcode'] && $saleuser['create_time']<1528181531){
//            $params=['page' => 'pages/index/index', 'scene' => $qr['uid']];
//            $qrcode=Qrcode::instance()->getwxacodeunlimit($params);
//            $saleuser['qrcode']='';
//            if($qrcode && $qrcode['all']){
//                @pdo_update('monai_market_member',array( 'qrcode' => $qrcode['short']),array('user_id'=>$saleuser['user_id']));
//                $saleuser['qrcode']=$qrcode['all'];
//            }
//        }else{
//            $saleuser['qrcode']=tomedia($saleuser['qrcode']);
//        }
////        $saleuser['head_image']=$this->download($saleuser['head_image'],'images/temps/'.$_GPC['uid'].'/logo/'.date('his').'/');
//        $enddelimg=[];
//        $info=pdo_get('monai_market_info',array('uniacid'=>$uniacid));
//        return $this->result(0, '',[
//            'carimg'=>$car,'qrcode'=>$saleuser['qrcode'],'headimg'=>$saleuser['head_image'],'info'=>$info,'enddelimg'=>$enddelimg]);
    }
    //获取用户分享的汽车单图
    public function getcarimg()
    {
        global $_GPC, $_W;
        $uniacid=$_W['uniacid'];
        $carid=$_GPC['carid'];
        $uid=$_GPC['uid'];
        $img=pdo_get('monai_market_saleinfo',array('uniacid'=>$uniacid));
        $saleuser=pdo_get('monai_market_member',array('uniacid'=>$uniacid,'uid'=>$uid));
        if(!$saleuser['head_image'] || $saleuser['head_image']==''){
            $fans=pdo_get('mc_members',array('uid'=>$uid));
            if(!$fans){
                return $this->result(5000,'获取用户信息失败请重新授权','');
            }
            $saleuser['head_image']=$fans['avatar'];
            pdo_update('monai_market_member',array('head_image'=>$fans['avatar']),array('uniacid'=>$uniacid,'uid'=>$uid));
        }
        if(!$saleuser['qrcode_index'] || !isset($saleuser['qrcode_index']) || $saleuser['qrcode_time']<1528338159){
            $params=['page' => 'pages/index/index', 'scene' =>$uid];
            $qrcode=Qrcode::instance()->getwxacodeunlimit($params);
            $saleuser['qrcode']='';
            if($qrcode && $qrcode['all']){
                pdo_update('monai_market_member',array( 'qrcode' => $qrcode['short']),array('user_id'=>$saleuser['user_id']));
                $saleuser['qrcode']=$qrcode['all'];
            }
        }else{
            $saleuser['qrcode_index']=tomedia($saleuser['qrcode_index']);
        }
//        $images=isset($img['image_patch'])?tomedia($img['image_patch']):'';




        $info=pdo_get('monai_market_info',array('uniacid'=>$uniacid));
        if($info['pop_bgimg']){
            $info['pop_bgimg'] = tomedia($info['pop_bgimg']);
        }

        $images= $info['pop_bgimg']?tomedia($info['pop_bgimg']):'';
        return $this->result(0, '',['carimg'=>$images,'qrcode'=>$saleuser['qrcode'],'headimg'=>$saleuser['head_image'],'info'=>$info,'enddelimg'=>[]]);

        //当汽车id为空时，查找uid下对应的汽车，如果没有汽车则提示请上传汽车后在推广
//        if(!$carid || $carid=='undefined'){
//            $sql='select * from '.tablename('monai_market_car_detail').' where uniacid='.$uniacid.' and uid='.$_GPC['uid'].' order by id desc';
//            $car=pdo_fetch($sql);
//            if(!$car){
//                return $this->result(500,'请发布汽车后再推广','');
//            }
//            $carid=$car['id'];
//        }
//        $sql='select * from '.tablename('monai_market_image').' where uniacid='.$uniacid.' and product_id='.$carid.' order by id desc';
//        $car=pdo_fetch($sql);
//        if(!$car){
//            return $this->result(0,'请发布汽车后再推广','');
//        }
//        $ifinstore['enddelimg']=[];
//        $car['img_patch']=tomedia($car['img_patch']);
//        if(strpos($car['img_patch'],$_W['siteroot']) ===false){
//            $car['img_patch']=$this->download($car['img_patch'],'images/temps/'.$_GPC['uid'].'/logo/'.date('his').'/');
//            $enddelimg[0]=$car['img_patch'];
//        }

//        $qr=pdo_get('monai_market_car_detail',array('uniacid'=>$uniacid,'id'=>$carid));
//        $saleuser=pdo_get('monai_market_member',array('uniacid'=>$uniacid,'uid'=>$qr['uid']));
//        if(!$saleuser['head_image'] || $saleuser['head_image']==''){
//            $fans=pdo_get('mc_members',array('uid'=>$qr['uid']));
//            if(!$fans){
//                return $this->result(5000,'获取用户信息失败请重新授权','');
//            }
//            $saleuser['head_image']=$fans['avatar'];
//            pdo_update('monai_market_member',array('head_image'=>$fans['avatar']),array('uniacid'=>$uniacid,'uid'=>$qr['uid']));
//        }
//        if(!$saleuser['qrcode'] && $saleuser['create_time']<1528181531){
//            $params=['page' => 'pages/index/index', 'scene' => $qr['uid']];
//            $qrcode=Qrcode::instance()->getwxacodeunlimit($params);
//            $saleuser['qrcode']='';
//            if($qrcode && $qrcode['all']){
//                @pdo_update('monai_market_member',array( 'qrcode' => $qrcode['short']),array('user_id'=>$saleuser['user_id']));
//                $saleuser['qrcode']=$qrcode['all'];
//            }
//        }else{
//            $saleuser['qrcode']=tomedia($saleuser['qrcode']);
//        }
////        $saleuser['head_image']=$this->download($saleuser['head_image'],'images/temps/'.$_GPC['uid'].'/logo/'.date('his').'/');
//        $enddelimg=[];
//        $info=pdo_get('monai_market_info',array('uniacid'=>$uniacid));
//        return $this->result(0, '',[
//            'carimg'=>$car,'qrcode'=>$saleuser['qrcode'],'headimg'=>$saleuser['head_image'],'info'=>$info,'enddelimg'=>$enddelimg]);
    }
    //获取汽车品牌
    public function carsbrand()
    {
        global $_GPC, $_W;
        $uniacid=$_W['uniacid'];
        $pid = intval($_GPC['pid']);
        /*
        $abc=['A' =>  [] ,'B' =>  [] , 'C' =>  [] ,'D' =>  [] ,'E' =>  [] ,'F' =>  [] ,'G' =>  [] , 'H' =>  [] ,'I' =>  [] ,'J' =>  [] , 'K' =>  [] ,
  'L' =>  [] ,'M' =>  [] ,'N' =>  [] ,'O' =>  [] ,'P' =>  [] , 'Q' =>  [] ,'R' =>  [] ,'S' =>  [] , 'T' =>  [] , 'U' =>  [] , 'V' =>  [] , 'W' =>  [] ,
  'X' =>  [] , 'Y' =>  [] , 'Z' =>  []  ];
        */
        //查看分类/品牌（monai_market_brand）
        $abc = array();
        $brand=pdo_getall('monai_market_brand',array('uniacid'=>$uniacid,'pid'=>$pid),array(),'',array('sort DESC'));
        foreach($brand as $k => $b){
//            $abc[strtoupper($b['abc'])]=$b;
            $brand[$k]['brand_icon']=tomedia($b['brand_icon']);
            if(empty($b['abc'])){
                $b['abc'] = 'A';
            }
            if(!array_key_exists(strtoupper($b['abc']),$abc)){
                $abc[strtoupper($b['abc'])] = [];
            }

            @array_push($abc[strtoupper($b['abc'])],$brand[$k]);
        }

        return $this->result(0,'',$abc);

    }
    public function carsbrand_bk()
    {
        global $_GPC, $_W;
        $uniacid=$_W['uniacid'];
        $pid = intval($_GPC['pid']);
        $abc=['A' =>  [] ,'B' =>  [] , 'C' =>  [] ,'D' =>  [] ,'E' =>  [] ,'F' =>  [] ,'G' =>  [] , 'H' =>  [] ,'I' =>  [] ,'J' =>  [] , 'K' =>  [] ,
            'L' =>  [] ,'M' =>  [] ,'N' =>  [] ,'O' =>  [] ,'P' =>  [] , 'Q' =>  [] ,'R' =>  [] ,'S' =>  [] , 'T' =>  [] , 'U' =>  [] , 'V' =>  [] , 'W' =>  [] ,
            'X' =>  [] , 'Y' =>  [] , 'Z' =>  []  ];
        //查看分类/品牌（monai_market_brand）
        $brand=pdo_getall('monai_market_brand',array('uniacid'=>$uniacid,'pid'=>$pid),array(),'',array('sort DESC'));
        foreach($brand as $k => $b){
//            $abc[strtoupper($b['abc'])]=$b;
            $brand[$k]['brand_icon']=tomedia($b['brand_icon']);
            if(empty($b['abc'])){
                $b['abc'] = 'A';
            }
            @array_push($abc[strtoupper($b['abc'])],$brand[$k]);
        }
        if(empty($brand)){
            $abc = array();
        }
        //unset($b);
        return $this->result(0,'',$abc);

    }
    //获取汽车公告
    public function getnotice()
    {
        global $_GPC, $_W;
        $limit = '';
        $page = 5;
        $pageId = $_GPC['leftid'] > 0 ? $_GPC['leftid'] : 1;
        $cur_page = ($pageId - 1) * $page;
        $limit = " LIMIT " . $cur_page . "," . $page;
        $sql='SELECT id,content,`desc` FROM '.tablename('monai_market_notice').' WHERE uniacid='.$_W['uniacid'].$limit;
        $notices=pdo_fetchall($sql);
        return $this->result(0,'',$notices);
    }
    //二手车主页获取信息
    public function getstoredetail()
    {
        global $_GPC, $_W;

        $thatuid=(!$_GPC['thatuid'] || $_GPC['thatuid']=='undefined')?0:$_GPC['thatuid'];//展示的对应用户的信息
        //当leftid大于1时表示是分页请求，只查找汽车数据，不在此查询对应的其他数据
        $pageId = $_GPC['leftid'] > 0 ? $_GPC['leftid'] : 1;
        if($pageId<=1){
            //是否关注
            $follow=pdo_get('monai_market_follow_logs',array('uniacid'=>$_W['uniacid'],'uid'=>$_GPC['uid'],'ucar_id'=>$thatuid,'type'=>1));
            $iffollow=true;
            if($follow && $follow['status']==1){
                $iffollow=false;
            }
            //查询人气值，查询发布数量，查询粉丝数量
            $sql='SELECT count(id) as coun FROM '.tablename('monai_market_follow_logs').' WHERE uniacid='.$_W['uniacid'].
                ' AND ucar_id='.$thatuid.' AND `type`=1 AND status=1';
            $fans=pdo_fetch($sql);
            $sql='SELECT count(id) as coun FROM '.tablename('monai_market_car_detail').' WHERE uniacid='.$_W['uniacid'].
                ' AND status=3 and delete_time=0 AND  uid='.$thatuid;
            $release=pdo_fetch($sql);
            $sql='SELECT SUM(browse) as sums FROM '.tablename('monai_market_car_detail').' WHERE uniacid='.$_W['uniacid'].
                ' AND uid='.$thatuid;
            $browse=pdo_fetch($sql);
            $sql='SELECT nickname,phone,address,head_image,is_vip,end_time,store_img FROM '.tablename('monai_market_member').' WHERE uniacid='.$_W['uniacid'].
                ' AND uid='.$thatuid;
            $detail=pdo_fetch($sql);
            $detail['is_member'] = false;//判断 是否是会员
            if($detail['is_vip']==1 && $detail['end_time']>time()){
                $detail['is_member'] = true;
                $detail['store_img']= tomedia($detail['store_img']);
            }
            $is_vip = $detail['is_member'];
            $sql='SELECT * FROM '.tablename('monai_market_ensure').' WHERE uniacid='.$_W['uniacid'].' order by sort desc LIMIT 4 ';
            $ensure = pdo_fetchall($sql);
            if(!empty($ensure))
            {
                foreach ($ensure as &$vt)
                {
                    @$vt['image'] = tomedia($vt['image']);
                }
            }
            else
            {
                $ensure = array(
                    0=>array(
                        'image'=>'/pages/image/shouhou_icn.png',
                        'name'=>'售后保障'
                    ),
                    1=>array(
                        'image'=>'/pages/image/chekuang_icn.png',
                        'name'=>'车况保障'
                    ),
                    2=>array(
                        'image'=>'/pages/image/price_icon.png',
                        'name'=>'交易保障'
                    ),
                    3=>array(
                        'image'=>'/pages/image/shouchedj_btn.png',
                        'name'=>'价格公道'
                    ),
                );
            }
        }
        else
        {
            $is_vip = $_GPC['is_vip'];
        }
        if($is_vip)
        {
            //会员查询  $pageId判断是否首次进入 分页时只有后面参数来区分是否是置顶的数据
            //置顶
            if($pageId<=1 || $is_vip==1)
            {
                $where=' uid='.$thatuid.' AND status=3 AND uniacid='.$_W['uniacid'].' AND delete_time=0 and top_time>0 and expiry_time>'.time();
                $page = 4;
                $order = 'top_time';
                $cars = $this->select_car($where, $page, $pageId, $order);
            }
            //非置顶
            if($pageId<=1 || $is_vip==2)
            {
                $where=' uid='.$thatuid.' AND status=3 AND uniacid='.$_W['uniacid'].' AND delete_time=0 and expiry_time<'.time();
                $page = 5;
                $order = 'id';
                $cars2 = $this->select_car($where, $page, $pageId, $order);
            }
        }
        else
        {
            //非会员查询
            $where=' uid='.$thatuid.' AND status=3 AND uniacid='.$_W['uniacid'].' AND delete_time=0';
            $page = 5;
            $order = 'id';
            $cars = $this->select_car($where, $page, $pageId, $order);
            $cars2 = [];
        }
        if($pageId<=1){
            return $this->result(0,'',array(
                'iffollow'=>$iffollow,
                'fans'=>$fans['coun'],
                'release'=>$release['coun'],
                'detail'=>$detail,
                'cars'=>$cars,
                'browse'=>$browse['sums'],
                'cars2'=>$cars2,
                'ensure'=>$ensure,
            ));
        }else
        {
            $cars = empty($cars) ? $cars2 : $cars;
            if(empty($cars))
            {
                $cars = 'null';
            }
            return $this->result(0,$where,$cars);
        }
    }
    /*
     * 查询车辆
     * page每页数量
     * $pageId 第几页
     * 其他参数不解释了
     */
    public function select_car($where, $page, $pageId, $order='id')
    {
        $limit = '';
        $cur_page = ($pageId - 1) * $page;
        $limit = " LIMIT " . $cur_page . "," . $page;
        $sql='SELECT id,status,uid,price,name,km,introduce,agelimit,carimg,audit_type,create_time,browse,year,exhaust,gearbox FROM'.tablename('monai_market_car_detail')
            .' WHERE '.$where.' ORDER BY '.$order.' DESC '.$limit;
        $cars=pdo_fetchall($sql);
        if($cars){
            foreach($cars as &$c) {
                $thistime=time()-$c['create_time'];
                $c['time']=date('Y.m.d',$c['create_time']);
                if ($thistime < 60) {
                    $c['time']='刚刚';
                } elseif ($thistime < 3600) {
                    $c['time']=floor($thistime / 60) . '分钟前';
                } elseif ($thistime < 86400) {
                    $c['time']=floor($thistime / 3600) . '小时前';
                } elseif ($thistime < 259200) {//3天内
                    $c['time']=floor($thistime / 86400) . '天前';
                }
                $c['km']=ceil($c['km'])==$c['km']?intval($c['km']):$c['km'];
                $c['img_patch']=tomedia($c['carimg']);
            }
        }
        return $cars;
    }
    public function download($url, $path ='images/temps/')
    {
        global $_W;
        load()->func('file');
        if(!is_dir(ATTACHMENT_ROOT.$path)){
            mkdirs(ATTACHMENT_ROOT.$path);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $file = curl_exec($ch);
        curl_close($ch);
        $filename = pathinfo($url, PATHINFO_BASENAME);
        $resource = @fopen(ATTACHMENT_ROOT . $path . $filename, 'a');
        fwrite($resource, $file);
        fclose($resource);
        return $_W['siteroot'].'attachment/'.$path . $filename;
    }
    public function salenew()
    {
        global $_GPC, $_W;
        $uniacid=$_W['uniacid'];
        $info = pdo_get('monai_market_info',array('uniacid'=>$uniacid));
        $info['sale_logo'] = tomedia($info['sale_logo']);
        return $this->result(0,'',$info);
    }
    public function ingujia()
    {
        global $_GPC, $_W;
        $uniacid=$_W['uniacid'];
        $img = json_decode(html_entity_decode($_GPC['carimages']),true);

        $data = array(
            'uniacid'=>$uniacid,
            'uid'=>$_GPC['uid'],
            'address'=>$_GPC['address'],
            'topdate'=>$_GPC['topdate'],
            'phone'=>$_GPC['phone'],
            'licheng'=>$_GPC['licheng'],
            'cartype'=>$_GPC['cartype'],
            'img1'=>$img[0]['imgshort'],
            'img2'=>$img[1]['imgshort'],
            'img3'=>$img[2]['imgshort'],
            'time'=>time(),
        );
        $res = pdo_insert('monai_market_gujia',$data);
        if($res){
            return $this->result(0,200);
        }else {
            return $this->result(0,500);
        }
    }

    public function ingujiayc()
    {
        global $_GPC, $_W;
        $uniacid=$_W['uniacid'];
        $img = json_decode(html_entity_decode($_GPC['carimages']),true);

        if($this->ismaxpost(2)){
            return $this->result(500,'当前无法发贴,请开通VIP');
        }

        $data = array(
            'uniacid'=>$uniacid,
            'uid'=>$_GPC['uid'],
            'pricemin' => $_GPC['pricemin'],
            'pricemax' => $_GPC['pricemax'],
            'address'=>$_GPC['address'],
            'brand' => $_GPC['brand'],
            'brand2' => $_GPC['brand2'],
            'brand3' => $_GPC['brand3'],
            'topdate'=>$_GPC['topdate'],
            'phone'=>$_GPC['phone'],
            'licheng'=>$_GPC['licheng'],
            'carcolor' => $_GPC['carcolor'],
            'cartype'=>$_GPC['cartype'],
            'otherdes' => $_GPC['otherdes'],
            'time'=>time(),
        );
        $res = pdo_insert('monai_market_gujia',$data);
        if($res){
            return $this->result(0,200);
        }else {
            return $this->result(0,500);
        }
    }
    //获取用户分享的汽车单图
    public function getcarimg111()
    {
        global $_GPC, $_W;
        $uniacid=$_W['uniacid'];
        $carid=$_GPC['carid'];
        $uid=$_GPC['uid'];

        $carinfo = pdo_get('monai_market_car_detail',array('uniacid'=>$uniacid,'id'=>$carid));
        if(empty($carinfo)){
            return $this->result(0,'车辆不存在','');
        }
        $carimg = tomedia($carinfo['carimg']);
        //下一个if 走强制获取新的二维码
        if(empty($carinfo['qrcode_index']) || 1==1){
            $params=['page' => 'pages/home/carMessage/carMessage', 'scene' => $carid];
            $qrcode=Qrcode::instance()->getwxacodeunlimit($params);
            if($qrcode && $qrcode['all']){
                @pdo_update('monai_market_car_detail',array( 'qrcode_index' => $qrcode['short'],'qrcode_time'=>time()),array('id'=>$carid));
                $qrcode=$qrcode['all'];
            }
        }else{
            $qrcode = tomedia($carinfo['qrcode_index']);
        }
        $carinfo['one'] = $carinfo['name'];

        /*$carinfo['agelimit'] ==0 ? "全新车":'车龄'.$carinfo['agelimit'].'年';
        if($carinfo['gearbox']=='0')
        {
            $gearbox = '手自一体';
        }else if($carinfo['gearbox']=='1')
        {
            $gearbox = '手动挡';
        }elseif($carinfo['gearbox']=='2')
        {
            $gearbox = '自动挡';
        }
        $carinfo['two'] = $carinfo['agelimit'].' '.$carinfo['exhaust'].' '.$gearbox;*/
        $carinfo['two'] = '上牌时间:'.str_replace('月','',str_replace("年",".",$carinfo['year'])).'    价格:'.$carinfo['price'].'万    行驶:'.(int)$carinfo['km'].'万公里';
        $info=pdo_get('monai_market_info',array('uniacid'=>$uniacid));
       return $this->result(0, '',[
           'carimg'=>$carimg,'qrcode'=>$qrcode,'headimg'=>'','info'=>$carinfo,'enddelimg'=>'']);
    }
    public function inloan()
    {
        global $_GPC, $_W;
        $uniacid=$_W['uniacid'];

        $data = array(
            'uniacid'=>$uniacid,
            'uid'=>$_GPC['uid'],
            'phone'=>$_GPC['phone'],
            'time'=>time(),
        );
        $lishi = pdo_get('monai_market_loan',array(
            'uniacid'=>$uniacid,
            'uid'=>$_GPC['uid'],
            'phone'=>$_GPC['phone'],
            'state'=>0
        ));
        if(!empty($lishi))
        {
            return $this->result(2,'您近期已提交过申请');
        }
        $res = @pdo_insert('monai_market_loan',$data);
        if($res){
            return $this->result(0,200);
        }else {
            return $this->result(2,'提交失败，请重试');
        }
    }
}