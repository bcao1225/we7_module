<?php
/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2018/5/7

 * Time: 15:07

 */

if (!(defined('IN_IA')))
{
    exit('Access Denied');
}
class Api_Me extends WeModuleWxapp
{

    /**
     * 获取用户的充值记录
     */
    public function getPayRecord(){
        global $_W,$_GPC;
        $list = pdo_fetchall("SELECT * FROM ims_monai_market_finance WHERE uid=:uid ORDER BY create_time DESC",[":uid"=>$_GPC['uid']]);
        $this->result(0,['获取充值记录成功'],$list);
    }

    /**
     * 个人中心
     */
    public function detail(){

        global $_GPC, $_W;

        $detail = pdo_get('monai_market_member',['uniacid'=>$_W['uniacid'],'uid' => $_GPC['uid']]);

        $kf_info = pdo_get('monai_market_saleinfo',['uniacid'=>$_W['uniacid']]);
        $vip = pdo_get('monai_market_info',['uniacid'=>$_W['uniacid']]);;
        if(!$detail){

            $data = [

                'uniacid'=>$_W['uniacid'],

                'uid' => $_GPC['uid'],

                'head_image'=> $_GPC['head_image'],

                'create_time' => $_SERVER['REQUEST_TIME'],

                'nickname' => $_GPC['nickname'],

            ];

            pdo_insert('monai_market_member',$data);

            $detail = pdo_get('monai_market_member',['uniacid'=>$_W['uniacid'],'uid' => $_GPC['uid']]);

        }else{
            if(empty($detail['nickname'])){
                pdo_update('monai_market_member',array('nickname'=>$_GPC['nickname']),array('user_id'=>$detail['user_id']));
            }
        }

        $detail['is_member'] = false;//判断 是否是会员
        if($detail['is_vip']==1 && $detail['end_time']>time()){
            $detail['is_member'] = true;
            $detail['store_img']= tomedia($detail['store_img']);
            $detail['end_time'] = date("Y-m-d H:i:s",$detail['end_time']) ;
        }
        $sale = pdo_get('monai_market_saleinfo',['uniacid'=>$_W['uniacid']]);
        if($sale && $sale['status'] ==1){
            $detail['sale_status'] = 1;

        }
        if($detail['qrcode']){
            $detail['qrcode'] = tomedia($detail['qrcode']);
        }

        // 关注用户总数

        $gtotal= pdo_fetch("SELECT count(*) as total FROM ".tablename('monai_market_follow_logs')." WHERE uid = {$_GPC['uid']} and `type`=1 and uniacid={$_W['uniacid']} and status = 1 LIMIT 1");

        // 粉丝总数

        $ftotal= pdo_fetch("SELECT count(*) as total FROM ".tablename('monai_market_follow_logs')." WHERE ucar_id = {$_GPC['uid']} and `type`=1 and uniacid={$_W['uniacid']}  and status = 1 LIMIT 1");

        // 发布总数

        $fbtotal= pdo_fetch("SELECT count(*) as total FROM ".tablename('monai_market_car_detail')." WHERE uid = {$_GPC['uid']} and uniacid={$_W['uniacid']} and delete_time=0 LIMIT 1");

        //积分数
        $query  = "SELECT SUM(brokerage) AS brokerage
                    FROM  ".tablename('monai_market_account')."
                    WHERE parent_uid={$_GPC['uid']}  AND uniacid={$_W['uniacid']}";
        $brokerage = pdo_fetchcolumn($query);
        $brokerage = $brokerage?$brokerage:0;

        $where=" mmfl.uid=".$_GPC['uid'].' AND mmfl.type=2 AND mmfl.status=1 AND mmfl.uniacid='.$_W['uniacid'].' AND mmcd.id!=\'\'';

        $sql='SELECT count(*) as total FROM'.tablename('monai_market_follow_logs')

            .' AS mmfl LEFT JOIN '.tablename('monai_market_car_detail').' AS mmcd ON mmfl.ucar_id=mmcd.id  WHERE '.$where.'  ORDER BY mmcd.id DESC ';

        $stotal=pdo_fetch($sql);

        $weizhang = pdo_get('monai_market_info',array('uniacid'=>$_W['uniacid']));


        $result = [

            'user' => $detail,

            'stotal' =>$stotal['total'] ,

            'gtotal' =>$gtotal['total'] ,

            'ftotal' =>$ftotal['total'] ,

            'jifen' => $brokerage,

            'fbtotal' =>$fbtotal['total'] ,
            'weizhang_open'=>empty($weizhang['juhe_appkey'])?0:1,
            'qipei_open'=>$weizhang['qipei_open'],
            'kf_info' =>$kf_info["kf_info"],
            'kf_qrcode' =>tomedia($kf_info["kf_qrcode"]),
            'vip_words'=> $vip['vip_words'],
            'phone' => $vip['phone'],

            'pop_con' => $vip['pop_con'],
            'pop_bgimg' => tomedia($vip['pop_bgimg']),
            'is_vipgroup' => $vip['is_vipgroup'],
        ];



        return $this->result(0, '', $result);

    }
    /**
     * 扫码绑定推荐关系
     */
    public function binding(){
        global $_GPC, $_W;
        $detail = pdo_get('monai_market_member',['uniacid'=>$_W['uniacid'],'uid' => $_GPC['uid']]);
        if(!$detail){
            $data = [
                'uniacid'=>$_W['uniacid'],
                'uid' => $_GPC['uid'],
                'head_image'=> $_GPC['head_image'],
                'create_time' => $_SERVER['REQUEST_TIME'],
                'nickname' => $_GPC['nickname'],
                'parent_uid' => $_GPC['parent_uid'],
            ];
            pdo_insert('monai_market_member',$data);
        }
        return $this->result(0, '', '');
    }


    /**

     * 用户信息

     */

    public function info(){
        global $_GPC, $_W;
        $detail = pdo_get('monai_market_member',['uniacid'=>$_W['uniacid'],'uid' => $_GPC['uid']]);
        if($detail){
            $detail['store_img'] = tomedia($detail['store_img']);
            if($detail['is_vip'] && $detail['end_time'] > $_SERVER['REQUEST_TIME'] ){
                $detail['is_vip'] = 1;
            }else{
                $detail['is_vip'] = 2;
            }
        }
        return $this->result(0, '', $detail);

    }



    /**

     * 修改用户信息

     */

    public function updateinfo(){
        global $_GPC, $_W;
        $params = [
            'nickname' => $_GPC['nickname'],
            'phone' => $_GPC['phone'],
            'address' => $_GPC['address'],
            'store_img' => $_GPC['store_img'],
            'head_image' => $_GPC['head_image']
        ];
        $result = pdo_update('monai_market_member',$params,['uniacid'=>$_W['uniacid'],'uid' => $_GPC['uid']]);
        return $this->result(0, '', $result);
    }



    /**

     * 粉丝

     */

    public function fans(){

        global $_GPC, $_W;

        $pageSize = 10;

        $page = $_GPC['page'] ?: 0;

        $beginPage = $page*$pageSize;

        if($_GPC['type'] == 1){

            $query = "SELECT mfl.is_follow,mfl.status,mfl.uid AS fans_id,mm.nickname,mm.`head_image`

                    FROM  ".tablename('monai_market_follow_logs')."   mfl

                    LEFT JOIN ".tablename('monai_market_member')."  mm ON mm.uid=mfl.uid

                    WHERE mfl.uniacid={$_W['uniacid']}  and mfl.type = 1 AND mfl.ucar_id= {$_GPC['uid']} and mfl.status = 1   GROUP BY mfl.id LIMIT {$beginPage},{$pageSize} ";

        }elseif($_GPC['type'] == 2){

            $query = "SELECT mfl.is_follow,mfl.status,mfl.uid,mfl.ucar_id as fans_id ,mm.nickname,mm.`head_image`

                    FROM ".tablename('monai_market_follow_logs')." mfl

                    LEFT JOIN ".tablename('monai_market_member')."  mm ON mm.uid=mfl.ucar_id

                    WHERE mfl.uniacid={$_W['uniacid']}  and mfl.type = 1 AND mfl.uid= {$_GPC['uid']} and mfl.status = 1  GROUP BY mfl.id  LIMIT {$beginPage},{$pageSize} ";

        }



        $list = pdo_fetchall($query);

        if($list){

            return $this->result(0, '', $list);

        }else{

            return $this->result(0, '', false);

        }



    }

    /**

     * 关注

     */

    public function follow(){

        global $_GPC, $_W;

        $params = [

            'uniacid'=>$_W['uniacid'],

            'uid' => $_GPC['uid'],

            'type'=>$_GPC['type'],

            'ucar_id'=>$_GPC['ucar_id']

        ];

        $detail = pdo_get('monai_market_follow_logs',$params);

        if($detail){

            $status = $detail['status'] == 1 ? 2 : 1;

            if($_GPC['type'] == 1){

                $info = pdo_get('monai_market_follow_logs',['uniacid'=>$_W['uniacid'], 'uid' => $_GPC['ucar_id'],

                    'type'=>1, 'ucar_id'=>$_GPC['uid'],'status' => '1'

                ]);

                if($info){

                    $is_follow = $status==1 ? 1: 2 ;

                }

            }

            pdo_update('monai_market_follow_logs',['is_follow'=> $is_follow ?: 2],['uniacid'=>$_W['uniacid'], 'uid' => $_GPC['ucar_id'],

                'type'=>1, 'ucar_id'=>$_GPC['uid'],'status' => '1'

            ]);

            pdo_update('monai_market_follow_logs',['status'=> $status],$params);

        }else{

            if($_GPC['type'] == 1){

                $info = pdo_get('monai_market_follow_logs',['uniacid'=>$_W['uniacid'], 'uid' => $_GPC['ucar_id'],

                    'type'=>1, 'ucar_id'=>$_GPC['uid'],'status' => '1'

                ]);

                if($info){

                    $params['is_follow'] = 1;

                }

            }

            pdo_insert('monai_market_follow_logs',$params);

            $status = 1;

        }

        return $this->result(0, '', $status);

    }

    /**
     * 支付凭证信息
     */
    public function payorder(){
        global $_GPC, $_W;
        $uid = $_GPC['uid'];
        $uniacid = $_W['uniacid'];
        $info = pdo_get('monai_market_payorder',array('uid'=>$uid,'uniacid'=>$uniacid));
        if($info){
            $info['img'] = tomedia($info['img']);
        }
        return $this->result(0, '', $info);
    }
    /**
     *  上传支付凭证
     */
    public function uppayorder(){
        global $_GPC, $_W;
        $uid = $_GPC['uid'];
        $uniacid = $_W['uniacid'];
        $imgurl = $_GPC['payorder'];

        $info = pdo_get('monai_market_payorder',array('uid'=>$uid,'uniacid'=>$uniacid));
        $data = array(
            'img' => $imgurl,
            'status' => 0,
            'createtime' => time()
        );
        if($info){
            pdo_update('monai_market_payorder',$data,array('id'=>$info['id']));
        }else{
            $data['uniacid'] = $uniacid;
            $data['uid'] = $uid;
            pdo_insert('monai_market_payorder',$data);
        }
        return $this->result(0, '','');
    }

}
