<?php

if (!(defined('IN_IA'))) 

{

	exit('Access Denied');

}

class Web_Feedback extends Web_Base

{

    public function index() 

    {

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        $pindex = max(1, intval($_GPC['page']));

        $psize = 8;

        $list=pdo_fetchall("select a.*,b.name,b.km,b.username,b.phone,b.carimg,b.price from ".tablename('monai_market_feedback')."  as a Left Join" .tablename('monai_market_car_detail')." as b On a.car_id=b.id where a.`uniacid`='$uniacid' and a.`feedback_type`=2 and a.`status`='0' $where order by a.id desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");

        

        $total = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_feedback')."  as a Left Join" .tablename('monai_market_car_detail')." as b On a.car_id=b.id where a.`uniacid`='$uniacid' and a.`feedback_type`=2 and a.`status`='0' $where order by a.id desc");

        $pager = pagination2($total, $pindex, $psize);

        $i = ($pindex - 1) * $psize+1;

        include $this->template();

    }

    public function feedback() 

    {

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        $id = $_GPC['id'];

        $car_id = $_GPC['car_id'];

        //var_dump($car_id);exit;

        $img1=pdo_getall('monai_market_image',array('product_id'=>$car_id,'uniacid'=>$uniacid));

        $data=pdo_get('monai_market_car_detail',array('id'=>$car_id,'uniacid'=>$uniacid));

        $feedback=pdo_get('monai_market_feedback',array('id'=>$id,'uniacid'=>$uniacid));

        load()->func('tpl');

        if($_W['ispost']){

            $data=array();

            $data['status']=$_GPC['status'];

            $res=pdo_update('monai_market_feedback',$data,array('id'=>$id));

            if($_GPC['status']==1)

            {

                $del['delete_time']=time();

                pdo_update('monai_market_car_detail',$del,array('id'=>$car_id));

            }

            $this->success('处理成功','feedback/index');

        }

        include $this->template();

    }
    /*
    * 自营车辆留言
     */
    public function words()
    {
        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        $pindex = max(1, intval($_GPC['page']));

        $psize = 8;

        $list=pdo_fetchall("select a.*,b.name,b.km,b.username,b.phone,b.carimg,b.price from ".tablename('monai_market_feedback')."  as a Left Join" .tablename('monai_market_car_detail')." as b On a.car_id=b.id where a.`uniacid`='$uniacid' and a.`feedback_type`=1 and a.`status`='0' and a.`car_uid`!=0 $where order by a.id desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
        var_dump(end(pdo_debug(false)));

        

        $total = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_feedback')."  as a Left Join" .tablename('monai_market_car_detail')." as b On a.car_id=b.id where a.`uniacid`='$uniacid' and a.`feedback_type`=1 and a.`status`='0' and a.`car_uid`!=0 $where order by a.id desc");

        $pager = pagination2($total, $pindex, $psize);

        $i = ($pindex - 1) * $psize+1;

        include $this->template();
    }

}

?>