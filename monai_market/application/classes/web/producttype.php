<?php

if (!(defined('IN_IA'))) 

{

	exit('Access Denied');

}

class Web_Producttype extends Web_Base

{



	public function type() 

	{

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        $pindex = max(1, intval($_GPC['page']));

        $psize = 8;

        $where = '';
        if ($_GPC['name']) {
            $where .= " AND `name` like('%{$_GPC['name']}%')";
        }
        $pid = intval($_GPC['pid']);

        //计算 层级
        $level = 0;
        $pidtemp = $pid;
        $arrparent = array();
        while ($pidtemp!=0){
            $infotemp = pdo_get('monai_market_brand',array('id'=>$pidtemp));
            $level++;
            if ($level>2){
                $pid = 0;
                break;
            }
            $pidtemp = $infotemp['pid'];
            if(intval($pidtemp)==0){
                $arrparent['level1'] = $infotemp;
            }else{
                $arrparent['level2'] = $infotemp;
            }
        }
        $where .= " AND `pid`={$pid}";


        $list = pdo_fetchall("select * from ".tablename('monai_market_brand')." where `uniacid`='$uniacid' {$where} order by sort desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");

        foreach($list as &$itemy){
            $sql = "SELECT COUNT(*) FROM ".tablename('monai_market_brand')." WHERE `pid`={$itemy['id']} AND `uniacid`='$uniacid'";
            $count = pdo_fetchcolumn($sql);
            $itemy['issub'] = ($count>0) ? 1 : 0;
        }
        unset($itemy);

        

        $total = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_brand')." where `uniacid`='$uniacid' {$where}");

        $pager = pagination2($total, $pindex, $psize);

        $i = ($pindex - 1) * $psize+1;

        include $this->template();

	}

    public function type_add() 

	{

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        //echo $uniacid;exit;
        $pid = intval($_GPC['pid']);

        load()->func('tpl');

        if($_W['ispost']){

            $data=array();

            $data['pid'] = $pid;

            $data['name']=$_GPC['name'];

            $data['brand_icon']=$_GPC['brand_icon'];

            $data['guide']=$_GPC['guide'];

            if($_GPC['abc']){
                $data['abc'] = $_GPC['abc'];
            }else{
                $data['abc'] = $this->getFirstCharter($_GPC['name']);
            }


            $data['sort']=$_GPC['sort'];

            $data['uniacid']=$uniacid;

            $res=pdo_insert('monai_market_brand',$data);  

            if($_GPC['jixu'])
            {
                //继续添加
                $this->success('添加成功','',2);
            }
            else
            {

                echo json_encode(array('msg'=>'添加成功','url'=>webUrl('producttype/type').'&pid='.$pid,'type'=>1,'state'=>'success'));
                exit;
                //$this->success('添加成功','producttype/type');

            }

        }

        include $this->template();

	}

    /*

    * 修改分类

     */

    public function type_edit()
    {

        global $_W,$_GPC;

        load()->func('tpl');

        $id=$_GPC['id'];

        $uniacid=$_W['uniacid'];

        $data=pdo_get('monai_market_brand',array('id'=>$id,'uniacid'=>$uniacid));

        if($_W['ispost']){
            $data=array();
            $data['name']=$_GPC['name'];
            $data['brand_icon']=$_GPC['brand_icon'];

            if($_GPC['abc']){
                $data['abc']=$_GPC['abc'];
            }else{
                $data['abc']=$this->getFirstCharter($_GPC['name']);
            }

            /*指导价格*/
            $data['guide'] = $_GPC['guide'];

            $data['sort']=$_GPC['sort'];

            $res=pdo_update('monai_market_brand',$data,array('id'=>$id));

            $this->success('修改成功','producttype/type');

        }
        include $this->template();
    }

    /*

    * 删除分类

     */

    public function type_del()

    {

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        if($_GPC['id']){

            $id=$_GPC['id'];

            $data=pdo_delete('monai_market_brand',array('id'=>$id,'uniacid'=>$uniacid));

            if ($data) {
                echo  1;
            }else
            {
                echo 2;
            }

        }

    }
    //一键导入汽车品牌
    public function shuju_add_all()
    {
        global $_W,$_GPC;
        $uniacid=$_W['uniacid'];
        $car_arr = array(
            ['name'=>'ALPINA',      'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/ALPINA.png', 'abc'=>'A','uniacid'=>$uniacid],
            ['name'=>'ARCFOX',       'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/ARCFOX.png','abc'=>'A','uniacid'=>$uniacid],
            ['name'=>'阿尔法·罗密欧', 'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/aer.png','abc'=>'A','uniacid'=>$uniacid],
            ['name'=>'阿斯顿·马丁',   'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/asi.png','abc'=>'A','uniacid'=>$uniacid],
            ['name'=>'奥迪',         'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/ao.png','abc'=>'A','uniacid'=>$uniacid],

            ['name'=>'宝骏',      'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/bjun.png','abc'=>'B','uniacid'=>$uniacid],
            ['name'=>'宝马',      'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/bm.png','abc'=>'B','uniacid'=>$uniacid],
            ['name'=>'宝沃',      'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/bw.png','abc'=>'B','uniacid'=>$uniacid],
            ['name'=>'保时捷',    'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/bsj.png','abc'=>'B','uniacid'=>$uniacid],
            ['name'=>'北京',      'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/bj.png','abc'=>'B','uniacid'=>$uniacid],
            ['name'=>'北汽昌河',    'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/bqch.png','abc'=>'B','uniacid'=>$uniacid],
            ['name'=>'北汽道达',    'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/bqdd.png','abc'=>'B','uniacid'=>$uniacid],
            ['name'=>'北汽幻速',    'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/bqhs.png','abc'=>'B','uniacid'=>$uniacid],
            ['name'=>'北汽绅宝',    'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/bqsb.png','abc'=>'B','uniacid'=>$uniacid],
            ['name'=>'北汽威旺',    'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/bqww.png','abc'=>'B','uniacid'=>$uniacid],
            ['name'=>'北汽新能源',   'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/bqxny.png','abc'=>'B','uniacid'=>$uniacid],
            ['name'=>'北汽制造',    'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/bqzz.png','abc'=>'B','uniacid'=>$uniacid],
            ['name'=>'奔驰',      'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/bc.png','abc'=>'B','uniacid'=>$uniacid],
            ['name'=>'奔腾',      'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/bt.png','abc'=>'B','uniacid'=>$uniacid],
            ['name'=>'本田',      'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/btian.png','abc'=>'B','uniacid'=>$uniacid],
            ['name'=>'比速',      'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/bs.png','abc'=>'B','uniacid'=>$uniacid],
            ['name'=>'比亚迪',     'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/byd.png','abc'=>'B','uniacid'=>$uniacid],
            ['name'=>'标致',      'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/bz.png','abc'=>'B','uniacid'=>$uniacid],
            ['name'=>'别克',      'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/bk.png','abc'=>'B','uniacid'=>$uniacid],
            ['name'=>'宾利',      'brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/bl.png','abc'=>'B','uniacid'=>$uniacid],

            ['name'=>'长安跨越','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/caky.png','abc'=>'C','uniacid'=>$uniacid],
            ['name'=>'长安欧尚','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/caos.png','abc'=>'C','uniacid'=>$uniacid],
            ['name'=>'长安汽车','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/caqc.png','abc'=>'C','uniacid'=>$uniacid],
            ['name'=>'长安轻型车','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/caqxc.png','abc'=>'C','uniacid'=>$uniacid],
            ['name'=>'长城','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/cc.png','abc'=>'C','uniacid'=>$uniacid],

            ['name'=>'DS','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/DS.png','abc'=>'D','uniacid'=>$uniacid],
            ['name'=>'大众','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/dz.png','abc'=>'D','uniacid'=>$uniacid],
            ['name'=>'道奇','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/dq.png','abc'=>'D','uniacid'=>$uniacid],
            ['name'=>'电咖','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/dk.png','abc'=>'D','uniacid'=>$uniacid],
            ['name'=>'东风','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/df.png','abc'=>'D','uniacid'=>$uniacid],
            ['name'=>'东风风度','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/dffd.png','abc'=>'D','uniacid'=>$uniacid],
            ['name'=>'东风风光','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/dffg.png','abc'=>'D','uniacid'=>$uniacid],
            ['name'=>'东风风行','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/dffx.png','abc'=>'D','uniacid'=>$uniacid],
            ['name'=>'东风风神','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/dffs.png','abc'=>'D','uniacid'=>$uniacid],
            ['name'=>'东风小康','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/dfxk.png','abc'=>'D','uniacid'=>$uniacid],
            ['name'=>'东南','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/dn.png','abc'=>'D','uniacid'=>$uniacid],

            ['name'=>'Faraday Future','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/Faraday.png','abc'=>'F','uniacid'=>$uniacid],
            ['name'=>'法拉利','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/fll.png','abc'=>'F','uniacid'=>$uniacid],
            ['name'=>'菲亚特','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/fyt.png','abc'=>'F','uniacid'=>$uniacid],
            ['name'=>'丰田','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/ftian.png','abc'=>'F','uniacid'=>$uniacid],
            ['name'=>'福迪','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/fd.png','abc'=>'F','uniacid'=>$uniacid],
            ['name'=>'福汽启腾','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/fqqt.png','abc'=>'F','uniacid'=>$uniacid],
            ['name'=>'福特','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/ft.png','abc'=>'F','uniacid'=>$uniacid],
            ['name'=>'福田','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/fut.png','abc'=>'F','uniacid'=>$uniacid],

            ['name'=>'GMC','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/GMC.png','abc'=>'G','uniacid'=>$uniacid],
            ['name'=>'观致','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/gz.png','abc'=>'G','uniacid'=>$uniacid],
            ['name'=>'广汽传祺','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/gqcq.png','abc'=>'G','uniacid'=>$uniacid],
            ['name'=>'广汽新能源','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/gqxny.png','abc'=>'G','uniacid'=>$uniacid],
            ['name'=>'国金','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/gj.png','abc'=>'G','uniacid'=>$uniacid],

            ['name'=>'哈佛','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/hf.png','abc'=>'H','uniacid'=>$uniacid],
            ['name'=>'海马','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/hm.png','abc'=>'H','uniacid'=>$uniacid],
            ['name'=>'汉腾','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/ht.png','abc'=>'H','uniacid'=>$uniacid],
            ['name'=>'红旗','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/hq.png','abc'=>'H','uniacid'=>$uniacid],
            ['name'=>'红星汽车','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/hx.png','abc'=>'H','uniacid'=>$uniacid],
            ['name'=>'华骐','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/hqi.png','abc'=>'H','uniacid'=>$uniacid],
            ['name'=>'华颂','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/hs.png','abc'=>'H','uniacid'=>$uniacid],
            ['name'=>'华泰','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/htai.png','abc'=>'H','uniacid'=>$uniacid],
            ['name'=>'黄海','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/hh.png','abc'=>'H','uniacid'=>$uniacid],

            ['name'=>'Jeep','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/Jeep.png','abc'=>'J','uniacid'=>$uniacid],
            ['name'=>'吉利','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/jl.png','abc'=>'J','uniacid'=>$uniacid],
            ['name'=>'江淮','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/jh.png','abc'=>'J','uniacid'=>$uniacid],
            ['name'=>'江铃','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/jling.png','abc'=>'J','uniacid'=>$uniacid],
            ['name'=>'捷豹','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/jb.png','abc'=>'J','uniacid'=>$uniacid],
            ['name'=>'捷途','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/jt.png','abc'=>'J','uniacid'=>$uniacid],
            ['name'=>'金杯','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/jbei.png','abc'=>'J','uniacid'=>$uniacid],
            ['name'=>'金龙','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/jlong.png','abc'=>'J','uniacid'=>$uniacid],
            ['name'=>'九龙','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/jiul.png','abc'=>'J','uniacid'=>$uniacid],
            ['name'=>'君马','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/jm.png','abc'=>'J','uniacid'=>$uniacid],

            ['name'=>'卡升','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/ks.png','abc'=>'K','uniacid'=>$uniacid],
            ['name'=>'卡威','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/kv.png','abc'=>'K','uniacid'=>$uniacid],
            ['name'=>'凯迪拉克','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/kdlk.png','abc'=>'K','uniacid'=>$uniacid],
            ['name'=>'凯瑞','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/kr.png','abc'=>'K','uniacid'=>$uniacid],
            ['name'=>'凯翼','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/ky.png','abc'=>'K','uniacid'=>$uniacid],
            ['name'=>'康迪全球鹰','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/kd.png','abc'=>'K','uniacid'=>$uniacid],
            ['name'=>'克莱斯特','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/klsl.png','abc'=>'K','uniacid'=>$uniacid],

            ['name'=>'兰博基尼','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/lbjn.png','abc'=>'L','uniacid'=>$uniacid],
            ['name'=>'劳伦士','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/lls.png','abc'=>'L','uniacid'=>$uniacid],
            ['name'=>'劳斯莱斯','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/lsls.png','abc'=>'L','uniacid'=>$uniacid],
            ['name'=>'雷克萨斯','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/lkss.png','abc'=>'L','uniacid'=>$uniacid],
            ['name'=>'雷诺','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/ln.png','abc'=>'L','uniacid'=>$uniacid],
            ['name'=>'力帆','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/lf.png','abc'=>'L','uniacid'=>$uniacid],
            ['name'=>'莲花','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/lianh.png','abc'=>'L','uniacid'=>$uniacid],
            ['name'=>'猎豹','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/lieb.png','abc'=>'L','uniacid'=>$uniacid],
            ['name'=>'林肯','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/link.png','abc'=>'L','uniacid'=>$uniacid],
            ['name'=>'铃木','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/lingm.png','abc'=>'L','uniacid'=>$uniacid],
            ['name'=>'零跑汽车','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/lingp.png','abc'=>'L','uniacid'=>$uniacid],
            ['name'=>'领克','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/lingk.png','abc'=>'L','uniacid'=>$uniacid],
            ['name'=>'陆风','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/luf.png','abc'=>'L','uniacid'=>$uniacid],
            ['name'=>'路虎','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/luh.png','abc'=>'L','uniacid'=>$uniacid],
            ['name'=>'路特斯','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/lts.png','abc'=>'L','uniacid'=>$uniacid],

            ['name'=>'MINI','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/MINI.png','abc'=>'M','uniacid'=>$uniacid],
            ['name'=>'马自达','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/mzd.png','abc'=>'M','uniacid'=>$uniacid],
            ['name'=>'玛莎拉蒂','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/msll.png','abc'=>'M','uniacid'=>$uniacid],
            ['name'=>'迈巴赫','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/mbh.png','abc'=>'M','uniacid'=>$uniacid],
            ['name'=>'迈凯伦','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/mkl.png','abc'=>'M','uniacid'=>$uniacid],
            ['name'=>'名爵','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/mj.png','abc'=>'M','uniacid'=>$uniacid],

            ['name'=>'纳智捷','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/nzj.png','abc'=>'N','uniacid'=>$uniacid],

            ['name'=>'讴歌','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/og.png','abc'=>'O','uniacid'=>$uniacid],
            ['name'=>'欧宝','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/ob.png','abc'=>'O','uniacid'=>$uniacid],
            ['name'=>'欧尚','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/os.png','abc'=>'O','uniacid'=>$uniacid],

            ['name'=>'Polestar','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/Polestar.png','abc'=>'P','uniacid'=>$uniacid],

            ['name'=>'奇点','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/qd.png','abc'=>'Q','uniacid'=>$uniacid],
            ['name'=>'奇瑞','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/qr.png','abc'=>'Q','uniacid'=>$uniacid],
            ['name'=>'启辰','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/qc.png','abc'=>'Q','uniacid'=>$uniacid],
            ['name'=>'起亚','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/qy.png','abc'=>'Q','uniacid'=>$uniacid],
            ['name'=>'前途','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/qt.png','abc'=>'Q','uniacid'=>$uniacid],
            ['name'=>'庆铃','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/ql.png','abc'=>'Q','uniacid'=>$uniacid],

            ['name'=>'日产','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/rc.png','abc'=>'R','uniacid'=>$uniacid],
            ['name'=>'荣威','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/rv.png','abc'=>'R','uniacid'=>$uniacid],

            ['name'=>'smart','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/smart.png','abc'=>'','uniacid'=>$uniacid],
            ['name'=>'SWM斯威','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/sv.png','abc'=>'','uniacid'=>$uniacid],
            ['name'=>'三菱','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/sl.png','abc'=>'','uniacid'=>$uniacid],
            ['name'=>'山姆','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/sm.png','abc'=>'','uniacid'=>$uniacid],
            ['name'=>'上汽大通','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/sqdt.png','abc'=>'','uniacid'=>$uniacid],
            ['name'=>'双龙','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/slong.png','abc'=>'','uniacid'=>$uniacid],
            ['name'=>'思皓','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/shao.png','abc'=>'','uniacid'=>$uniacid],
            ['name'=>'斯巴鲁','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/sbl.png','abc'=>'','uniacid'=>$uniacid],
            ['name'=>'斯柯达','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/skd.png','abc'=>'','uniacid'=>$uniacid],

            ['name'=>'特斯拉','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/tsl.png','abc'=>'T','uniacid'=>$uniacid],
            ['name'=>'腾势','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/ts.png','abc'=>'T','uniacid'=>$uniacid],

            ['name'=>'WEY','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/WEY.png','abc'=>'W','uniacid'=>$uniacid],
            ['name'=>'威马汽车','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/wmqc.png','abc'=>'W','uniacid'=>$uniacid],
            ['name'=>'潍柴英致','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/wcyz.png','abc'=>'W','uniacid'=>$uniacid],
            ['name'=>'蔚来','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/wl.png','abc'=>'W','uniacid'=>$uniacid],
            ['name'=>'沃尔沃','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/wew.png','abc'=>'W','uniacid'=>$uniacid],
            ['name'=>'五菱','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/wling.png','abc'=>'W','uniacid'=>$uniacid],
            ['name'=>'五十铃','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/wsl.png','abc'=>'W','uniacid'=>$uniacid],

            ['name'=>'现代','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/xd.png','abc'=>'X','uniacid'=>$uniacid],
            ['name'=>'小鹏汽车','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/xpqc.png','abc'=>'X','uniacid'=>$uniacid],
            ['name'=>'星驰','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/xc.png','abc'=>'X','uniacid'=>$uniacid],
            ['name'=>'雪佛兰','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/xfl.png','abc'=>'X','uniacid'=>$uniacid],
            ['name'=>'雪铁龙','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/xtl.png','abc'=>'X','uniacid'=>$uniacid],

            ['name'=>'野马','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/ym.png','abc'=>'Y','uniacid'=>$uniacid],
            ['name'=>'一汽','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/yq.png','abc'=>'Y','uniacid'=>$uniacid],
            ['name'=>'依维柯','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/yvk.png','abc'=>'Y','uniacid'=>$uniacid],
            ['name'=>'英菲尼迪','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/yfnd.png','abc'=>'Y','uniacid'=>$uniacid],
            ['name'=>'驭胜','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/ysheng.png','abc'=>'Y','uniacid'=>$uniacid],
            ['name'=>'裕路','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/yl.png','abc'=>'Y','uniacid'=>$uniacid],
            ['name'=>'云度','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/yd.png','abc'=>'Y','uniacid'=>$uniacid],

            ['name'=>'之诺','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/zn.png','abc'=>'Z','uniacid'=>$uniacid],
            ['name'=>'知豆','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/zd.png','abc'=>'Z','uniacid'=>$uniacid],
            ['name'=>'中华','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/zh.png','abc'=>'Z','uniacid'=>$uniacid],
            ['name'=>'中兴','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/zx.png','abc'=>'Z','uniacid'=>$uniacid],
            ['name'=>'众泰','brand_icon'=>$_W['siteroot'].'/addons/monai_market/static/carlog/zt.png','abc'=>'Z','uniacid'=>$uniacid],
        );
        if(!empty($_GPC['daoru']))
        {
            foreach ($car_arr as $v)
            {
                pdo_insert('monai_market_brand',$v);
            }
            $this->success('导入成功','producttype/type');
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

     /*

    * 获取首字母

     */

    public function getFirstCharter($str)

    {

        if (empty($str)) {
            return '';
        }
        if($str == '讴歌') return 'O';

        $fchar = ord($str{0});

        if ($fchar >= ord('A') && $fchar <= ord('z')) return strtoupper($str{0});

        $s1 = iconv('UTF-8', 'gb2312', $str);

        $s2 = iconv('gb2312', 'UTF-8', $s1);

        $s = $s2 == $str ? $s1 : $str;

        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;

        if ($asc >= -20319 && $asc <= -20284) return 'A';

        if ($asc >= -20283 && $asc <= -19776) return 'B';

        if ($asc >= -19775 && $asc <= -19219) return 'C';

        if ($asc >= -19218 && $asc <= -18711) return 'D';

        if ($asc >= -18710 && $asc <= -18527) return 'E';

        if ($asc >= -18526 && $asc <= -18240) return 'F';

        if ($asc >= -18239 && $asc <= -17923) return 'G';

        if ($asc >= -17922 && $asc <= -17418) return 'H';

        if ($asc >= -17417 && $asc <= -16475) return 'J';

        if ($asc >= -16474 && $asc <= -16213) return 'K';

        if ($asc >= -16212 && $asc <= -15641) return 'L';

        if ($asc >= -15640 && $asc <= -15166) return 'M';

        if ($asc >= -15165 && $asc <= -14923) return 'N';

        if ($asc >= -14922 && $asc <= -14915) return 'O';

        if ($asc >= -14914 && $asc <= -14631) return 'P';

        if ($asc >= -14630 && $asc <= -14150) return 'Q';

        if ($asc >= -14149 && $asc <= -14091) return 'R';

        if ($asc >= -14090 && $asc <= -13319) return 'S';

        if ($asc >= -13318 && $asc <= -12839) return 'T';

        if ($asc >= -12838 && $asc <= -12557) return 'W';

        if ($asc >= -12556 && $asc <= -11848) return 'X';

        if ($asc >= -11847 && $asc <= -11056) return 'Y';

        if ($asc >= -11055 && $asc <= -10247) return 'Z';

        return null;

    }

}

?>