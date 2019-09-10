<?php

if (!(defined('IN_IA'))) 

{

	exit('Access Denied');

}

class Web_Money extends Web_Base

{



    /*

    * 资金列表

     */

    public function index() 

	{

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];
        $where='';
        $pay_time = $_GPC['pay_time'];
        if(empty($pay_time))
        {
            $pay_time = ['start'=>'2018-1-01','end'=>date('Y-m-d',time())];
        }
        else
        {
            $add_time_end = strtotime($pay_time['end'])+86399;
            $where .= " and pay_time > ".strtotime($pay_time['start'])." and pay_time < ".$add_time_end;
        }

        $pindex = max(1, intval($_GPC['page']));

        $psize = 8;

        $list=pdo_fetchall("select * from ".tablename('monai_market_finance')."  where `uniacid`='$uniacid' and status = 1 and pay_money > 0 $where order by pay_time desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");

        

        $total = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_finance')."  where `uniacid`='$uniacid' and status = 1 $where");

        foreach ($list as $k => $v)
        {
            $user_info = pdo_fetch("select * from ".tablename('monai_market_member')."  where `uniacid`='$uniacid' and uid=".$v['uid']);

            $list[$k]['nickname'] = $user_info['nickname'];
            $list[$k]['user_id'] = $user_info['user_id'];

            $car_info = pdo_fetch("select * from ".tablename('monai_market_car_detail')."  where id =".$v['pay_by_id']);

            $list[$k]['title'] = $car_info['name'];

            $list[$k]['carimg'] = $car_info['carimg'];
        }

        $pager = pagination2($total, $pindex, $psize);

        $i=($pindex - 1) * $psize+1;

        include $this->template();

	}

}

?>