<?php

if (!(defined('IN_IA'))) 

{

	exit('Access Denied');

}

class Web_Assess extends Web_Base

{
    /*删除*/
    public function delete(){
        global $_W,$_GPC;
        pdo_delete("monai_market_gujia",['id'=>$_GPC['id']]);
        message("删除成功",webUrl("assess/index"),"success");
    }

    public function index()
    {
        global $_W,$_GPC;
        $uniacid=$_W['uniacid'];
        $pindex = max(1, intval($_GPC['page']));
        $psize = 8;
        $list=pdo_fetchall("select a.*,b.nickname from ".tablename('monai_market_gujia')."  as a Left Join" .tablename('monai_market_member')." as b On a.uid=b.uid where a.`uniacid`='$uniacid' order by a.id desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
        $total = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_gujia')."  as a Left Join" .tablename('monai_market_member')." as b On a.uid=b.uid where a.`uniacid`='$uniacid' order by a.id desc");
        //var_dump($list);exit;
        $pager = pagination2($total, $pindex, $psize);
        $i = ($pindex - 1) * $psize+1;
        include $this->template();
    }
    public function index_see()
    {
        global $_W,$_GPC;
        $uniacid=$_W['uniacid'];
        if($_GPC['id']){
            $id=$_GPC['id'];
            $data['state'] = 1;
            $res=pdo_update('monai_market_gujia',$data,array('id'=>$id));
            if ($data) {
                echo  1;
            }else
            {
                echo 2;
            }
        }
    }
    public function loan()
    {
        global $_W,$_GPC;
        $uniacid=$_W['uniacid'];
        $pindex = max(1, intval($_GPC['page']));
        $psize = 8;
        $list=pdo_fetchall("select a.*,b.nickname,b.head_image from ".tablename('monai_market_loan')."  as a Left Join" .tablename('monai_market_member')." as b On a.uid=b.uid where a.`uniacid`='$uniacid' order by a.id desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
        $total = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_loan')."  as a Left Join" .tablename('monai_market_member')." as b On a.uid=b.uid where a.`uniacid`='$uniacid' order by a.id desc");
        //var_dump($list);exit;
        $pager = pagination2($total, $pindex, $psize);
        $i = ($pindex - 1) * $psize+1;
        include $this->template();
    }
    public function loan_see()
    {
        global $_W,$_GPC;
        $uniacid=$_W['uniacid'];
        if($_GPC['id']){
            $id=$_GPC['id'];
            $data['state'] = 1;
            $res=pdo_update('monai_market_loan',$data,array('id'=>$id));
            if ($res) {
                echo  1;
            }else
            {
                echo 2;
            }
        }
    }
}
?>