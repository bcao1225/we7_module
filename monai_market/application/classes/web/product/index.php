<?php

if (!(defined('IN_IA')))

{

	exit('Access Denied');

}

class Web_Product_Index extends Web_Base

{

    /*

    * 产品

     */

    public function product()

	{

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        $name=$_GPC['name'];

        $status = $_GPC['status'];

        $where='';

        if($name!='')

        {

            $where = " and a.name like'%".$name."%'";

        }

        if ($status=='1') {



            $where = " and a.uid =0";

        }

        if ($status=='2') {



            $where = " and a.uid >0";

        }

        $pindex = max(1, intval($_GPC['page']));

        $psize = 12;

        $list=pdo_fetchall("select a.*,b.name as type_name from ".tablename('monai_market_car_detail')."  as a Left Join" .tablename('monai_market_brand')." as b On a.brand2=b.id where a.`uniacid`='$uniacid' and a.`delete_time`=0 $where order by a.id desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
        
        $total = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_car_detail')."  as a Left Join" .tablename('monai_market_brand')." as b On a.brand=b.id where a.`uniacid`='$uniacid' and a.`delete_time`=0 $where order by a.id desc");

        $pager = pagination2($total, $pindex, $psize);

        $i=($pindex - 1) * $psize+1;

        include $this->template();

	}

    /*

    * 添加产品

     */

    public function product_add()

	{

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        /*随机生成一个6位数，前缀为CMP，并且不能重复*/
        $serial_number = 'CMP'.random(6,true); //生成一个纯数字随机数
        $identify_list = pdo_getall("ims_monai_market_car_detail",[],['identify']); //获取车辆的所有编号

        //如果随机数存在于数组中就循环一次，重新生成一个随机数，再次判断
        while (in_array($serial_number,$identify_list)){
            $serial_number = 'CMP'.random(6,true); //生成一个纯数字随机数
        }

        $brand=pdo_fetchall("SELECT * FROM ".tablename('monai_market_brand')." where `uniacid`='$uniacid' AND pid=0 order by id desc");

        $class=pdo_fetchall("SELECT * FROM ".tablename('monai_market_class')." where `uniacid`='$uniacid' order by id desc");

        $cardestype = array(
            array(
                'id'=>1,
                'name'=>'（A+）原版原漆',
                'des'=>'全车没做过油漆没做过钣金 全车螺丝未动',
            ),
            array(
                'id'=>2,
                'name'=>'（A）精品',
                'des'=>'全车补漆少,钣金少',
            ),
            array(
                'id'=>3,
                'name'=>'（B）小瑕疵',
                'des'=>'车身有喷漆板金,有更换覆盖件,但结构件没动到',
            ),
            array(
                'id'=>4,
                'name'=>'（C）小事故',
                'des'=>'ABC柱后围侧围水箱框架有钣金修复,面积较小',
            ),
            array(
                'id'=>5,
                'name'=>'（D）大事故',
                'des'=>'大梁动过,ABC柱,后围侧围有切割或火烧,泡水',
            ),
            array(
                'id'=>6,
                'name'=>'其他',
                'des'=>'发动机 变速箱大修或者更换',
            ),
        );

        load()->func('tpl');

        if($_W['ispost']){

            //var_dump($_GPC['img_patch']);exit;;



            $data=array();

            $data['y']=$_GPC['longitude'];

            $data['x']=$_GPC['latitude'];

            $data['caraddress']=$_GPC['search'];

            $data['name']=$_GPC['name'];

            $data['title']=$_GPC['title'];

            $data['agelimit']=$_GPC['agelimit'];

            $data['year']=$_GPC['year'];

            $data['km']=$_GPC['km'];

            $data['audit_type']=1;

            $data['username']=$_GPC['username'];

            $data['phone']=$_GPC['phone'];

            $data['brand']=$_GPC['brand'];
            $data['brand2']=intval($_GPC['brand2']);
            $data['brand3']=intval($_GPC['brand3']);

            $data['class']=$_GPC['class'];

            $data['price']=$_GPC['price'];

            $data['introduce']=$_GPC['introduce'];

            $data['carimg']=$_GPC['carimg'];

            $data['vehicletime']=$_GPC['vehicletime'];

            $data['insurance']=$_GPC['insurance'];

            $data['exhaust']=$_GPC['exhaust'];

            $data['watchcar']=$_GPC['watchcar'];

            $data['gearbox']=$_GPC['gearbox'];

            $data['uid']=0;

            $data['status']=3;

            $data['uniacid']=$uniacid;

            $data['create_time']=time();

            $data['producttime']=$_GPC['producttime'];
            $data['identify']=$_GPC['identify'];
            $data['transnum']=$_GPC['transnum'];
            $data['effluentstand']=$_GPC['effluentstand'];
            $data['carcolor'] = $_GPC['carcolor'];
            $data['cardes'] = $_GPC['cardes'];
            $data['cardestype'] = $_GPC['cardestype'];

            $res=pdo_insert('monai_market_car_detail',$data);

            if (is_array($_GPC['img_patch']))

            {

                $id = pdo_insertid();

                foreach ($_GPC['img_patch'] as $key => $value) {

                    if ($value!='') {

                        $img['img_patch']=$value;

                        $img['intro']=$_GPC['intro'][$key];

                        $img['type'] = 3;

                        $img['product_id'] = $id;

                        $img['uniacid'] = $uniacid;

                        $img['create_time'] = time();

                        $res=pdo_insert('monai_market_image',$img);

                    }

                }

            }

            if($_GPC['jixu'])

            {

                //继续添加

                $this->success('添加成功','',2);

            }

            else

            {

                $this->success('添加成功','product/index/product');

            }

        }

        include $this->template();

	}

    public function product_img()

    {

        include $this->template();

    }

    /*

    * 修改产品信息

     */

    public function product_edit()

    {

        global $_W,$_GPC;

        load()->func('tpl');

        $id=$_GPC['id'];

        $uniacid=$_W['uniacid'];

        $data=pdo_get('monai_market_car_detail',array('id'=>$id,'uniacid'=>$uniacid));
        if(empty($data['identify'])||$data['identify']=='undefined'){
            $data['identify'] = random(6,true);
        }

        $img1=pdo_getall('monai_market_image',array('product_id'=>$id,'uniacid'=>$uniacid,'type'=>3));

        /*$array = array();

        foreach ($img1 as $key => $value) {

            $array[] = $value['img_patch'];

        }*/

        $cardestype = array(
                array(
                    'id'=>1,
                    'name'=>'（A+）原版原漆',
                    'des'=>'全车没做过油漆没做过钣金 全车螺丝未动',
                ),
                array(
                    'id'=>2,
                    'name'=>'（A）精品',
                    'des'=>'全车补漆少,钣金少',
                ),
                array(
                    'id'=>3,
                    'name'=>'（B）小瑕疵',
                    'des'=>'车身有喷漆板金,有更换覆盖件,但结构件没动到',
                ),
                array(
                    'id'=>4,
                    'name'=>'（C）小事故',
                    'des'=>'ABC柱后围侧围水箱框架有钣金修复,面积较小',
                ),
                array(
                    'id'=>5,
                    'name'=>'（D）大事故',
                    'des'=>'大梁动过,ABC柱,后围侧围有切割或火烧,泡水',
                ),
                array(
                    'id'=>6,
                    'name'=>'其他',
                    'des'=>'发动机 变速箱大修或者更换',
                ),
            );


        $brand=pdo_fetchall("SELECT * FROM ".tablename('monai_market_brand')." where `uniacid`='$uniacid' AND pid=0 order by id desc");
        if(!empty($data['brand2'])){
            $info = pdo_get('monai_market_brand',array('id'=>$data['brand2'],'uniacid'=>$uniacid));
            $brand2=pdo_fetchall("SELECT * FROM ".tablename('monai_market_brand')." where `uniacid`='$uniacid' AND pid={$info['pid']} order by id desc");
        }
        if(!empty($data['brand3'])){
            $info = pdo_get('monai_market_brand',array('id'=>$data['brand3'],'uniacid'=>$uniacid));
            $brand3=pdo_fetchall("SELECT * FROM ".tablename('monai_market_brand')." where `uniacid`='$uniacid' AND pid={$info['pid']} order by id desc");
        }

        $class=pdo_fetchall("SELECT * FROM ".tablename('monai_market_class')." where `uniacid`='$uniacid' order by id desc");

        if($_W['ispost']){

            $data=array();

            $data['y']=$_GPC['longitude'];

            $data['x']=$_GPC['latitude'];

            $data['caraddress']=$_GPC['search'];

            $data['name']=$_GPC['name'];

            $data['title']=$_GPC['title'];

            $data['agelimit']=$_GPC['agelimit'];

            $data['year']=$_GPC['year'];

            $data['km']=$_GPC['km'];

            $data['audit_type']=1;

            $data['username']=$_GPC['username'];

            $data['phone']=$_GPC['phone'];

            $data['brand']=$_GPC['brand'];
            $data['brand2']=intval($_GPC['brand2']);
            $data['brand3']=intval($_GPC['brand3']);

            $data['class']=$_GPC['class'];

            $data['price']=$_GPC['price'];

            $data['introduce']=$_GPC['introduce'];

            $data['carimg']=$_GPC['carimg'];

            $data['vehicletime']=$_GPC['vehicletime'];

            $data['insurance']=$_GPC['insurance'];

            $data['exhaust']=$_GPC['exhaust'];

            $data['watchcar']=$_GPC['watchcar'];

            $data['gearbox']=$_GPC['gearbox'];

            $data['producttime']=$_GPC['producttime'];
            $data['identify']=$_GPC['identify'];
            $data['transnum']=$_GPC['transnum'];
            $data['effluentstand']=$_GPC['effluentstand'];
            $data['carcolor'] = $_GPC['carcolor'];
            $data['cardes'] = $_GPC['cardes'];
            $data['cardestype'] = $_GPC['cardestype'];

            $res=pdo_update('monai_market_car_detail',$data,array('id'=>$id));

            if (is_array($_GPC['img_patch']))

            {

                pdo_delete('monai_market_image',array('product_id'=>$id,'type'=>3,'uniacid'=>$uniacid));

                //$thumbs = $_GPC['figure_img'];

                //$thumb_url = array();

                foreach ($_GPC['img_patch'] as $key => $value) {

                    if ($value!='') {

                        $img['img_patch']=$value;

                        $img['intro']=$_GPC['intro'][$key];

                        $img['type'] = 3;

                        $img['product_id'] = $id;

                        $img['uniacid'] = $uniacid;

                        $img['create_time'] = time();

                        $res=pdo_insert('monai_market_image',$img);

                    }

                }

            }

            $this->success('修改成功','product/index/product');

        }



        include $this->template();

    }

    /*

    * 删除产品

     */

    public function product_del()

    {

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        if($_GPC['id']){

            $id=$_GPC['id'];
            $data['delete_time']=time();
            //$data=pdo_delete('monai_market_car_detail',array('id'=>$id,'uniacid'=>$uniacid));
            $data = pdo_update('monai_market_car_detail',$data,array('id'=>$id));
            //pdo_delete('monai_market_image',array('product_id'=>$id,'type'=>3,'uniacid'=>$uniacid));
            if ($data) {
                echo  1;
            }else
            {
                echo 2;
            }
        }

    }
    /*
    * 浏览量增加
     */
    public function product_liulan()
    {
        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        if($_GPC['id']){

            $id=$_GPC['id'];
            $text=$_GPC['text'];
            $data1=pdo_get('monai_market_car_detail',array('id'=>$id,'uniacid'=>$uniacid));
            $data['browse']=$data1['browse']+$text;
            //$data=pdo_delete('monai_market_car_detail',array('id'=>$id,'uniacid'=>$uniacid));
            pdo_update('monai_market_car_detail',$data,array('id'=>$id));
            //pdo_delete('monai_market_image',array('product_id'=>$id,'type'=>3,'uniacid'=>$uniacid));
            if ($data) {
                echo  1;
            }else
            {
                echo 2;
            }
        }
    }

    /*

    * 置顶

     */

    public function product_top()

    {

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        if($_GPC['id']){

            $result=pdo_fetch("SELECT * FROM ".tablename('monai_market_info')." where `uniacid`='$uniacid' order by id desc limit 1");
						//var_dump($result);die;

            $id=$_GPC['id'];

            $data['top_time']=time();

            $data['expiry_time']=(7*86400)+time();

            $res=pdo_update('monai_market_car_detail',$data,array('id'=>$id));

            if ($res) {
                echo  1;
            }else
            {
                echo 2;
            }

        }

    }
    /*
    * 取消置顶
     */
    public function product_esctop()
    {
        global $_W,$_GPC;
        $uniacid=$_W['uniacid'];
        if($_GPC['id']){
            $id=$_GPC['id'];
            //$data['top_time']=time();
            $data['expiry_time']=time();
            $res=pdo_update('monai_market_car_detail',$data,array('id'=>$id));
            if ($res) {
                echo  1;
            }else
            {
                echo 2;
            }
        }
    }

    /*

    * 产品

     */

    public function audit()

    {

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        $name=$_GPC['name'];

        $status=$_GPC['status'];

        $where='';

        if($name!='')

        {

            $where = " and a.name like'%".$name."%'";

        }

        if($status!='')

        {

            $where .= " and a.audit_type=".$status." " ;

        }

        $pindex = max(1, intval($_GPC['page']));

        $psize = 8;

        $list=pdo_fetchall("select a.*,b.name as type_name from ".tablename('monai_market_car_detail')."  as a Left Join" .tablename('monai_market_brand')." as b On a.brand=b.id where a.`uniacid`='$uniacid' $where order by a.id desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");



        $total = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_car_detail')."  as a Left Join" .tablename('monai_market_brand')." as b On a.brand=b.id where a.`uniacid`='$uniacid' $where order by a.id desc");

        $pager = pagination2($total, $pindex, $psize);

        $i=($pindex - 1) * $psize+1;

        include $this->template();

    }

    //审核内页

    public function audit_product()

    {

        global $_W,$_GPC;

        load()->func('tpl');

        $id=$_GPC['id'];

        $uniacid=$_W['uniacid'];

        // print_r($id);echo "1";
        // print_r($uniacid);

        // $data=pdo_get('monai_market_car_detail',array('id'=>$id,'uniacid'=>$uniacid));

        $data=pdo_fetchall("select * from ims_monai_market_car_detail right outer join ims_monai_market_brand on ims_monai_market_car_detail.brand = ims_monai_market_brand.id where ims_monai_market_car_detail.uniacid =$uniacid and ims_monai_market_car_detail.id = $id");



        $img1=pdo_getall('monai_market_image',array('product_id'=>$id,'uniacid'=>$uniacid,'type'=>3));

        //var_dump($img1);exit;

        if($_W['ispost']){

            $data=array();

            $data['audit_type']=$_GPC['audit_type'];

            $data['failure']=$_GPC['failure'];

            if ($_GPC['audit_type']==1) {

                $data['status']=2;

            }

            $res=pdo_update('monai_market_car_detail',$data,array('id'=>$id));

            $this->success('处理成功','product/index/audit');

        }

        include $this->template();

    }
    //查看会员信息
    public function product_member()

    {

        global $_W,$_GPC;

        load()->func('tpl');

        $uid=$_GPC['uid'];

        $uniacid=$_W['uniacid'];

        $data=pdo_get('monai_market_member',array('uid'=>$uid,'uniacid'=>$uniacid));

        //var_dump($img1);exit;

        if($_W['ispost']){

            $data=array();
            //var_dump($_GPC['status']);exit;
            $data['status']=$_GPC['status'];

            $res=pdo_update('monai_market_member',$data,array('uid'=>$uid));

            $this->success('处理成功','product/index/product');

        }

        include $this->template();

    }
    //同意审核

    public function product_yes()

    {

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        $id=$_GPC['id'];

        $data['audit_type'] = 1;

        pdo_update('monai_market_car_detail',$data,array('id'=>$id,'uniacid'=>$uniacid));

        $this->success('处理成功','',2);

    }

    //驳回

    public function product_no()

    {

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        $id=$_GPC['id'];

        $data['audit_type'] = 2;

        pdo_update('monai_market_car_detail',$data,array('id'=>$id,'uniacid'=>$uniacid));

        $this->success('处理成功','',2);

    }

     //修改推荐状态

    public function appoint_ajax_recom()
    {

        global $_W,$_GPC;

        load()->func('tpl');

        $id=$_GPC['id'];

        $uniacid=$_W['uniacid'];

        $data=pdo_get('monai_market_car_detail',array('id'=>$id,'uniacid'=>$uniacid));

        $sale = $data['status'] == '3'? '4' : '3';

        $array = array('status'=>$sale);

        $res=pdo_update('monai_market_car_detail',$array,array('id'=>$id,'uniacid'=>$uniacid));

        echo $sale;

        exit;

    }

    public function appoint_ajax_sold(){
        global $_W,$_GPC;
        $id=$_GPC['id'];
        $uniacid=$_W['uniacid'];
        $data = pdo_get('monai_market_car_detail',array('id'=>$id,'uniacid'=>$uniacid));
        //$sale = $data['status'] == '3'? '4' : '3';

        $issold = $data['issold'] == '1'? '0' : '1';
        $array = array('status'=>'4','issold'=>$issold);
        $res=pdo_update('monai_market_car_detail',$array,array('id'=>$id,'uniacid'=>$uniacid));
        echo $issold;
        exit;
    }
    public function appoint_ajax_vip(){
        global $_W,$_GPC;
        $id=$_GPC['id'];
        $uniacid=$_W['uniacid'];
        $data = pdo_get('monai_market_car_detail',array('id'=>$id,'uniacid'=>$uniacid));

        $isvip = $data['isvip'] == '1'? '0' : '1';
        $array = array('isvip'=>$isvip);
        $res=pdo_update('monai_market_car_detail',$array,array('id'=>$id,'uniacid'=>$uniacid));
        echo $isvip;
        exit;
    }
    //精品
    public function appoint_ajax_fine(){
        global $_W,$_GPC;
        $id = $_GPC['id'];
        $uniacid = $_W['uniacid'];
        $data = pdo_get('monai_market_car_detail',array('id'=>$id,'uniacid'=>$uniacid));

        $isfine = $data['isfine'] == '1'? '0' : '1';
        $array = array('isfine'=>$isfine);
        $res=pdo_update('monai_market_car_detail',$array,array('id'=>$id,'uniacid'=>$uniacid));
        echo $isfine;
        exit;
    }

    public function brandlist(){
        global $_W,$_GPC;

        $uniacid = $_W['uniacid'];
        $pid = intval($_GPC['pid']);

        $list = pdo_getall('monai_market_brand',array('uniacid'=>$uniacid,'pid'=>$pid));


        echo json_encode(array('status'=>0,'data'=>$list));
        exit;
    }

    /*

     * 去除富文本内的字体

     * 2018年1月19日13:22:00

     */

    public function fwb_del_font($str)

    {

        $str = htmlspecialchars_decode($str);

        $re = preg_replace("/font-family:.*;/i",'',$str);

        $re = preg_replace("/font-family:.*\"/i",'"',$re);

        return $re;

    }

     /*

    * 获取首字母

     */

    public function getFirstCharter($str)

    {

        if (empty($str)) {

            return '';

        }

        $fchar = ord($str{0});

        if ($fchar >= ord('A') && $fchar <= ord('z')) return strtoupper($str{0});

        $s1 = iconv('UTF-8', 'gb2312', $str);

        $s2 = iconv('gb2312', 'UTF-8', $s1);

        $s = $s2 == $str ? $s1 : $str;

        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;

        if ($asc >= -20319 && $asc <= -20284) return 'A';

        if ($asc >= -20283 && $asc <= -19776) return 'B';

        if ($asc >= -19775 && $asc <= -19219) return 'C';

        if ($asc >= -19218 && $asc <= -18711) return 'D';

        if ($asc >= -18710 && $asc <= -18527) return 'E';

        if ($asc >= -18526 && $asc <= -18240) return 'F';

        if ($asc >= -18239 && $asc <= -17923) return 'G';

        if ($asc >= -17922 && $asc <= -17418) return 'H';

        if ($asc >= -17417 && $asc <= -16475) return 'J';

        if ($asc >= -16474 && $asc <= -16213) return 'K';

        if ($asc >= -16212 && $asc <= -15641) return 'L';

        if ($asc >= -15640 && $asc <= -15166) return 'M';

        if ($asc >= -15165 && $asc <= -14923) return 'N';

        if ($asc >= -14922 && $asc <= -14915) return 'O';

        if ($asc >= -14914 && $asc <= -14631) return 'P';

        if ($asc >= -14630 && $asc <= -14150) return 'Q';

        if ($asc >= -14149 && $asc <= -14091) return 'R';

        if ($asc >= -14090 && $asc <= -13319) return 'S';

        if ($asc >= -13318 && $asc <= -12839) return 'T';

        if ($asc >= -12838 && $asc <= -12557) return 'W';

        if ($asc >= -12556 && $asc <= -11848) return 'X';

        if ($asc >= -11847 && $asc <= -11056) return 'Y';

        if ($asc >= -11055 && $asc <= -10247) return 'Z';

        return null;

    }

    public function province_save() {
        global $_W;

        $map_key = pdo_get('monai_market_info', array('uniacid'=>$_W['uniacid']), array('map_key'));

        if ($map_key['map_key']) {
            $page_size = 100;
            for ($i=0; $i<9999; $i++) {
                $sql = "select `id`,`y`,`x` from " . tablename('monai_market_car_detail') . " where `uniacid`={$_W['uniacid']} limit " . ($i * $page_size) . ",{$page_size}";
                $arr = pdo_fetchall($sql);

                if ($arr) {
                    foreach ($arr as $v) {
                        if ($v['y'] && $v['x']) {
                            $res = file_get_contents("http://apis.map.qq.com/ws/geocoder/v1/?location={$v['x']},{$v['y']}&key={$map_key['map_key']}");
                            $arr = json_decode($res, true);

                            $data = array(
                                'province' => $arr['result']['address_component']['province'],
                                'city' => $arr['result']['address_component']['city'],
                                'district' => $arr['result']['address_component']['district'],
                            );
                            pdo_update('monai_market_car_detail', $data, array('id'=>$v['id']));

                            sleep(1);
                        }
                    }
                } else {
                    break;
                }
            }
        }

        include $this->template();
    }


}

?>
