<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/20
 * Time: 15:12
 */

class Web_Index extends Web_Base
{
 	public function index(){
 		global $_W,$_GPC;
        $uniacid=$_W['uniacid'];
        load()->func('tpl');
		$car_detail = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_car_detail')." where `uniacid`='$uniacid' and `uid`>0 and `delete_time`=0");
		$feedback = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_feedback')." where `uniacid`='$uniacid' and `feedback_type`=2 and `status`=0");
		$finance = pdo_fetchcolumn("select sum(pay_money) from ".tablename('monai_market_finance')." where `uniacid`='$uniacid' and `status`=1");
		//$finance == $finance>0 ?  $finance : 0;
		$member = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_member')." where `uniacid`='$uniacid'");
		//var_dump($finance);exit;
		include $this->template();
 	}
}