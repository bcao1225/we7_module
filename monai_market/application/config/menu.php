<?php
return array(
	'companyset'       => array(
        'title'    => '设置',
        'subtitle' => '设置',
        'route' => 'companyset.index.index',
        'icon'     => 'fa fa-gear',
        'items'    => array(
            array('title' => '公司信息', 'route' => 'index.index','desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '短信设置', 'route' => 'index.message','desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '版权设置', 'route' => 'index.copyright', 'desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '启动广告', 'route' => 'index.start', 'desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '小程序跳转', 'route' => 'index.jump_app', 'desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '标题设置', 'route' => 'index.title_set', 'desc' => '','icon'=>'fa fa-list-alt'),
        ),
    ),
	'slide'      => array(
		'title'    => '轮播',
		'subtitle' => '轮播管理',
		'icon'     => 'fa fa-picture-o',
	    'route' => 'slide.index.slide',
		'items'    => array(
            array('title' => '轮播列表', 'route' => 'index.slide', 'desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '添加轮播', 'route' => 'index.slide_add', 'desc' => '','icon'=>'fa fa-list-alt'),
			)
		),
    'product'      => array(
        'title'    => '产品',
        'subtitle' => '产品管理',
        'icon'     => 'fa  fa-list-ul',
        'route' => 'product.index.product',
        'items'    => array(
            array('title' => '产品分类', 'route' => 'index.type', 'desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '添加分类', 'route' => 'index.type_add', 'desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '产品列表', 'route' => 'index.product', 'desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '添加产品', 'route' => 'index.product_add', 'desc' => '','icon'=>'fa fa-list-alt')
            )
       ),
	'new'      => array(
		'title'    => '资讯',
		'subtitle' => '资讯管理',
		'icon'     => 'fa fa-coffee',
	    'route' => 'new.index.type',
		'items'    => array(
            array('title' => '资讯分类', 'route' => 'index.type', 'desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '资讯列表', 'route' => 'index.index', 'desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '添加资讯', 'route' => 'index.new_add', 'desc' => '','icon'=>'fa fa-list-alt'),
			)
		),
    'appoint'      => array(
        'title'    => '预约',
        'subtitle' => '预约管理',
        'icon'     => 'fa fa-calendar',
        'route' => 'appoint.index.index',
        'items'    => array(
            array('title' => '预约列表', 'route' => 'index.index', 'desc' => '','icon'=>'fa fa-list-alt'),
            array('title' => '预约设置', 'route' => 'index.set', 'desc' => '','icon'=>'fa fa-list-alt'),
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
    'other'      => array(
        'title'    => '模板',
        'subtitle' => '自定义',
        'icon'     => 'fa  fa-th-large',
        'route' => 'other.index.bottom',
        'items'    => array(
            array('title' => '底部导航', 'route' => 'index.bottom', 'desc' => '','icon'=>'fa fa-list-alt'),
            
            /*array('title' => '首页模板', 'route' => 'index.modules', 'desc' => '','icon'=>'fa fa-list-alt'),*/
            /*array('title' => '客服中心', 'route' => 'index.service', 'desc' => '','icon'=>'fa fa-list-alt'),*/
            
            array('title' => '选择模板', 'route' => 'index.template_list', 'desc' => '','icon'=>'fa fa-list-alt'),
            
            )
        ),
    'user'       => array(
        'title'    => '权限管理',
        'subtitle' => '权限管理',
        'icon'     => 'fa fa-key red',
        'route' => 'user.role',
        'items'    => array(
            array('title' => '添加角色', 'route' => 'role.add', 'desc' => '店铺配送方式管理','icon'=>'fa fa-list-alt'),
            array('title' => '角色列表', 'route' => 'role', 'desc' => '店铺公告管理','icon'=>'fa fa-list-alt'),
            array('title' => '操作员管理', 'route' => 'user', 'desc' => '店铺商品评价管理','icon'=>'fa fa-list-alt'),
            array('title' => '添加操作员', 'route' => 'user.add', 'desc' => '店铺退货地址管理','icon'=>'fa fa-list-alt')
        ),
    ),
	);