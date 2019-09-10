<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Web_Appoint_Index extends Web_Base
{

	public function index() 
	{
        global $_W,$_GPC;
        $uniacid=$_W['uniacid'];
        $status=$_GPC['status'];
        if ($status!='') {
            $where = "and status ='".$status."'" ;
        }
        //$list=pdo_fetchall("select * from ".tablename('miniapp_company_appoint')." where `admin_id`='$uniacid' order by id desc");
        $info=pdo_get('miniapp_company_info',array('store_id'=>$uniacid));
        $pindex = max(1, intval($_GPC['page']));
        $psize = 13;
        $list=pdo_fetchall("select * from ".tablename('miniapp_company_appoint')." where `admin_id`='$uniacid' $where order by id desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
        
        $total = pdo_fetchcolumn("select count(*) from ".tablename('miniapp_company_appoint')." where `admin_id`='$uniacid' $where order by id desc");
        $pager = pagination($total, $pindex, $psize);
        //var_dump($list);
        $i = ($pindex - 1) * $psize+1;
        include $this->template();
	}
    /*
     * 批量处理产品
     */
    public function appoint_editall()
    {
        global $_W,$_GPC;
        $uniacid=$_W['uniacid'];
        if(empty($_GPC['xuanze']))
        {
            $this->error('未选择数据','',0);
        }
        if($_GPC['piliang'] == 1)
        {
            //批量删除
            foreach ($_GPC['xuanze'] as $key => $value) {
                $data['id']=$value;
                $data['admin_id'] = $uniacid;
                pdo_delete('miniapp_company_appoint',$data);
            }
            $this->success('删除成功','',2);
        }
        else if($_GPC['piliang'] == 2)
        {
            //批量查看
            foreach ($_GPC['xuanze'] as $key => $value) {
                $data['status'] = 2;
                pdo_update('miniapp_company_appoint',$data,array('id'=>$value,'admin_id'=>$uniacid));
            }
            $this->success('处理成功','',2);
        }
        else
        {
            //批量不查看
            foreach ($_GPC['xuanze'] as $key => $value) {
                $data['status'] = 1;
                pdo_update('miniapp_company_appoint',$data,array('id'=>$value,'admin_id'=>$uniacid));
            }
            $this->success('处理成功','',2);
        }
    }
    /*
    * 修改查看状态
     */
    public function appoint_ajax_type()
    {
        global $_W,$_GPC;
        load()->func('tpl');
        $id=$_GPC['id'];
        $uniacid=$_W['uniacid'];
        $data=pdo_get('miniapp_company_appoint',array('id'=>$id,'admin_id'=>$uniacid));
        $sale = $data['status'] == '1'? '2' : '1';
        $array = array('status'=>$sale);
        $res=pdo_update('miniapp_company_appoint',$array,array('id'=>$id,'admin_id'=>$uniacid));
        echo $sale; 
        exit;   
    }
    public function set() 
	{
        global $_W,$_GPC;
        $uniacid=$_W['uniacid'];
        load()->func('tpl');
        $result=pdo_fetch("SELECT * FROM ".tablename('miniapp_company_info')." where `store_id`='$uniacid' order by id desc limit 1");
        if($_W['ispost']){
            $aot = array();
            foreach ($_GPC['aot'] as $k) {
                if($k!='')
                {
                    $aot[] = $k;
                }
            }
            $data=array();
            $data['store_id']=$_W['uniacid'];
            $data['contact']=$_GPC['contact'];
            $data['appointment_img']=$_GPC['appointment_img'];
            $data['aot1']=$aot[0];
            $data['aot2']=$aot[1];
            $data['aot3']=$aot[2];
            $data['aot4']=$aot[3];
            $result=pdo_fetch("SELECT `id` FROM ".tablename('miniapp_company_info')." where `store_id`='$uniacid' order by id desc limit 1");
            if($result['id']){
                $res=pdo_update('miniapp_company_info',$data,array('id'=>$result['id']));
            }else{
                $res=pdo_insert('miniapp_company_info',$data);
            }
            $this->success('修改成功','appoint/index/set');
        }
        include $this->template();
	}
}
?>