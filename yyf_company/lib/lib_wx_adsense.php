<?php
	global $_GPC, $_W;
	$uniacid=$_GPC['uniacid'];
	$data1=pdo_get('yyf_company_adsense',array('uniacid'=>$uniacid));
	foreach ($data1 as $key => $value) {
		if(strpos($key, 'img')){
			$data1[$key]=tomedia($value);
		}
		if(strpos($key, 'url')){
			if(substr($value, 0,1)=='a'){
				$num=substr($key,1,1);
				$id=substr($value, 1,strlen($value)-1);
				$videosrc=pdo_getcolumn('yyf_company_news',array('id'=>$id),'videosrc');
				$videosrc=trim($videosrc);
				if(empty($videosrc)){
					$data1[$key]='../content/content?id='.$id;
				}else{
					$data1[$key]='../video/video?id='.$id;
				}
			}else{
				$id=$value;
				$res=pdo_get('yyf_company_category',array('id'=>$id),array('type'));
				$num=substr($key,1,1);
				if($res['type']==1){
					$data1[$key]='../case/case?tid='.$id.'&fid=0';
				}elseif($res['type']==2){
					$data1[$key]='../news/news?tid='.$id.'&fid=0';
				}elseif($res['type']==4){
					$data1[$key]='../newsnoimg/newsnoimg?tid='.$id.'&fid=0';
				}elseif($res['type']==3){
					$data1[$key]='../about/about?tid='.$id;
				}elseif($res['type']==5){
					$data1[$key]='../imglist/imglist?tid='.$id.'&fid=0';
				}else{
					$data1[$key]='0';
				}
			}
		}
	}

	$message = '返回消息';
		$errno = 0;
		return $this->result($errno, $message,$data1);
	
?>