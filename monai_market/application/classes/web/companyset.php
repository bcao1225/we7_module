<?php

if (!(defined('IN_IA')))

{

	exit('Access Denied');

}

class Web_Companyset extends Web_Base

{



	public function index()

	{

        global $_W;

        global $_GPC;

        $uniacid=$_W['uniacid'];

        load()->func('tpl');

        $result=pdo_fetch("SELECT * FROM ".tablename('monai_market_info')." where `uniacid`='$uniacid' order by id desc limit 1");

        if($_W['ispost']){

            $data=array();

            $data['name']=$_GPC['name'];

            $data['remind']=$_GPC['remind'];

            $data['release_money']=$_GPC['release_money'];

            $data['uniacid']=$_W['uniacid'];

            $data['upload']=$_GPC['upload'];

            $data['logo']=$_GPC['logo'];

            $data['phone']=$_GPC['phone'];

            $data['top_cycle']=$_GPC['top_cycle'];

            $data['top_money']=$_GPC['top_money'];

            $data['audit_status']=$_GPC['audit_status'];

            $data['recom']=$_GPC['recom'];

            $data['plate_type']=$_GPC['plate_type'];

            $data['sale_logo']=$_GPC['sale_logo'];

            $data['map_key']=$_GPC['map_key'];

            $data['area_set']=$_GPC['area_set'];
            $data['qipei_open']=$_GPC['qipei_open'];

            $data['vip_words'] =  $_GPC['vip_words'];
            $data['ad_one'] = $_GPC['ad_one'];
            $data['ad_two'] = $_GPC['ad_two'];
            $data['ad_three'] = $_GPC['ad_three'];
            $data['ad_detail'] = $_GPC['ad_detail'];

            $data['ad_buycar'] = $_GPC['ad_buycar'];
            $data['ad_salecar'] = $_GPC['ad_salecar'];

            $data['ad_oneurl'] = $_GPC['ad_oneurl'];
            $data['ad_twourl'] = $_GPC['ad_twourl'];
            $data['ad_threeurl'] = $_GPC['ad_threeurl'];

            $data['deposit_img'] = $_GPC['deposit_img'];
            $data['pag_vipresult'] = $_GPC['pag_vipresult'];

            $data['pop_bgimg'] = $_GPC['pop_bgimg'];
            $data['pop_con'] = $_GPC['pop_con'];
            $data['is_vipgroup'] = $_GPC['is_vipgroup'];

            /*$data['flow_set']=$_GPC['flow_set'];

            if ($_GPC['flow_set']==2) {

                $data['flow_id']=$_GPC['flow_id'];

            }else{

                $data['flow_id']='';
            }*/

            if(!empty($result)){

                $res=pdo_update('monai_market_info',$data,array('uniacid'=>$uniacid));

                $logo['nickname']=$_GPC['name'];

                $logo['head_image']=tomedia($_GPC['logo']);

                $logo['phone']=$_GPC['phone'];

                $logo['uid']=0;

                $res=pdo_update('monai_market_member',$logo,array('uid'=>0,'uniacid'=>$uniacid));

            }else{

                pdo_delete('monai_market_member',array('uid'=>0,'uniacid'=>$uniacid));

                $logo['head_image']=tomedia($_GPC['logo']);

                $logo['nickname']=$_GPC['name'];

                $logo['phone']=$_GPC['phone'];

                $logo['uid']=0;

                $logo['uniacid']=$uniacid;

                $logo['create_time']=time();

                $res=pdo_insert('monai_market_member',$logo);

                $res=pdo_insert('monai_market_info',$data);
            }
            $this->success('修改成功','companyset/index');

        }

        include $this->template();

	}
    public function sale_set()
    {
        global $_W;

        global $_GPC;

        $uniacid=$_W['uniacid'];

        load()->func('tpl');

        $result=pdo_fetch("SELECT * FROM ".tablename('monai_market_saleinfo')." where `uniacid`='$uniacid' order by uniacid desc limit 1");

        if($_W['ispost']){

            $data=array();

            $data['status']=$_GPC['status'];

            $data['scale']=$_GPC['scale'];

            $data['image_patch']=$_GPC['image_patch'];

            $data['uniacid']=$_W['uniacid'];
						$data['kf_info'] = $_GPC['kf_info'];
						$data['kf_qrcode'] = $_GPC['kf_qrcode'];
            if(!empty($result)){

                $res=pdo_update('monai_market_saleinfo',$data,array('uniacid'=>$uniacid));

            }else{

                $res=pdo_insert('monai_market_saleinfo',$data);

            }

            $this->success('修改成功','companyset/sale_set');

        }

        include $this->template();
    }
    public function enter_set()
    {
        global $_W;
        global $_GPC;
        $uniacid=$_W['uniacid'];
        load()->func('tpl');
        $data=pdo_fetch("SELECT * FROM ".tablename('monai_market_enter')." where `uniacid`='$uniacid' order by id desc limit 1");
        if($_W['ispost']){
            $data1=array();
            $data1['status']=$_GPC['status'];
            $data1['cycle']=$_GPC['cycle'];
            $data1['name']=$_GPC['name'];
            $data1['price']=$_GPC['price'];
            /*积分*/
            $data1['integral']=$_GPC['integral'];
            $data1['uniacid']=$_W['uniacid'];
            if(!empty($data)){
                $res=pdo_update('monai_market_enter',$data1,array('uniacid'=>$uniacid));
            }else{
                $res=pdo_insert('monai_market_enter',$data1);
            }
            $this->success('修改成功','companyset/enter_set');
        }
        include $this->template();
    }
    public function flow_set()
    {
        global $_W;
        global $_GPC;
        $uniacid=$_W['uniacid'];
        load()->func('tpl');
        $result=pdo_fetch("SELECT * FROM ".tablename('monai_market_info')." where `uniacid`='$uniacid' order by id desc limit 1");
        if($_W['ispost']){
            $data1=array();
            $data1['flow_set']=$_GPC['flow_set'];
            $data1['head_flow']=$_GPC['head_flow'];
            $data1['sell_flow']=$_GPC['sell_flow'];
            if ($_GPC['flow_set']==2) {
                $data1['flow_set_id']=$_GPC['flow_set_id'];
            }else
            {
                $data1['flow_set_id']='';
            }
            if ($_GPC['head_flow']==2) {

                $data1['head_flow_id']=$_GPC['head_flow_id'];
            }else
            {
                $data1['head_flow_id']='';
            }if ($_GPC['sell_flow']==2) {

                $data1['flow_id']=$_GPC['flow_id'];
            }else
            {
                $data1['flow_id']='';
            }
            $data1['uniacid']=$_W['uniacid'];
            if(!empty($result)){
                $res=pdo_update('monai_market_info',$data1,array('uniacid'=>$uniacid));
            }else{
                $res=pdo_insert('monai_market_info',$data1);
            }
            $this->success('修改成功','companyset/flow_set');
        }
        include $this->template();
    }
    /*
     * 违章设置
     */
    public function peccancy_set()
    {
        global $_W; global $_GPC;
        $uniacid=$_W['uniacid'];
        load()->func('tpl');
        $result=pdo_fetch("SELECT * FROM ".tablename('monai_market_info')." where `uniacid`='$uniacid' order by id desc limit 1");
        if($_W['ispost']){
            $data=array();
            $data['juhe_appkey']=$_GPC['juhe_appkey'];
            $data['weizhang_num']=$_GPC['weizhang_num'];
            $data['weizhang_money']=$_GPC['weizhang_money'];

            if(!empty($result)){
                $res=pdo_update('monai_market_info',$data,array('uniacid'=>$uniacid));
            }else{
                $res=pdo_insert('monai_market_info',$data);
            }
            $this->success('修改成功','companyset/peccancy_set');
        }
        include $this->template();
    }

    /*

     * 去除富文本内的字体

     * 2018年1月19日13:22:00

     */

    public function fwb_del_font($str)

    {

        $str = htmlspecialchars_decode($str);

        $re = preg_replace("/font-family:.*;/i",'',$str);

        $re = preg_replace("/font-family:.*\"/i",'"',$re);

        return $re;

    }

    /**
     * 消息模板设置
     */
    public function message() {
        global $_W, $_GPC;

        $arr = pdo_getall('monai_market_wx_message_tmp', array('uniacid'=>$_W['uniacid']), array(), 'type');

        if ($_W['ispost']) {
            $rel = true;
            for ($i=1; $i<4; $i++) {
                if (empty($arr[ $i ])) {
                    $data = array(
                        'uniacid' => $_W['uniacid'],
                        'name' => $_GPC['message' . $i]['name'],
                        'content' => $_GPC['message' . $i]['content'],
                        'type' => $_GPC['message' . $i]['type'],
                    );
                    $res = pdo_insert('monai_market_wx_message_tmp', $data);
                    if (empty($res)) {
                        $rel = false;
                    }
                } else {
                    $data = array(
                        'name' => $_GPC['message' . $i]['name'],
                        'content' => $_GPC['message' . $i]['content'],
                        'type' => $_GPC['message' . $i]['type'],
                    );
                    $res = pdo_update('monai_market_wx_message_tmp', $data, array('uniacid'=>$_W['uniacid'],'type'=>$_GPC['message' . $i]['type']));
                    if ($res === false) {
                        $rel = false;
                    }
                }
            }

            if ($rel) {
                $this->success('保存成功', '', 0);
            } else {
                $this->success('保存失败', '', 0);
            }
        }

        include $this->template();
    }

		public function slide()

	    {

	        global $_W,$_GPC;

	        $uniacid=$_W['uniacid'];

	        //$list=pdo_fetchall("select `id`,`url`,`sort` from ".tablename('miniapp_company_image')." where `admin_id`='$uniacid' order by sort desc");



	        $pindex = max(1, intval($_GPC['page']));

	        $psize = 16;

	        $list=pdo_fetchall("select * from ".tablename('monai_wx_ad')." where `uniacid`='$uniacid' order by sort desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");



	        $total = pdo_fetchcolumn("select count(*) from ".tablename('monai_wx_ad')." where `uniacid`='$uniacid' order by sort desc");

	        $pager = pagination2($total, $pindex, $psize);

	        $i = ($pindex - 1) * $psize+1;

	        include $this->template();

	    }

	    /*

	     * 批量处理产品

	     */

	    public function slide_editall()

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

	                $data['uniacid'] = $uniacid;

	                pdo_delete('monai_wx_ad',$data);

	            }

	            $this->success('删除成功','',2);

	        }

	    }

	    /*

	    * 添加幻灯片

	     */

	    public function slide_add()

	    {

	        global $_W,$_GPC;

	        $uniacid=$_W['uniacid'];

	        $_W['page']['title'] = '添加微信广告';

	        load()->func('tpl');

	        $list=pdo_fetchall("select * from ".tablename('monai_wx_ad')." where `uniacid`='$uniacid'  order by id desc");

	        if($_W['ispost']){

	            //var_dump($_GPC['type']);exit;

	            $data=array();

	            // $list = pdo_get('monai_wx_ad',array('id'=>$_GPC['product_id'],'uniacid'=>$uniacid));

	            $data['title']=$_GPC['title'];

							$data['subtitle']=$_GPC['subtitle'];

	            $data['head_images']=$_GPC['head_images'];

	            $data['qrcode_images']=$_GPC['qrcode_images'];

	            $data['uniacid']=$uniacid;

	            $res=pdo_insert('monai_wx_ad',$data);

	            if($_GPC['jixu'])

	            {

	                //继续添加

	                $this->success('添加成功','',2);

	            }

	            else

	            {

	                $this->success('添加成功','wxad/index/slide');

	            }

	        }

	        include $this->template();

	    }

	    /*

	    * 修改幻灯片

	     */

	    public function slide_edit()

	    {

	        global $_W,$_GPC;

	        load()->func('tpl');

	        $id=$_GPC['id'];

	        //var_dump($id);exit;

	        $uniacid=$_W['uniacid'];

	        $_W['page']['title'] = '修改微信广告';

	        $data=pdo_get('monai_wx_ad',array('id'=>$id,'uniacid'=>$uniacid));

	        $list=pdo_fetchall("select * from ".tablename('monai_market_car_detail')." where `uniacid`='$uniacid' and `status`=3 and `delete_time`=0 order by id desc");

	        if($_W['ispost']){

	            $data=array();

	            //$list = pdo_get('monai_market_car_detail',array('id'=>$_GPC['product_id'],'uniacid'=>$uniacid));

							$data['title']=$_GPC['title'];

							$data['subtitle']=$_GPC['subtitle'];

	            $data['head_images']=$_GPC['head_images'];

	            $data['qrcode_images']=$_GPC['qrcode_images'];

	            // $data['uniacid']=$uniacid;

	            $res=pdo_update('monai_wx_ad',$data,array('id'=>$id));

	            //echo  "成功";exit;

	           $this->success('修改成功','wxad/index/slide');

	        }

	        include $this->template();

	    }

	    /*

	    * 删除幻灯片

	     */

	    public function slide_del($id=0){

	        global $_W,$_GPC;

	        $uniacid=$_W['uniacid'];

	        if($_GPC['id']){

	            $id=$_GPC['id'];

	            $data=pdo_delete('monai_market_image',array('id'=>$id,'uniacid'=>$uniacid));
	            if ($data) {
	                echo  1;
	            }else
	            {
	                echo 2;
	            }
	        }
	    }


    /**
     * 广告位设置
     */
    public function adv_set(){
        global $_W,$_GPC;

        include $this->template();
    }

    /**
     * 加群列表
     */
    public function group_list(){
        global $_W,$_GPC;
        $uniacid = $_W['uniacid'];

        $list = pdo_getall('monai_market_yclist',array('uniacid'=>$uniacid,'type'=>1));
        $grouplist = array();
        foreach ($list as $item){
            $info = iunserializer($item['con']);
            $info['id'] = $item['id'];
            array_unshift($grouplist,$info);
        }

        include $this->template();
    }

    public function group_del(){
        global $_W,$_GPC;
        $uniacid=$_W['uniacid'];

        if($_GPC['id']){
            $id=$_GPC['id'];
            $res=pdo_delete('monai_market_yclist',array('id'=>$id,'uniacid'=>$uniacid));
            if ($res) {
                echo  1;
            }else
            {
                echo 2;
            }
        }
    }
    public function group_edit(){
        global $_W,$_GPC;
        $uniacid = $_W['uniacid'];
        $op = $_GPC['op'];
        if($op=='edit'){
            $id = $_GPC['id'];
            $info = pdo_get('monai_market_yclist',array('id'=>$id,'uniacid'=>$uniacid,'type'=>1));
            if($info){
                $groupinfo = iunserializer($info['con']);
            }
        }

        if ($_W['ispost']){
            $group = array(
                'name' => $_GPC['name'],
                'des' => $_GPC['des'],
                'qrcode' => $_GPC['qrcode']
            );
            $data = array(
                'name' => 'group',
                'con' => iserializer($group),
                'type' => 1,
                'status' => 1
            );
            if($op=='edit'){
                pdo_update('monai_market_yclist',$data,array('id'=>$_GPC['id']));
                $this->success('保存成功','companyset/group_list');
            }else{
                $data['uniacid'] = $uniacid;
                $data['createtime'] = time();
                pdo_insert('monai_market_yclist',$data);
                $this->success('添加成功','companyset/group_list');
            }
        }

        include $this->template();
    }

}

?>
