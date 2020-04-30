<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

require_once "../lib/WxPay.Api.php";
require_once '../lib/WxPay.Notify.php';
require_once 'log.php';

//初始化日志
//$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
//$log = Log::Init($logHandler, 15);

class NativeNotifyCallBack extends WxPayNotify
{
	public function unifiedorder($openId, $product_id)
	{
		//统一下单
		$input = new WxPayUnifiedOrder();
		$input->SetBody("test");
		$input->SetAttach("test");
		$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
		$input->SetTotal_fee("1");
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
		$input->SetGoods_tag("test");
		$input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");
		$input->SetTrade_type("NATIVE");
		$input->SetOpenid($openId);
		$input->SetProduct_id($product_id);
		$result = WxPayApi::unifiedOrder($input);
		//Log::DEBUG("unifiedorder:" . json_encode($result));
		return $result;
	}
	
	public function NotifyProcess($data, &$msg)
	{
		//echo "处理回调";
		
		
		if(!array_key_exists("openid", $data))
		{
			$msg = "回调数据异常";
			return false;
		}
		
		
		$openid = $data["openid"];
		$product_id = $data["product_id"];
		//统一下单
		$result = $this->unifiedorder($openid, $product_id);
		if(!array_key_exists("appid", $result) ||
			 !array_key_exists("mch_id", $result) ||
			 !array_key_exists("prepay_id", $result))
		{
		 	$msg = "统一下单失败";
		 	return false;
		 }
		
		$this->SetData("appid", $result["appid"]);
		$this->SetData("mch_id", $result["mch_id"]);
		$this->SetData("nonce_str", WxPayApi::getNonceStr());
		$this->SetData("prepay_id", $result["prepay_id"]);
		$this->SetData("result_code", "SUCCESS");
		$this->SetData("err_code_des", "OK");
		
		
		//===============================================================================================================================更新支付状态 start	
		
		
		

		
		
//Log::DEBUG("call back00:" . json_encode($data));
	$trade_no = explode("|",$data['attach']);
	$out_trade_no = $data["transaction_id"];


	//$trade_no[0] = "145671613885";
	//$out_trade_no = "0000000";


		define('IN_LCSHOP', true);
		require_once('../../../conn.inc.php');
		require_once('../../../config.php');
		
		$conn=mysql_connect($db_host,$db_user,$db_pwd) or die("error connecting"); 
		mysql_query("set names '".$db_charset."'"); 
		mysql_select_db($db_name);
		$sql = "select count(id) as ids from ".$db_tablepre."goodsorder where orderlistnum='".$trade_no[0]."' and checkinfo=0";
		$rs = mysql_query($sql,$conn);
		if($rs)
		{
			$sql ="UPDATE `".$db_tablepre."goodsorder` SET checkinfo='1', paynum='$out_trade_no' WHERE  orderlistnum='".$trade_no[0]."' and checkinfo=0"; //SQL语句

			if(mysql_query($sql))
			{

				if($cfg_delhouse==1)//用户设为待发货减库存
				{

					/*待发货减库存*/
					$sql="SELECT `gid`,`num`,`norm` FROM `".$db_tablepre."goodsshopcart` 
					WHERE `gorderlistnum`='{$out_trade_no}' AND `Status`='order'";
					$rs=mysql_query($sql);
					while($v = mysql_fetch_array($rs)) 
					{
						$sql="UPDATE `".$db_tablepre."goodsattr` SET `housenum`=housenum-{$v['num']}
							WHERE `goodsid`='{$v['gid']}' AND `attrname`='{$v['norm']}'" ;
						mysql_query($sql);
						if($v['norm']=="")//再更新商品表的库存字段
						{
							$sql="UPDATE `".$db_tablepre."goods` SET `housenum`=housenum-{$v['num']}
							WHERE `id`='{$v['gid']}'" ;
							mysql_query($sql);
						}
					}
				}
			}

		}

	
		//===============================================================================================================================更新支付状态 end			
		
		
		return true;
	}
}

//Log::DEBUG("begin notify!");
$notify = new NativeNotifyCallBack();

$a = $notify->Handle(true);

file_put_contents('b.txt', $a);

