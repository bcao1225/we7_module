<?php 
global $_GPC, $_W;

$uniacid=$_W['uniacid'];
$v=pdo_fetch("SELECT * FROM ".tablename('yyf_company_tabbar')." where `uniacid`='$uniacid' order by id desc limit 1");
$styleArray=pdo_get('yyf_company_style',array('uniacid'=>$uniacid),array('tcolor'));

//echo pdo_debug();
$list=array();
foreach ($v as $key => $value) {
	strpos($key, 'img') ? $v[$key]=tomedia($value) : '';
	if(strpos($key, 'path')){
		if($value=='index'){
			$num=substr($key,1,1);
			$v['path'.$num]='../index/index';
		}
		if($value=='message'){
			$num=substr($key,1,1);
			$v['path'.$num]='../message/message';
		}
		if($value=='contact'){
			$num=substr($key,1,1);
			$v['path'.$num]='../contact/contact';
		}
		if(is_numeric($value)){
			$id=$value;
			$res=pdo_get('yyf_company_category',array('id'=>$id),array('type'));
			$num=substr($key,1,1);
			if($res['type']==1){
				$v['path'.$num]='../case/case?tid='.$id.'&fid=0';
			}elseif($res['type']==2){
				$v['path'.$num]='../news/news?tid='.$id.'&fid=0';
			}elseif($res['type']==4){
				$v['path'.$num]='../newsnoimg/newsnoimg?tid='.$id.'&fid=0';
			}elseif($res['type']==5){
				$v['path'.$num]='../imglist/imglist?tid='.$id.'&fid=0';
			}else{
				$v['path'.$num]='../about/about?tid='.$id;
			}
		}
		if(substr($value, 0,1)=='a'){
			$num=substr($key,1,1);
			$id=substr($value, 1,strlen($value)-1);
			$videosrc=pdo_getcolumn('yyf_company_news',array('id'=>$id),'videosrc');
			$videosrc=trim($videosrc);

			if(empty($videosrc)){
				$v['path'.$num]='../content/content?id='.$id;
			}else{
				$v['path'.$num]='../video/video?id='.$id;
			}
		}

	}
}

$v['tcolor']=$styleArray['tcolor'];
$sysinfo=pdo_get('yyf_company_sysinfo',array('uniacid'=>$uniacid),array('phone','jing','wei','address'));
$v['phone']=$sysinfo['phone'];
$v['jing']=$sysinfo['jing'];
$v['wei']=$sysinfo['wei'];
$v['address']=$sysinfo['address'];
$message = '返回消息';
$errno = 0;
return $this->result($errno, $message,$v);



?>