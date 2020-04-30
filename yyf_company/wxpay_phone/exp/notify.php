<?php
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
define('IN_LCSHOP', true);
$userid=@$_COOKIE["userid"];
require_once dirname(__FILE__) ."/../../../include/common.inc.php";
require_once dirname(__FILE__) ."/../../../pay/wxpay_phone/lib/WxPay.Api.php";
require_once dirname(__FILE__) .'/../../../pay/wxpay_phone/lib/WxPay.Notify.php';
require_once dirname(__FILE__) .'/../../../pay/wxpay_phone/exp/log.php';
//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

//回调结果返回一个data data里面有[订单编号] 去数据库里面查询 修改
function updateOrder ($out_trade_no,$trade_no){
	// file_put_contents('as.txt',$out_trade_no);
	//1.先查询订单表
	// print_r(ss);die;
	global $dosql;
	// $out_trade_no = $data["transaction_id"];
		$posttime = time();
		$bond = $dosql->GetOne("SELECT * FROM `#@__qiugouorder` WHERE orderlistnum =  ".$out_trade_no);
		// $bond = 'ss';
		if($bond){
			//更改状态
			$bond_sql = "UPDATE `#@__qiugouorder` set order_status = '1' where orderlistnum = ".$out_trade_no;
			if($dosql->ExecNoneQuery($bond_sql)){
				//更改log表的状态
				$dosql->ExecNoneQuery("UPDATE `#@__qiugoulog` set status = '1' where orderlistnum = ".$out_trade_no);
				//插入买家log
				$shopid = $dosql->GetOne("SELECT mobile FROM `#@__member` WHERE id = ".$bond['shop_id']);
				$content = "卖家".$shopid['mobile']."已向您支付保障金";
				$dosql->ExecNoneQuery("INSERT INTO `#@__mailog`(`content`,`shop_phone`,`posttime`,`userid`) VALUES('$content','".$shopid['mobile']."','$posttime','".$bond['userid']."') ");


			}			
		}else{

			$update_sql = "UPDATE `#@__totalorder` set checkinfo = '1' where orderlistnum = ".$out_trade_no;
			file_put_contents('update_sql.txt',$update_sql);
			if($dosql->ExecNoneQuery($update_sql)){


				$t_sql = "SELECT * FROM `#@__totalorder` where `orderlistnum`='$out_trade_no' ";
				$dosql->Execute($t_sql);
				while($row = $dosql->GetArray())
					{
						if($row)
						{
							$rows[] = $row;
						}
					}
				foreach ($rows as $k => $v) {
					if($v['is_urgent'] == '1'){
						$in_sql = "INSERT INTO `#@__carorder` (orderlistnum,userid,weight,goodsorderlistnum,is_urgent,posttime) VALUES ('$out_trade_no','".$v['userid']."','".$v['weight']."','".$v['goodsorderlistnum']."','1','$posttime')";
					}else{
						$in_sql = "INSERT INTO `#@__carorder` (orderlistnum,userid,weight,goodsorderlistnum,is_urgent,posttime) VALUES ('$out_trade_no','".$v['userid']."','".$v['weight']."','".$v['goodsorderlistnum']."','0','$posttime')";
					}
						$dosql->ExecNoneQuery($in_sql);
					
				}
				//该订单的金额
				$order_info = $dosql->GetOne("SELECT * from `#@__totalorder` WHERE orderlistnum = ".$out_trade_no);
				//更改用户的托盘积分
				$tuo_expval = $dosql->GetOne("SELECT tuo_expval FROM `#@__member` WHERE id  =".$order_info['userid']);
				$tuo_expvals = (int)$order_info['ordermount'] + $tuo_expval['tuo_expval'];
				$dosql->ExecNoneQuery("UPDATE `#@__member` set tuo_expval = ".$tuo_expvals." WHERE id = ".$order_info['userid']);

				//插入积分日志
				$dosql->ExecNoneQuery("INSERT INTO `#@__tuoexpval` (type,userid,content,expval,posttime) VALUES ('1','".$order_info['userid']."','成功付款,获得积分','".$order_info['ordermount']."','$posttime')");
			}
		}
			
	// $r=$dosql->getone("select id from #@__goodsorder where  orderlistnum='{$out_trade_no}' and checkinfo=0 "); 
	// if($r){


	// 	if(is_array($r)){
	// 		$sql="UPDATE `#@__goodsorder` SET checkinfo='2', paynum='$trade_no' WHERE  orderlistnum='{$out_trade_no}' and checkinfo=0";
	// 		// 商品出售排行在此处修改
	// 		if($dosql->ExecNoneQuery($sql))
	// 		{
	// 			// @$dosql->ExecNoneQuery("UPDATE `#@__member_point` SET status=1 WHERE o_id='{$r['id']}' and status=0");
	// 			if($cfg_delhouse=='1')//用户设为待付款减库存
	// 			{
	// 				/*待付款减库存*/
	// 				$sql="SELECT `gid`,`num`,`norm` FROM `#@__goodsshopcart` 
	// 				WHERE `gorderlistnum`='{$out_trade_no}' AND `Status`='order'";
	// 				$row=$dosql->GETALL($sql);
	// 				// var_dump($row);die;
	// 				foreach($row as $v)
	// 				{
	// 					$g_num = $dosql->getone("SELECT housenum FROM `#@__goods` WHERE id = ".$v['gid']);
	// 					$sql="UPDATE `#@__goodsattr` SET `housenum`={$g_num['housenum']}-{$v['num']}
	// 						WHERE `goodsid`='{$v['gid']}'" ;
	// 					$dosql->execnonequery($sql);
						
	// 						$sqls="UPDATE `#@__goods` SET `housenum`={$g_num['housenum']}-{$v['num']}
	// 						WHERE `id`='{$v['gid']}'" ;
	// 						$dosql->execnonequery($sqls);
						
	// 				}
	// 			}
	// 		}	
	// 	}
	// }

}
class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{

			$out_trade_no = $result['out_trade_no'];
			$trade_no = $result['trade_no'];
			updateOrder($out_trade_no,$trade_no);
			
			$nowRes = file_get_contents("result.txt");
			$nowRes .= PHP_EOL .var_export($result,true);
			file_put_contents("result.txt", $nowRes);
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		//
		return true;
	}
}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
