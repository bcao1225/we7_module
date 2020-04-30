<?php
// echo json_encode($_GET);die;
/**
 *	
 * 功能：查询扫码支付状态，并返回给扫码支付前台页面
 * 改者：【梦行Monxin】积木式建站系统 www.monxin.com
 * 日期：2015-07-14
 *	
 */

header('Content-Type:text/html;charset=utf-8');
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);

$id=@$_GET['id'];
// echo $id;die;
if($id==0){exit("{'state':'fail','info':'<span class=fail>id err</span>'}");}
//====================================================================================================================================查数据库检测支付状态 start
		define('IN_LCSHOP', true);
		require_once(dirname(__FILE__).'/../../../../include/config.php');
		$r=$dosql->getone("select checkinfo from #@__goodsorder where  orderlistnum='{$id}'"); 
		// echo json_encode($r);die;
		if($r['checkinfo']==1){exit("{'state':'success','info':'<span class=success>".$language['success']."</span>','url':''}");}

//====================================================================================================================================查数据库检测支付状态 end


require_once dirname(__FILE__)."/../lib/WxPay.Api.php";

/*
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("./logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);
*/
	$out_trade_no=$id;
	$input = new WxPayOrderQuery();
	$input->SetOut_trade_no($out_trade_no);
	$r=WxPayApi::orderQuery($input);
	//var_dump($r);
	//$r['trade_state']='USERPAYING';
	exit("{'state':'".$r['trade_state']."','info':'".$r['trade_state']."'}");
	exit();

?>