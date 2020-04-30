<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

require_once dirname(__FILE__) ."/../../../../data/payment/wxpay/lib/WxPay.Api.php";
require_once dirname(__FILE__) ."/../../../../data/payment/wxpay/exp/WxPay.NativePay.php";
require_once dirname(__FILE__) .'/../../../../data/payment/wxpay/exp/log.php';

//模式一
/**
 * 流程：
 * 1、组装包含支付信息的url，生成二维码
 * 2、用户扫描二维码，进行支付
 * 3、确定支付之后，微信服务器会回调预先配置的回调地址，在【微信开放平台-微信支付-支付配置】中进行配置
 * 4、在接到回调通知之后，用户进行统一下单支付，并返回支付信息以完成支付（见：native_notify.php）
 * 5、支付完成之后，微信服务器会通知支付成功
 * 6、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
 */
$notify = new NativePay();
$url1 = $notify->GetPrePayUrl("123456789");

//模式二
/**
 * 流程：
 * 1、调用统一下单，取得code_url，生成二维码
 * 2、用户扫描二维码，进行支付
 * 3、支付完成之后，微信服务器会通知支付成功
 * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
 */
$rate_fees=sprintf("%.0f", $pay_config['order_price']*100); 
$test = $pay_config['order_no']."|".$pay_config['product_name'];

$SERVER_PORT　= "";
$QUERY_STRING = "";
if ($_SERVER['SERVER_PORT'] != '80' ) {$SERVER_PORT = ":".$_SERVER['SERVER_PORT'];}
if ($_SERVER['QUERY_STRING'] != '' ) {$QUERY_STRING = "?".$_SERVER['QUERY_STRING'];}
$SERVER_URL = "http://".$_SERVER['SERVER_NAME']."/data/payment/wxpay/exp/native_notify.php";

// echo $SERVER_URL;die;

 
$input = new WxPayUnifiedOrder();
$input->SetBody($test);
$input->SetAttach($test);
$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
$input->SetTotal_fee($rate_fees);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag($test);
$input->SetNotify_url($SERVER_URL);
$input->SetTrade_type("NATIVE");
$input->SetProduct_id("123456789");
$result = $notify->GetPayUrl($input);
// var_dump($result);die;
$url2 = $result["code_url"];
$apply=$dosql->getone("select card_shenhe from #@__member where id = ".$_COOKIE['userid']); 
$html_text = "";
$html_text .= "<div style=\"margin-bottom:15px;font-size:14px;\">正在使用微信扫码交易<br><b style='font-size:16px; margin-right:60px;'>".$pay_config['product_name']."</b>收款方：".$cfg_webname."</div><div style=\"text-align:center;border-top: solid 3px #ccc;border-bottom: solid 3px #ccc; margin-bottom:50px; padding-top:80px; padding-bottom:100px;\"><span style='line-height:35px; font-size:14px'>扫一扫付款（元）</span><br><span style='font-size:25px; font-weight:bold; color:#ff6600'>".$pay_config['order_price']."</span><br><br><div style=\"border: solid 1px #ccc; width:222px; margin:0 auto;	box-shadow: 2px 2px #ccc;\"><img alt=\"微信扫码支付\" src=\"{dirname(__FILE__)}/../../../../data/payment/wxpay/exp/phpqrcode/qrcode.php?data=".urlencode($url2)."\" width='222' id='qr'/><div id='bottom'><img alt=\"微信扫码支付\" src=\"{dirname(__FILE__)}/../../../../data/payment/wxpay/exp/images/3.jpg\" style=\"margin-bottom:8px;\"></div><div id='sum'></div></div></div>";


		$str.= "<style>\n";	
		$str.=".changeBox_a1{width:204px;height:182px; position:relative; text-align:left; top:-500px; left:720px;}\n";
		$str.=".changeBox_a1 .a_bigImg img{position:absolute;display:none;}\n";
		$str.=".ul_change_a2{position:absolute;right:46%; bottom:1%;padding-left:19px;overflow:hidden;}\n";
		$str.=".ul_change_a2 li{display: -moz-inline-stack;display:inline-block;*display:inline;*zoom:1;}\n";
		$str.=".ul_change_a2 span{display: -moz-inline-stack;display:inline-block;*display:inline;*zoom:1;font-size:0.8em; margin-right:2px;background:#bebebe;filter:alpha(opacity=85);opacity:0.85;cursor:hand;cursor:pointer;width:5px; height:5px;line-height:none; padding:0px;}\n";
		$str.=".ul_change_a2 span.on{background:#c12322;color:#CC0000; }\n";
		$str.="</style>\n";	
		$str.="<div class=\"changeBox_a1\" id=\"change_3\">\n";
		$str.="<a class=\"a_bigImg\"><img src=\"{dirname(__FILE__)}/../../../../data/payment/wxpay/exp/images/1.jpg\" width=\"204\" height=\"182\" alt=\"\" /></a>\n";
		$str.="<a class=\"a_bigImg\"><img src=\"{dirname(__FILE__)}/../../../../data/payment/wxpay/exp/images/2.jpg\" width=\"204\" height=\"182\" alt=\"\" /></a>\n";
		$str.="<ul class=\"ul_change_a2\">\n";
		$str.="<li><span>&nbsp;</span></li>\n";
		$str.="<li><span>&nbsp;</span></li>\n";
		$str.="</ul>\n";
		$str.="</div>\n";
		$str.="<script type=\"text/javascript\" src=\"{dirname(__FILE__)}/../../../../data/payment/wxpay/exp/images/jquery.soChange-min.js\"></script>\n";
		$str.="<script>\n";
		$str.="$('#change_3 .a_bigImg img').soChange({\n";
		$str.="thumbObj:'#change_3 .ul_change_a2 span', \n";
		$str.="thumbNowClass:'on', \n";
		$str.="changeTime:4000 \n";
		$str.="});\n";
		$str.="</script>\n";

$str.="<script>\n";  
$str.="var t1 ;\n";
$str.="var sum=0;\n";
$str.="$(document).ready(function(){\n";
$str.="function update_native_state(){\n";
$str.="sum++;\n";
$str.="if(sum>600){window.clearInterval(t1);return false;}\n";
$str.="if(sum>180){\n";
$str.="m=sum % 10;\n";
$str.="if(m!=0){return false;}\n";
$str.="}\n";
$str.="$('#sum').html(sum);\n";
$str.="$.get('{dirname(__FILE__)}/../../../../data/payment/wxpay/exp/native_state.php?id=".$pay_config['order_no']."',function(data){\n";
$str.="v=eval(\"(\"+data+\")\");\n";
$str.="if(v.state=='USERPAYING'){\n";
$str.="$(\"#bottom\").html('');\n";
$str.="}\n";

$str.="if(v.state=='success'){\n";
$str.="window.clearInterval(t1);\n";
$str.="if(v.url==''){\n";
$str.="$(\"#qr\").attr('src','{dirname(__FILE__)}/../../../../data/payment/wxpay/exp/images/done.png');\n";
if($apply['card_shenhe'] != 3){
    $str.="$(\"#bottom\").html('支付成功，<a href=member.php?act=sm>请先进行实名认证</a>');\n";
}else{
    $str.="$(\"#bottom\").html('支付成功，<a href=member.php?act=ordercontent&orderid=".$pay_config['order_id'].">查看订单</a>');\n";
}
$str.="}else{\n";
$str.="window.location.href=v.url;\n";
$str.="}\n";
$str.="}\n";
$str.="});\n";
$str.="}\n";
$str.="t1 = window.setInterval(update_native_state,1000); \n";
$str.="window.onblur = function() {\n";
$str.="clearInterval(t1);\n";
$str.="};\n";
$str.="window.onfocus = function() {\n";
$str.="t1 = setInterval(function() {\n";
$str.="update_native_state();\n";
$str.="}, 1000);\n";
$str.="}\n";
$str.="});\n";
$str.="</script>\n";

$html_text .= $str;



?>