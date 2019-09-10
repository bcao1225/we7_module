<?php

return array(

	'companyset'       => array(

        'title'    => '设置',

        'subtitle' => '设置',

        'route' => 'companyset.index',

        'icon'     => 'fa fa-gear',

        'items'    => array(

            array('title' => '基础设置', 'route' => 'index','desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '推广设置', 'route' => 'sale_set','desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '流量主设置', 'route' => 'flow_set','desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '入驻设置', 'route' => 'enter_set','desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '违章设置', 'route' => 'peccancy_set','desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '消息模板', 'route' => 'message','desc' => '','icon'=>'fa fa-list-alt'),
//            array('title' => '广告位设置', 'route' => 'adv_set','desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '群列表设置', 'route' => 'group_list','desc' => '','icon'=>'fa fa-list-alt'),
//	        array('title' => '广告位微信号', 'route' => 'slide','desc' => '','icon'=>'fa fa-list-alt'),
//	        array('title' => '广告位微信号', 'route' => 'slide_add','desc' => '','icon'=>'fa fa-list-alt'),
        ),

    ),

	'slide'      => array(

		'title'    => '轮播',

		'subtitle' => '轮播管理',

		'icon'     => 'fa fa-picture-o',

	    'route' => 'slide.index.slide',

		'items'    => array(
                array('title' => '轮播列表', 'route' => 'index.slide','desc' => '','icon'=>'fa fa-list-alt'),
                array('title' => '添加轮播', 'route' => 'index.slide_add','desc' => '','icon'=>'fa fa-list-alt'),
			)

		),

	'producttype'      => array(

        'title'    => '品牌',

        'subtitle' => '品牌管理',

        'icon'     => 'fa  fa-list-ul',

        'route' => 'producttype.type',

        'items'    => array(

                array('title' => '品牌列表', 'route' => 'type','desc' => '','icon'=>'fa fa-list-alt'),
                array('title' => '添加品牌', 'route' => 'type_add','desc' => '','icon'=>'fa fa-list-alt'),
                array('title' => '一键导入', 'route' => 'shuju_add_all','desc' => '','icon'=>'fa fa-list-alt'),

            )

       ),

    'productclass'      => array(

        'title'    => '类型',

        'subtitle' => '类型管理',

        'icon'     => 'fa  fa-list-ul',

        'route' => 'productclass.class_list',

        'items'    => array(
                array('title' => '类型列表', 'route' => 'class_list','desc' => '','icon'=>'fa fa-list-alt'),
                array('title' => '添加类型', 'route' => 'class_add','desc' => '','icon'=>'fa fa-list-alt'),
            

            )

       ),

    'product'      => array(

        'title'    => '车辆',

        'subtitle' => '车辆管理',

        'icon'     => 'fa  fa-list-ul',

        'route' => 'product.index.product',

        'items'    => array(

            array('title' => '车辆列表', 'route' => 'index.product','desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '添加车辆', 'route' => 'index.product_add','desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '更新车辆省市区', 'route' => 'index.province_save','desc' => '','icon'=>'fa fa-list-alt'),
            /*array('title' => '支付凭证', 'route' => 'index.payorder','desc' => '','icon'=>'fa fa-list-alt'),*/
        )

    ),

    'notice'      => array(

        'title'    => '公告',

        'subtitle' => '公告管理',

        'icon'     => 'fa fa-twitter-square',

        'route' => 'notice.index.notice',

        'items'    => array(

            array('title' => '公告列表', 'route' => 'index.notice', 'desc' => '','icon'=>'fa fa-list-alt'),

            array('title' => '添加公告', 'route' => 'index.notice_add', 'desc' => '','icon'=>'fa fa-list-alt'),

        )

    ),

    'member'      => array(

        'title'    => '会员',

        'subtitle' => '会员管理',

        'icon'     => 'fa fa-group',

        'route' => 'member.index',

        'items'    => array(

            array('title' => '会员列表', 'route' => 'index', 'desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '支付凭证', 'route' => 'payorder','desc' => '','icon'=>'fa fa-list-alt'),
        )

    ),

    'money'      => array(

        'title'    => '财务',

        'subtitle' => '资金记录',

        'icon'     => 'fa fa-cny',

        'route' => 'money.index',

        'items'    => array(

            array('title' => '资金记录', 'route' => 'index', 'desc' => '','icon'=>'fa fa-list-alt'),

        )

    ),

    'feedback'      => array(

        'title'    => '消息',

        'subtitle' => '消息记录',

        'icon'     => 'fa fa-comment',

        'route' => 'feedback.index',

        'items'    => array(

            array('title' => '举报记录', 'route' => 'index', 'desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '留言记录', 'route' => 'words', 'desc' => '','icon'=>'fa fa-list-alt'),

        )

    ),


    'carry'      => array(

        'title'    => '提现',

        'subtitle' => '提现管理',

        'icon'     => 'fa  fa-jpy',

        'route' => 'carry.index',

        'items'    => array(

            array('title' => '提现申请', 'route' => 'index', 'desc' => '','icon'=>'fa fa-list-alt'),

        )

    ),
    'assess'      => array(

        'title'    => '求购',

        'subtitle' => '求购管理',

        'icon'     => 'fa fa-pie-chart',

        'route' => 'assess.index',

        'items'    => array(
            array('title' => '求购列表', 'route' => 'index', 'desc' => '','icon'=>'fa fa-list-alt'),
//            array('title' => '贷款申请', 'route' => 'loan', 'desc' => '','icon'=>'fa fa-list-alt'),
        )

    ),
    'ensure'      => array(

        'title'    => '服务',

        'subtitle' => '服务保障',

        'icon'     => 'fa fa-tty',

        'route' => 'ensure.index',

        'items'    => array(

            array('title' => '服务保障', 'route' => 'index', 'desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '添加服务', 'route' => 'index_add', 'desc' => '','icon'=>'fa fa-list-alt'),

        )

    ),
    'service'      => array(

        'title'    => '客服',

        'subtitle' => '客服',

        'icon'     => 'fa fa-comments-o',

        'route' => 'service.index',

        'items'    => array(

        )

    ),
    'user'       => array(

        'title'    => '权限管理',

        'subtitle' => '权限管理',

        'icon'     => 'fa fa-key red',

        'route' => 'user.role',

        'items'    => array(

            array('title' => '添加角色', 'route' => 'role.add', 'desc' => '店铺配送方式管理','icon'=>'fa fa-list-alt'),

            array('title' => '角色列表', 'route' => 'role.index', 'desc' => '店铺公告管理','icon'=>'fa fa-list-alt'),

            array('title' => '操作员管理', 'route' => 'user.index', 'desc' => '店铺商品评价管理','icon'=>'fa fa-list-alt'),

            array('title' => '添加操作员', 'route' => 'user.add', 'desc' => '店铺退货地址管理','icon'=>'fa fa-list-alt')

        ),

    ),

    'part'      => array(

        'title'    => '汽配',

        'subtitle' => '订购管理',

        'icon'     => 'fa  fa-list-ul',

        'route' => 'part.order_list',

        'items'    => array(

            array('title' => '订购订单', 'route' => 'order_list','desc' => '','icon'=>'fa fa-list-alt'),

        )

    ),

);