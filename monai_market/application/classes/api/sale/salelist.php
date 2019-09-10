<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/9
 * Time: 9:27
 */
if (!(defined('IN_IA')))
{
    exit('Access Denied');
}
class Api_Sale_Salelist extends WeModuleWxapp
{
    //获取对应类型的卖车列表，已上架，未上架
    public function getlist()
    {
        global $_GPC, $_W;
        $uniacid=$_W['uniacid'];
        $uid=$_GPC['uid']?$_GPC['uid']:0;
        //获取用户查询状态：0全部，1未上架，2已上架
        $type=$_GPC['types']?$_GPC['types']:0;
        $where=' uid='.$uid.' AND uniacid='.$uniacid.' AND delete_time=0';
        if($type==1){
            $where.=' AND status<3';
        }
        if($type==2){
            $where.=' AND status=3';
        }
        $limit = '';
        $page = 6;
        $pageId = $_GPC['leftid'] > 0 ? $_GPC['leftid'] : 1;
        $cur_page = ($pageId - 1) * $page;
        $limit = " LIMIT " . $cur_page . "," . $page;
        //审核未通过的全部属于未上架，且返回审核状态；审核通过的，显示审核通过但是上下架状态由用户控制
//        $sql='SELECT a.id,a.status,a.uid,a.price,a.name,a.km,a.introduce,a.agelimit,b.img_patch,a.audit_type FROM'.tablename('monai_market_car_detail')
//            .' AS a,'.tablename('monai_market_image').' AS b WHERE '.$where.' GROUP BY a.id  ORDER BY a.id DESC '.$limit;

        $sql='SELECT * FROM'.tablename('monai_market_car_detail')
            .' WHERE '.$where.' ORDER BY id DESC '.$limit;
        $cars=pdo_fetchall($sql);
        if($cars){
            foreach($cars as &$c){
                $c['toptype']='未置顶';
                if($c['top_time']>0){
                    $c['toptype']=$c['expiry_time']>time()?"置顶到期时间:".date('Y年m月d日 H:i',$c['expiry_time']):'置顶过期';
                }
                $c['km']=ceil($c['km'])==$c['km']?intval($c['km']):$c['km'];
                $c['img_patch']=tomedia($c['carimg']);
            }
        }

        foreach ($cars as $key=>$car){
            /*获取二级分类名称*/
            $cars[$key]['brand2'] = pdo_get('ims_monai_market_brand',['id'=>$car['brand2']]);
            /*获取三级分类名称*/
            $cars[$key]['brand3'] = pdo_get('ims_monai_market_brand',['id'=>$car['brand3']]);
        }

        $user=pdo_get('monai_market_member',array('uniacid'=>$uniacid,'uid'=>$uid));
		   $is_vip=2;//当前用户是否是vip，1为是vip，2为不是
        if($user['is_vip']==1 && $user['end_time']>time()){
            $is_vip=1;
        }
        $user['is_vip']=$is_vip;
        return $this->result(0,'',array('cars'=>$cars,'user'=>$user));
    }
    //编辑汽车前获取汽车信息
    public function getcardetail()
    {
        global $_GPC, $_W;
        $uniacid=$_W['uniacid'];
        //获取汽车id，判断是否存在
        $carid=$_GPC['car'];
        $car=pdo_get('monai_market_car_detail',array('id'=>$carid,'uid'=>$_GPC['uid'],'uniacid'=>$uniacid));
        if(!$car){
            return $this->result(500,'获取当前汽车信息失败','');
        }
        $car['selecttime1']=str_replace("年","-",$car['year']);
        $car['selecttime1']=str_replace("月","",$car['selecttime1']);
        $carimg=pdo_getall('monai_market_image',array('product_id'=>$carid,'uniacid'=>$uniacid));
        $carsimg[]=[];
        foreach($carimg as $k=>&$c){
            $carsimg[$k]['id']=$c['id'];
            $carsimg[$k]['imgshort']=$c['img_patch'];
            $carsimg[$k]['intro']=$c['intro'];
            $carsimg[$k]['img']=tomedia($c['img_patch']);
        }
        $brand=pdo_get('monai_market_brand',array('id'=>$car['brand'],'uniacid'=>$uniacid),array('name'));
        $classname=pdo_get('monai_market_class',array('id'=>$car['class'],'uniacid'=>$uniacid),array('name'));
        $class=pdo_getall('monai_market_class',array('uniacid'=>$uniacid),array(),'',array('sort DESC'));
        return $this->result(0, '',
            array('car'=>$car,
                'classname'=>$classname,
                'img'=>$carsimg,
                'class'=>$class,
                'brand'=>$brand));
    }

    //执行编辑
    public function upsale()
    {
        global $_GPC, $_W;
        $uniacid=$_W['uniacid'];
        $uid=$_GPC['uid'];
        $car=pdo_get('monai_market_car_detail',array('id'=>$_GPC['id'],'uid'=>$_GPC['uid'],'uniacid'=>$uniacid));
        if(!$car){
            return $this->result(0,'获取汽车信息失败','');
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
        $upimgs=json_decode(htmlspecialchars_decode($_GPC['carimgs']),true);

        $carimg=$upimgs[0]['imgshort'];

        // 根据经纬度获取省市区
        $province = '';
        $city = '';
        $district = '';
        if ($_GPC['mapy'] && $_GPC['mapx']) {
            if ($_GPC['mapy'] != $car['y'] || $_GPC['mapx'] != $car['x']) {
                $line = pdo_get('monai_market_info', array('uniacid'=>$uniacid), array('map_key'));
                if ($line['map_key']) {
                    $res = file_get_contents("http://apis.map.qq.com/ws/geocoder/v1/?location={$_GPC['mapx']},{$_GPC['mapy']}&key={$line['map_key']}");
                    $arr = json_decode($res, true);
                    $province = $arr['result']['address_component']['province'];
                    $city = $arr['result']['address_component']['city'];
                    $district = $arr['result']['address_component']['district'];
                }
            }
        }

        $insaledb=[
            'uid'=>$uid,
            'uniacid'=>$uniacid,
            'introduce'=>$_GPC['introduce'],
            'price'=>$_GPC['price'],
            'agelimit'=>$agelimit,
            'year'=>$_GPC['times'],
            'brand'=>$_GPC['brand'],
            'km'=>$_GPC['kilometre'],
            'y'=>$_GPC['mapy'],
            'x'=>$_GPC['mapx'],
            'class'=>$_GPC['classid'],
            'name'=>$_GPC['carname'],
            'status'=>1,
            'exhaust'=>$_GPC['exhaust'],
            'watchcar'=>$_GPC['watchcar'],
            'insurance'=>$_GPC['insurance'],
            'vehicletime'=>$_GPC['vehicletime'],
            'gearbox'=>$_GPC['gearboxindex'],
            'carimg'=>$carimg,
            'province' => $province,
            'city' => $city,
            'district' => $district,
            'caraddress'=>$_GPC['address'],
            'username'=>$_GPC['username'],
            'phone'=>$_GPC['phone'],
            'synopsis'=>$_GPC['synopsis']
        ];
        pdo_begin();
        pdo_update('monai_market_car_detail',$insaledb,array('id'=>$_GPC['id']));
        //将新添加的分离出来，并插入表
        $inimgdb=[
            'uniacid'=>$uniacid,
            'product_id'=>$_GPC['id'],
            'type'=>3,
            'create_time'=>time(),
            'car_title'=>$_GPC['carname']
        ];
        foreach($upimgs as $u){
            if($u['id']=='new'){
                $inimgdb['img_patch']=$u['imgshort'];
                $inimgdb['intro']=$u['intro'];
                $inimg=pdo_insert('monai_market_image',$inimgdb);
                if(!$inimg){
                    pdo_rollback();
                    return $this->result(505, '汽车图文信息更新失败','');
                }
                continue;
            }
            $inimgdb['img_patch']=$u['imgshort'];
            $inimgdb['intro']=$u['intro'];
            pdo_update('monai_market_image',$inimgdb,array('id'=>$u['id']));
        }
        //执行删除操作
        $delimg=json_decode(htmlspecialchars_decode($_GPC['dodelimgs']),true);
        foreach($delimg as $i){
            $this->delImg($i['img_patch']);
            @pdo_delete('monai_market_image',array('id'=>$i['id'],'uniacid'=>$uniacid));
        }
        pdo_commit();
        return $this->result(0, '修改成功','');
    }
    //获取付费上架汽车的操作
    public function getfee()
    {
        global $_GPC, $_W;
        $uniacid=$_W['uniacid'];
        $infosql='SELECT release_money,top_money,top_cycle FROM'.tablename('monai_market_info').' WHERE uniacid='.$uniacid;
        $fee=pdo_fetch($infosql);
        return $this->result(0,'',['onshelffee'=>($fee['release_money']?$fee['release_money']:0),'settopfee'=>($fee['top_money']?$fee['top_money']:0),'settoptime'=>($fee['top_cycle']?$fee['top_cycle']:0)]);
    }
    //汽车信息上架操作
    public function onshelf()
    {
        global $_GPC, $_W;
        $uniacid=$_W['uniacid'];
        //获取汽车id，判断是否存在
        $carid=$_GPC['car'];
        //获取汽车id，判断是否存在
        $car=pdo_get('monai_market_car_detail',array('id'=>$carid,'uid'=>$_GPC['uid'],'uniacid'=>$uniacid));
        if(!$car){
            return $this->result(500,'获取当前汽车信息失败','');
        }
        //判断是否支付
//        if($car['status']<2){
//            return $this->result(550,'请完成支付后在上架','');
//        }
        //修改为上架
        $ifuop=pdo_update('monai_market_car_detail',array('status'=>3,'audit_type'=>1),array('id'=>$carid,'uniacid'=>$uniacid));
        if(!$ifuop){
            return $this->result(555,'上架失败，请确认当前汽车是否已上架','');
        }
        return $this->result(0,'上架成功','');
        //判断当前汽车是否已经支付，未支付提示错误，已支付，修改为待审核

    }
    //汽车信息下架操作
    public function downshelf()
    {
        global $_GPC, $_W;
        $uniacid=$_W['uniacid'];
        //获取汽车id，判断是否存在
        $carid=$_GPC['car'];
        $car=pdo_get('monai_market_car_detail',array('id'=>$carid,'uid'=>$_GPC['uid'],'uniacid'=>$uniacid));
        if(!$car){
            return $this->result(500,'获取当前汽车信息失败','');
        }
        //修改为已付款
        $ifuop=pdo_update('monai_market_car_detail',array('status'=>2),array('id'=>$carid,'uniacid'=>$uniacid));
        if(!$ifuop){
            return $this->result(550,'下架失败，请确认当前汽车是否已下架','');
        }
        return $this->result(0,'下架成功','');
    }
    //执行删除操作
    public function delsale()
    {
        global $_GPC, $_W;
        $uniacid=$_W['uniacid'];
        $carid=$_GPC['car'];
        //获取删除卖车信息
        $car=pdo_get('monai_market_car_detail',array('id'=>$carid,'uid'=>$_GPC['uid'],'uniacid'=>$uniacid));
        //判断用户是否发布该汽车
        if(!$car){
            return $this->result(500,'获取当前汽车信息失败','');
        }
        //删除卖车信息
        $isdel=pdo_update('monai_market_car_detail',array('delete_time'=>time()),array('id'=>$carid,'uniacid'=>$uniacid));
        if($isdel){
            return $this->result(0,'删除成功','');
        }
//        $images=pdo_getall('monai_market_image',array('product_id'=>$carid,'uniacid'=>$uniacid));
//        //删除对应图片及图片表的记录
//        foreach ($images as $i){
//            if($i['img_patch']){
//                $this->delImg($i['img_patch']);
//            }
//        }
//        @pdo_delete('monai_market_image',array('product_id'=>$carid,'uniacid'=>$uniacid));
        return $this->result(500,'删除失败请稍后重试！','');
    }
    //删除图片
    public function delImg($imgurl)
    {
        global $_W;
        //拆分url链接去除开头，传值删除
        $fileurl=str_replace($_W['siteroot'].'attachment/','',$imgurl);
        load()->func('file');
        if (empty($_W['setting']['remote']['type'])) {
            if(file_is_image($fileurl)){
                @file_delete($fileurl);
            }
            return true;
        }
        $arr=explode('/',$imgurl);
        @file_remote_delete($arr[count($arr)-1]);
        return true;
    }
    //获取收藏汽车列表
    public function getcollection()
    {
        global $_GPC, $_W;
        $limit = '';
        $page = 5;
        $pageId = $_GPC['leftid'] > 0 ? $_GPC['leftid'] : 1;
        $cur_page = ($pageId - 1) * $page;
        $limit = " LIMIT " . $cur_page . "," . $page;
        $where=" mmfl.uid=".$_GPC['uid'].' AND mmfl.type=2 AND mmfl.status=1 AND mmfl.uniacid='.$_W['uniacid'].' AND mmcd.id!=\'\'';
        $sql='SELECT mmcd.id,mmcd.status,mmcd.delete_time,mmcd.name,mmcd.km,mmcd.agelimit,mmcd.price,mmcd.class_name,mmcd.carimg FROM'.tablename('monai_market_follow_logs')
            .' AS mmfl LEFT JOIN '.tablename('monai_market_car_detail').' AS mmcd ON mmfl.ucar_id=mmcd.id  WHERE '.$where.' GROUP BY mmfl.id ORDER BY mmcd.id DESC '.$limit;
        $cars=pdo_fetchall($sql);
        if($cars){
            foreach($cars as &$c) {
                $c['km']=ceil($c['km'])==$c['km']?intval($c['km']):$c['km'];
                $c['img_patch']=tomedia($c['carimg']);
            }
        }
        return $this->result(0,'',$cars);
    }
    //对汽车进行置顶操作
    public function maketop()
    {
        global $_GPC, $_W;
        $uniacid=$_W['uniacid'];
        //获取汽车id，判断是否存在
        $carid=$_GPC['car'];
        //获取汽车id，判断是否存在
        $car=pdo_get('monai_market_car_detail',array('id'=>$carid,'uid'=>$_GPC['uid'],'uniacid'=>$uniacid));
        if(!$car){
            return $this->result(500,'获取当前汽车信息失败','');
        }
        $infosql=@pdo_fetch('SELECT top_money,top_cycle FROM'.tablename('monai_market_info').' WHERE uniacid='.$uniacid);
        $up_time = $car['expiry_time']<time()?time():$car['expiry_time'];
        $endTime=$up_time+($infosql['top_cycle']*60*60*24);
        //修改为上架
        $ifuop=pdo_update('monai_market_car_detail',array('top_time'=>time(),'expiry_time'=>$endTime),array('id'=>$carid,'uniacid'=>$uniacid));
        if(!$ifuop){
            return $this->result(555,'置顶失败，请确认当前汽车是否已上架','');
        }
        //修改资金表数据
        $up_finance = pdo_update('monai_market_finance',['status'=>1,'pay_time'=>time()],['id'=>$_GPC['orderid'],'uniacid'=>$uniacid]);
        if(!$up_finance)
        {
            return $this->result(500,'置顶失败','');
        }
        return $this->result(0,'置顶成功','');
        //判断当前汽车是否已经支付，未支付提示错误，已支付，修改为待审核
    }
}