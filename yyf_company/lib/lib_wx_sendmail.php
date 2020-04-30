<?php
		global $_GPC, $_W;
		require_once (MODULE_ROOT.'/lib/lib_email.php');
		//$uniacid=$_GPC['uniacid'];

		$id=$_GPC['__input']['inserid'];
		$uniacid=$_GPC['__input']['uniacid'];
		//file_put_contents('1.txt',var_export($_GPC,true));
		

		if($id){
			$data=pdo_get('yyf_company_formvalue',array('id'=>$id));
			$con="";
			$data['t1name'] ? $con.=$data['t1name'].': '.$data['t1value'].'<br/>' : '';
			$data['t2name'] ? $con.=$data['t2name'].': '.$data['t2value'].'<br/>' : '';
			$data['t3name'] ? $con.=$data['t3name'].': '.$data['t3value'].'<br/>' : '';
			$data['t4name'] ? $con.=$data['t4name'].': '.$data['t4value'].'<br/>' : '';
			$data['rname'] ? $con.=$data['rname'].': '.$data['rvalue'].'<br/>' : '';
			$data['aname'] ? $con.=$data['aname'].': '.$data['avalue'].'<br/>' : '';
			$data['cname'] ? $con.=$data['cname'].': '.$data['cvalue'] : '';
			$row=pdo_get('yyf_company_sysinfo',array('uniacid'=>$uniacid),array('message_email','message_title','smtp_email','smtp_key'));
			if($row['message_email'] && $row['smtp_email']){
				sendMail($row['message_email'],'点击查看详情',$con,$row['message_title'],$row['smtp_email'],$row['smtp_key']);
			}
			
		}

		
		



?>