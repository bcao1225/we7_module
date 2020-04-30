<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

require_once "../lib/WxPay.Api.php";
require_once '../lib/WxPay.Notify.php';
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

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
		Log::DEBUG("unifiedorder:" . json_encode($result));
		return $result;
	}
	
	public function NotifyProcess($data, &$msg)
	{
		//echo "处理回调";
		Log::DEBUG("call back:" . json_encode($data));
		
		if(!array_key_exists("openid", $data) ||
			!array_key_exists("product_id", $data))
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
		
		
		
		define('IN_LCSHOP', true);
		require_once(dirname(__FILE__).'/../../../../include/config.php');
		
		//商户订单号
		Log::DEBUG("zhangdan:" . json_encode($data));
	
		$trade_no = explode("|",$data['attach']);
		
	
		//交易号
	
		$out_trade_no = $data["transaction_id"];
	

		$r=$dosql->getone("select id from #@__goodsorder where orderlistnum='".$trade_no[0]."' and checkinfo=0 "); 
		if(is_array($r)){

			$sql="UPDATE `#@__goodsorder` SET checkinfo='1', paynum='$out_trade_no' WHERE  orderlistnum='".$trade_no[0]."' and checkinfo=0";
			// 商品出售排行在此处修改
			if($dosql->ExecNoneQuery($sql))
			{

				if($cfg_delhouse==1)//用户设为待发货减库存
				{

					/*待发货减库存*/
					$sql="SELECT `gid`,`num`,`norm` FROM `#@__goodsshopcart` 
					WHERE `gorderlistnum`='{$out_trade_no}' AND `Status`='order'";
					$row=$dosql->GETALL($sql);
					foreach($row as $v)
					{
						$sql="UPDATE `#@__goodsattr` SET `housenum`=housenum-{$v['num']}
							WHERE `goodsid`='{$v['gid']}' AND `attrname`='{$v['norm']}'" ;
						$dosql->execnonequery($sql);
						if($v['norm']=="")//再更新商品表的库存字段
						{
							$sql="UPDATE `#@__goods` SET `housenum`=housenum-{$v['num']}
							WHERE `id`='{$v['gid']}'" ;
							$dosql->execnonequery($sql);
						}
					}
				}
			}
		}
		//===============================================================================================================================更新支付状态 end			
		
		
		return true;
	}
}

Log::DEBUG("begin notify!");
$notify = new NativeNotifyCallBack();
$notify->Handle(true);
