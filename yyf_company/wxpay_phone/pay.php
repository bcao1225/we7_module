<?php
define('IN_LCSHOP', true);
require_once(dirname(__FILE__).'/../include/config_m.php');
require_once(dirname(__FILE__).'/../include/checkmember.php');
include_once('bottom.php');
include_once(LCSHOP_LANG . '/pay.php');

$act 	  = isset($act) ? $act : '';
$state 	  = isset($state) ? $state : '';
// $_COOKIE['userid'] = 255;
//$smarty->assign("title", $_LANG['title']['pay']);
if (@$olnum != '')
{	
	if($act == 'setpay')
	{
		$smarty->assign("title", "订单提交成功");
		$r = $dosql->getone("SELECT * FROM `#@__goodsorder` WHERE `orderlistnum`='".$olnum."' and userid=".$_COOKIE['userid']);

		if(is_array($r))
		{
			
			$dosql->execnonequery("UPDATE `#@__goodsorder` SET `paymode`='".$paytype."',`bankId`='".$bankId."' WHERE `orderlistnum`='".$olnum."' and userid=".$_COOKIE['userid']);
			$row = $dosql->getone("SELECT * FROM `#@__payment` WHERE id='{$paytype}'");
		

			
			//支付宝
			if($row['code']=='alipay')
			{
				if(isset($row['config']) && !empty($row['config']))
				{
					$pay_config['WIDseller_config'] = string2array($row['config']);
				}
				else
				{
					$file = dirname(__FILE__).'/../include/module/payment/'.$r['code'].'.php';
					if (file_exists($file))
					{
						include_once($file);
						$pay_config['WIDseller_config'] = $module[0]['config'];
					}
				}
				$WIDsubject = '';
				$dosql->execute("SELECT gtitle FROM `#@__goodsshopcart` WHERE gorderlistnum='".$olnum."'");
				while($rs=$dosql->getarray())
				{
					$WIDsubject .= $rs['gtitle']."|";
				}
				$pay_config['WIDout_trade_no'] = $r['orderlistnum'];	//商户网站订单系统中唯一订单号
				$pay_config['WIDsubject'] 	   = $WIDsubject;	//订单名称
				$pay_config['WIDtotal_fee']    = $r['orderamount']+$r['cost'];//付款金额

				$pay_config['WIDbody'] 		   = '';//订单描述
				$pay_config['WIDshow_url']     = '';//商品展示地址
				
				$file = dirname(__FILE__).'/../data/payment/phone_alipay/alipayapi.php';
				if (file_exists($file))
				{
					include_once($file);
					$smarty->assign('html_text', $html_text); 
					$smarty->display('go_pay.html');
				}
				else
				{
					$smarty->display('404.html');
					exit();
				}
			}
			//财付通
			elseif($row['code']=='tenpay')
			{
				if(isset($row['config']) && !empty($row['config']))
				{
					$pay_config['config'] = string2array($row['config']);
				}
				else
				{
					$file = dirname(__FILE__).'/../include/module/payment/'.$r['code'].'.php';
					if (file_exists($file))
					{
						include_once($file);
						$pay_config['config'] = $module[0]['config'];
					}
				}
				$product_name = '';
				$dosql->execute("SELECT gtitle FROM `#@__goodsshopcart` WHERE gorderlistnum='".$olnum."'");
				while($rs=$dosql->getarray())
				{
					$product_name .= $rs['gtitle']."|";
				}
				
				
				$pay_config['order_no'] 		= $r['orderlistnum'];	//商户网站订单系统中唯一订单号
				$pay_config['product_name']		= restrlen($product_name,10);		//订单名称
				$pay_config['order_price']		= $r['orderamount']+$r['cost'];//付款金额
				$pay_config['trade_mode']		= 1;//支付方式 1即时到帐 2中介担保3后台选择
				$pay_config['remarkexplain']	= '';//简要说明
									   
				$file = dirname(__FILE__).'/../data/payment/tenpay/tenpay.php';
				if (file_exists($file))
				{
					include_once($file);
					$smarty->assign('html_text', $html_text); 
					$smarty->display('go_pay.html');
				}
				else
				{
					$smarty->display('404.html');
					exit();
				}
			}
			//微信扫码支付
			elseif ($row['code'] == 'wxpay') {
				if(isset($_COOKIE['userid'])&&!empty($_COOKIE['userid'])){
			     	
			     	 $r_user = $dosql->GetOne("SELECT `wx_info`,`user_money` FROM `#@__member` WHERE id=".$_COOKIE['userid']);
					 $wxuser = json_decode($r_user['wx_info'],true);
					 // print_r($r_user['user_money']);die;
					
			         }
				
					if (isset($row['config']) && !empty($row['config'])) {

						$pay_config['config'] = string2array($row['config']);
					} else {
						$file = dirname(__FILE__) . '/include/module/payment/' . $r['code'] . '.php';

					if (file_exists($file)) {
							include_once ($file);
							$pay_config['config'] = $module[0]['config'];
						}
				}
				$product_name = '';
				$dosql -> execute("SELECT gtitle FROM `#@__goodsshopcart` WHERE gorderlistnum='" . $olnum . "'");
				while ($rs = $dosql -> getarray()) {
					$product_name .= $rs['gtitle'] . "|";
				}

				// preg_match("/(\/[\w]+\/)*/i",$_SERVER['REQUEST_URI'],$array);
				// $date_uri=$array[0] ? $array[0]:"/"; 
				// $rtnUrl="http://".$_SERVER['HTTP_HOST'];
				// $apply=$dosql->getone("select card_shenhe from #@__member where id = ".$r['userid']); 
			 //    if($apply['card_shenhe'] != 3){
			 //        $rtnUrl.="/m/member.php?act=identitycard&orderid={$r['id']}";
			 //    }else{
			 //        $rtnUrl.="/m/member.php?act=ordercontent&orderid={$r['id']}";
			 //    } 
				$pay_config['openid'] = $wxuser['openid'];
				$pay_config['order_no'] = $r['orderlistnum'];
				//商户网站订单系统中唯一订单号
				$pay_config['product_name'] = restrlen($product_name, 10);
				//订单名称
				$pay_config['order_price'] = $r['orderamount']+$r['cost'];
				//付款金额
				$pay_config['order_id'] =  $r['id'];

				$file = dirname(__FILE__) . '/../data/payment/wxpay_phone/exp/jsapi.php';
				
				if (file_exists($file)) {
					include_once ($file);
					exit();
				} else {
					$smarty -> display('404.html');
					exit();
				}
				
				
			}
			//IPS
			elseif($row['code']=='ips')
			{
				if(isset($row['config']) && !empty($row['config']))
				{
					$pay_config['config'] = string2array($row['config']);
				}
				else
				{
					$file = dirname(__FILE__).'/../include/module/payment/'.$r['code'].'.php';
					if (file_exists($file))
					{
						include_once($file);
						$pay_config['config'] = $module[0]['config'];
					}
				}
				$product_name = '';
				$dosql->execute("SELECT gtitle FROM `#@__goodsshopcart` WHERE gorderlistnum='".$olnum."'");
				while($rs=$dosql->getarray())
				{
					$product_name .= $rs['gtitle']."|";
				}
				//商户交易日期
				$BillDate = date('Ymd');
				//商户返回地址
				$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https://' : 'http://';
				$url .= str_ireplace('localhost', '127.0.0.1', $_SERVER['HTTP_HOST']) . $_SERVER['SCRIPT_NAME'];
				$url = str_ireplace('orderpay', 'OrderReturn', $url);

				$pay_config['test'] 			= '1';								//提交地址 1测试环境 0正式环境
				$pay_config['Mer_code'] 		= '';								//商户号
				$pay_config['Mer_key'] 			= '';								//商户证书
				$pay_config['Billno'] 			= $r['orderlistnum'];				//订单号
				$pay_config['Amount'] 			= $r['orderamount']+$r['cost'];		//金额
				$pay_config['DispAmount'] 		= $r['orderamount']+$r['cost'];		//显示金额
				$pay_config['Date'] 			= $BillDate;						//日期
				$pay_config['Currency_Type'] 	= 'RMB';							//支付币种 RMB人民币
				$pay_config['Gateway_Type'] 	= '01';								//支付方式 01借记卡
				$pay_config['Lang'] 			= 'GB';								//GB中文
				$pay_config['Merchanturl'] 		= $url;								//支付成功返回URl
				$pay_config['FailUrl'] 			= '';								//支付失败返回URL
				$pay_config['Attach'] 			= '';								//商户附加数据包
				$pay_config['OrderEncodeType'] 	= '5';								//订单支付加密方式 5 md5摘要
				$pay_config['RetEncodeType'] 	= '17';								//交易返回加密方式  16 md5withRsa 17 md5摘要
				$pay_config['ServerUrl'] 		= $url;								//Server to Server返回页面   
				$pay_config['Rettype'] 			= '1';								//是否提供Server返回方式 0 无Server to Server 1有Server to Server
												   
				$file = dirname(__FILE__).'/../data/payment/ips/redirect.php';
				if (file_exists($file))
				{
					include_once($file);					
					$smarty->assign('html_text', $html_text); 
					$smarty->display('go_pay.html');
				}
				else
				{
					$smarty->display('404.html');
					exit();
				}
			}
			//快钱
			elseif($row['code']=='kuaiqian')
			{
				if(isset($row['config']) && !empty($row['config']))
				{
					$pay_config['config'] = string2array($row['config']);
				}
				else
				{
					$file = dirname(__FILE__).'/../include/module/payment/'.$r['code'].'.php';
					if (file_exists($file))
					{
						include_once($file);
						$pay_config['config'] = $module[0]['config'];
					}
				}
				$pay_config['orderId'] 		= $r['orderlistnum'];				//商户网站订单系统中唯一订单号
				$pay_config['rec_name']		= $r['rec_name'];					//付款人
				$pay_config['orderAmount']	= $r['orderamount']+$r['cost'];		//付款金额


				
				if(isset($bankId))
				{
					$pay_config['payType']	= 10;			//支付方式
					$pay_config['bankId']	= $bankId;		//银行代码
				}
				else
				{
					$pay_config['payType']	= 12;			//支付方式
					$pay_config['bankId']	= '';			//银行代码
				}
									   
				$file = dirname(__FILE__).'/../data/payment/kuaiqian/send.php';
				if (file_exists($file))
				{
					include_once($file);					
					$smarty->assign('html_text', $html_text); 
					$smarty->display('go_pay.html');
				}
				else
				{
					$smarty->display('404.html');
					exit();
				}
			}
			//银行转账汇款 or 邮储汇款 or 货到付款
			elseif($row['code']=='bank' or $row['code']=='post' or $row['code']=='cod')
			{
				$sql="UPDATE `#@__goodsorder` SET checkinfo='1' WHERE  orderlistnum='{$olnum}' and checkinfo=0";
				// 商品出售排行在此处修改
				if($dosql->ExecNoneQuery($sql))
				{

					if($cfg_delhouse==1)//用户设为待发货减库存
					{

						/*待发货减库存*/
						$sql="SELECT `gid`,`num`,`norm` FROM `#@__goodsshopcart` 
						WHERE `gorderlistnum`='{$olnum}' AND `Status`='order'";
						$row_s=$dosql->GETALL($sql);
						foreach($row_s as $v)
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
				$smarty->assign('html_text', $row['depict']); 
				$smarty->display('go_pay.html');
			}
		}
		else
		{
			$smarty->display('404.html');
			exit();
		}
		
	}
	else
	{
		$smarty->assign("title", "支付中心");
		$info = array();
		$online_bank = array();
		$info = $dosql->getall("SELECT * FROM `#@__payment` ORDER BY orderid ASC");
		foreach($info as $key => $val)
		{
			if($val['code']=='kuaiqian')
			{
				$file = dirname(__FILE__).'/include/module/payment/kuaiqian.php';
				if (file_exists($file))
				{
					$set_module = true;
					include_once($file);
					$online_bank['id'] 		= $val['id'];
					$online_bank['config']  = $module[0]['online_bank'];
				}
			}
		}
		$r = $dosql->getone("SELECT id,paymode,bankId FROM `#@__goodsorder` WHERE `orderlistnum`='".$olnum."'");
		if(isset($r['paymode']))
		{
			$id = $r['id'];
			$paymode = $r['paymode'];
			if($r['bankId']=="")
				$bankId  = 0;
			else
				$bankId  = $r['bankId'];
		}
		else
		{
			$paymode = 0;
			$bankId  = 0;
			$id  = 0;
		}
		$smarty->assign('info', $info); 
		$smarty->assign('online_bank', $online_bank); 
		$smarty->assign('paymode', $paymode);
		$smarty->assign('bankId', $bankId);
		$smarty->assign('id', $id);
		$smarty->assign("olnum", $olnum);
		$smarty->display('pay.html');
	}   

}
elseif($state == 'recharge'){
	if(isset($_COOKIE['userid'])&&!empty($_COOKIE['userid'])){
     	
     	 $r_user = $dosql->GetOne("SELECT `wx_info`,`user_money` FROM `#@__member` WHERE id=".$_COOKIE['userid']);
		 $wxuser = json_decode($r_user['wx_info'],true);
		 // print_r($r_user['user_money']);die;
		
         }
	//生成订单
	$orderamount = $_POST['recharge_money'];
	$userid = $_COOKIE['userid'];
	$orderlistnum = time()+rand(1,9999);
	$orderlistnum.=$userid;	
	$remarks = $wxuser['nickname'].'---余额充值---'.$orderamount;
	$sql = "INSERT INTO `#@__recharge` (`amount`,`orderlistnum`,`uid`,`remarks`) VALUES ('$orderamount','$orderlistnum','$userid','$remarks')";
	// print_r($sql);die;
	$result = $dosql->execnonequery($sql);
	// print_r($result);die;
	
	$id = $dosql->GetLastId();
	$product_name = "立即充值---余额";
	// print_r($id);die;
	// if (isset($row['config']) && !empty($row['config'])) {

	// 					$pay_config['config'] = string2array($row['config']);
	// 				} else {
	// 					$file = dirname(__FILE__) . '/include/module/payment/' . $r['code'] . '.php';

	// 				if (file_exists($file)) {
	// 						include_once ($file);
	// 						$pay_config['config'] = $module[0]['config'];
	// 					}
	// 			}
	// 			$product_name = '';
	// 			$dosql -> execute("SELECT gtitle FROM `#@__goodsshopcart` WHERE gorderlistnum='" . $olnum . "'");
	// 			while ($rs = $dosql -> getarray()) {
	// 				$product_name .= $rs['gtitle'] . "|";
	// 			}

				// preg_match("/(\/[\w]+\/)*/i",$_SERVER['REQUEST_URI'],$array);
				// $date_uri=$array[0] ? $array[0]:"/"; 
				// $rtnUrl="http://".$_SERVER['HTTP_HOST'];
				// $apply=$dosql->getone("select card_shenhe from #@__member where id = ".$r['userid']); 
			 //    if($apply['card_shenhe'] != 3){
			 //        $rtnUrl.="/m/member.php?act=identitycard&orderid={$r['id']}";
			 //    }else{
			 //        $rtnUrl.="/m/member.php?act=ordercontent&orderid={$r['id']}";
			 //    } 
				$pay_config['openid'] = $wxuser['openid'];
	// print_r($pay_config);die;
				//用户的openID
				
				$pay_config['order_no'] = $orderlistnum;

				//商户网站订单系统中唯一订单号
				$pay_config['product_name'] =$product_name;
				//订单名称
				$pay_config['order_price'] = $orderamount;
				//付款金额
				$pay_config['order_id'] =  $id;

				$file = dirname(__FILE__) . '/../data/payment/wxpay_phone/exp/jsapi.php';
				
				if (file_exists($file)) {
					include_once ($file);
					exit();
				} else {
					$smarty -> display('404.html');
					exit();
				}
}else
{
	$smarty->display('404.html');
	exit();
}

?>
