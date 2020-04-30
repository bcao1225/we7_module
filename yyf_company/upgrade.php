<?php
$sql="CREATE TABLE IF NOT EXISTS `ims_yyf_company_adsense` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `ad_img` varchar(200) NOT NULL,
  `ad_url` varchar(50) NOT NULL,
  `ad_height` varchar(50) NOT NULL,
  `ad1_img` varchar(200) NOT NULL,
  `ad1_url` varchar(50) NOT NULL,
  `ad2_img` varchar(200) NOT NULL,
  `ad2_url` varchar(50) NOT NULL,
  `ad1_height` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_yyf_company_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `sortrank` int(11) NOT NULL,
  `type` int(2) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `isshow` int(2) NOT NULL,
  `content` text NOT NULL,
  `isshow_nav` int(11) NOT NULL,
  `show_num` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_yyf_company_form` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `t1name` varchar(100) NOT NULL,
  `t1full` int(2) NOT NULL,
  `t1show` int(2) NOT NULL,
  `t2name` varchar(100) NOT NULL,
  `t2full` int(2) NOT NULL,
  `t2show` int(2) NOT NULL,
  `t3name` varchar(100) NOT NULL,
  `t3full` int(2) NOT NULL,
  `t3show` int(2) NOT NULL,
  `t4name` varchar(100) NOT NULL,
  `t4full` int(2) NOT NULL,
  `t4show` int(2) NOT NULL,
  `rname` varchar(100) NOT NULL,
  `rvalue` varchar(200) NOT NULL,
  `rfull` int(2) NOT NULL,
  `rshow` int(2) NOT NULL,
  `cname` varchar(100) NOT NULL,
  `cvalue` varchar(200) NOT NULL,
  `cfull` int(2) NOT NULL,
  `cshow` int(2) NOT NULL,
  `aname` varchar(100) NOT NULL,
  `afull` int(2) NOT NULL,
  `ashow` int(2) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `catname` varchar(100) NOT NULL,
  `interval` varchar(100) NOT NULL,
  `successtext` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_yyf_company_formvalue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `t1name` varchar(100) NOT NULL,
  `t1value` varchar(200) NOT NULL,
  `t2name` varchar(100) NOT NULL,
  `t2value` varchar(200) NOT NULL,
  `t3name` varchar(100) NOT NULL,
  `t3value` varchar(200) NOT NULL,
  `t4name` varchar(100) NOT NULL,
  `t4value` varchar(200) NOT NULL,
  `rname` varchar(100) NOT NULL,
  `rvalue` varchar(200) NOT NULL,
  `cname` varchar(100) NOT NULL,
  `cvalue` varchar(200) NOT NULL,
  `aname` varchar(100) NOT NULL,
  `avalue` varchar(255) NOT NULL,
  `read` int(1) NOT NULL,
  `addtime` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_yyf_company_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `cid` int(1) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `addtime` int(10) NOT NULL,
  `content` text NOT NULL,
  `videosrc` varchar(255) NOT NULL,
  `sortrank` int(11) NOT NULL,
  `pageaddress` varchar(255) NOT NULL,
  `appid` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_yyf_company_slide` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `images` varchar(255) NOT NULL,
  `aid` int(11) NOT NULL,
  `sortrank` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_yyf_company_style` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `tcolor` varchar(100) NOT NULL,
  `slide_close` int(11) NOT NULL,
  `nav_close` int(11) NOT NULL,
  `notice_close` int(11) NOT NULL,
  `custom_close` int(11) NOT NULL,
  `contact_background` varchar(255) NOT NULL,
  `contact_name` varchar(100) NOT NULL,
  `contact_logo` varchar(255) NOT NULL,
  `horn` varchar(255) NOT NULL,
  `slide_height` varchar(50) NOT NULL,
  `nav_style` int(11) NOT NULL,
  `title_style` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_yyf_company_sysinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `notice` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `jing` varchar(20) NOT NULL,
  `wei` varchar(20) NOT NULL,
  `qq` varchar(50) NOT NULL,
  `address` varchar(200) NOT NULL,
  `email` varchar(80) NOT NULL,
  `copyright` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `message_email` varchar(255) NOT NULL,
  `message_title` varchar(255) NOT NULL,
  `smtp_email` varchar(255) NOT NULL,
  `smtp_key` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_yyf_company_tabbar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `m1_name` varchar(255) NOT NULL,
  `m2_name` varchar(255) NOT NULL,
  `m3_name` varchar(255) NOT NULL,
  `m4_name` varchar(255) NOT NULL,
  `m1_path` varchar(255) NOT NULL,
  `m2_path` varchar(255) NOT NULL,
  `m3_path` varchar(255) NOT NULL,
  `m4_path` varchar(255) NOT NULL,
  `m1_img` varchar(255) NOT NULL,
  `m2_img` varchar(255) NOT NULL,
  `m3_img` varchar(255) NOT NULL,
  `m4_img` varchar(255) NOT NULL,
  `m1_selimg` varchar(255) NOT NULL,
  `m2_selimg` varchar(255) NOT NULL,
  `m3_selimg` varchar(255) NOT NULL,
  `m4_selimg` varchar(255) NOT NULL,
  `border_color` varchar(255) NOT NULL,
  `background_color` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `sel_color` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql);
if(!pdo_fieldexists('yyf_company_adsense',  'id')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_adsense')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('yyf_company_adsense',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_adsense')." ADD `uniacid` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_adsense',  'ad_img')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_adsense')." ADD `ad_img` varchar(200) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_adsense',  'ad_url')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_adsense')." ADD `ad_url` varchar(50) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_adsense',  'ad_height')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_adsense')." ADD `ad_height` varchar(50) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_adsense',  'ad1_img')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_adsense')." ADD `ad1_img` varchar(200) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_adsense',  'ad1_url')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_adsense')." ADD `ad1_url` varchar(50) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_adsense',  'ad2_img')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_adsense')." ADD `ad2_img` varchar(200) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_adsense',  'ad2_url')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_adsense')." ADD `ad2_url` varchar(50) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_adsense',  'ad1_height')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_adsense')." ADD `ad1_height` varchar(60) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_category',  'id')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_category')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('yyf_company_category',  'fid')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_category')." ADD `fid` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_category',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_category')." ADD `uniacid` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_category',  'name')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_category')." ADD `name` varchar(100) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_category',  'sortrank')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_category')." ADD `sortrank` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_category',  'type')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_category')." ADD `type` int(2) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_category',  'thumb')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_category')." ADD `thumb` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_category',  'isshow')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_category')." ADD `isshow` int(2) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_category',  'content')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_category')." ADD `content` text NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_category',  'isshow_nav')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_category')." ADD `isshow_nav` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_category',  'show_num')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_category')." ADD `show_num` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  'id')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('yyf_company_form',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `uniacid` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  't1name')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `t1name` varchar(100) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  't1full')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `t1full` int(2) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  't1show')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `t1show` int(2) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  't2name')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `t2name` varchar(100) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  't2full')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `t2full` int(2) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  't2show')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `t2show` int(2) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  't3name')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `t3name` varchar(100) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  't3full')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `t3full` int(2) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  't3show')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `t3show` int(2) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  't4name')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `t4name` varchar(100) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  't4full')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `t4full` int(2) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  't4show')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `t4show` int(2) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  'rname')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `rname` varchar(100) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  'rvalue')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `rvalue` varchar(200) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  'rfull')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `rfull` int(2) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  'rshow')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `rshow` int(2) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  'cname')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `cname` varchar(100) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  'cvalue')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `cvalue` varchar(200) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  'cfull')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `cfull` int(2) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  'cshow')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `cshow` int(2) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  'aname')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `aname` varchar(100) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  'afull')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `afull` int(2) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  'ashow')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `ashow` int(2) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  'desc')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `desc` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  'catname')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `catname` varchar(100) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  'interval')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `interval` varchar(100) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_form',  'successtext')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_form')." ADD `successtext` varchar(150) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_formvalue',  'id')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_formvalue')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('yyf_company_formvalue',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_formvalue')." ADD `uniacid` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_formvalue',  't1name')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_formvalue')." ADD `t1name` varchar(100) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_formvalue',  't1value')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_formvalue')." ADD `t1value` varchar(200) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_formvalue',  't2name')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_formvalue')." ADD `t2name` varchar(100) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_formvalue',  't2value')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_formvalue')." ADD `t2value` varchar(200) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_formvalue',  't3name')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_formvalue')." ADD `t3name` varchar(100) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_formvalue',  't3value')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_formvalue')." ADD `t3value` varchar(200) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_formvalue',  't4name')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_formvalue')." ADD `t4name` varchar(100) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_formvalue',  't4value')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_formvalue')." ADD `t4value` varchar(200) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_formvalue',  'rname')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_formvalue')." ADD `rname` varchar(100) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_formvalue',  'rvalue')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_formvalue')." ADD `rvalue` varchar(200) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_formvalue',  'cname')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_formvalue')." ADD `cname` varchar(100) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_formvalue',  'cvalue')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_formvalue')." ADD `cvalue` varchar(200) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_formvalue',  'aname')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_formvalue')." ADD `aname` varchar(100) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_formvalue',  'avalue')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_formvalue')." ADD `avalue` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_formvalue',  'read')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_formvalue')." ADD `read` int(1) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_formvalue',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_formvalue')." ADD `addtime` int(10) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_news',  'id')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_news')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('yyf_company_news',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_news')." ADD `uniacid` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_news',  'cid')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_news')." ADD `cid` int(1) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_news',  'thumb')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_news')." ADD `thumb` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_news',  'title')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_news')." ADD `title` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_news',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_news')." ADD `addtime` int(10) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_news',  'content')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_news')." ADD `content` text NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_news',  'videosrc')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_news')." ADD `videosrc` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_news',  'sortrank')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_news')." ADD `sortrank` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_news',  'pageaddress')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_news')." ADD `pageaddress` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_news',  'appid')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_news')." ADD `appid` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_slide',  'id')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_slide')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('yyf_company_slide',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_slide')." ADD `uniacid` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_slide',  'images')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_slide')." ADD `images` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_slide',  'aid')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_slide')." ADD `aid` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_slide',  'sortrank')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_slide')." ADD `sortrank` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_style',  'id')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_style')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('yyf_company_style',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_style')." ADD `uniacid` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_style',  'tcolor')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_style')." ADD `tcolor` varchar(100) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_style',  'slide_close')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_style')." ADD `slide_close` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_style',  'nav_close')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_style')." ADD `nav_close` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_style',  'notice_close')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_style')." ADD `notice_close` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_style',  'custom_close')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_style')." ADD `custom_close` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_style',  'contact_background')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_style')." ADD `contact_background` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_style',  'contact_name')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_style')." ADD `contact_name` varchar(100) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_style',  'contact_logo')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_style')." ADD `contact_logo` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_style',  'horn')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_style')." ADD `horn` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_style',  'slide_height')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_style')." ADD `slide_height` varchar(50) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_style',  'nav_style')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_style')." ADD `nav_style` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_style',  'title_style')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_style')." ADD `title_style` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_sysinfo',  'id')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_sysinfo')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('yyf_company_sysinfo',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_sysinfo')." ADD `uniacid` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_sysinfo',  'title')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_sysinfo')." ADD `title` varchar(150) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_sysinfo',  'notice')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_sysinfo')." ADD `notice` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_sysinfo',  'phone')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_sysinfo')." ADD `phone` varchar(15) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_sysinfo',  'jing')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_sysinfo')." ADD `jing` varchar(20) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_sysinfo',  'wei')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_sysinfo')." ADD `wei` varchar(20) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_sysinfo',  'qq')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_sysinfo')." ADD `qq` varchar(50) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_sysinfo',  'address')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_sysinfo')." ADD `address` varchar(200) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_sysinfo',  'email')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_sysinfo')." ADD `email` varchar(80) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_sysinfo',  'copyright')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_sysinfo')." ADD `copyright` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_sysinfo',  'name')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_sysinfo')." ADD `name` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_sysinfo',  'message_email')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_sysinfo')." ADD `message_email` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_sysinfo',  'message_title')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_sysinfo')." ADD `message_title` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_sysinfo',  'smtp_email')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_sysinfo')." ADD `smtp_email` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_sysinfo',  'smtp_key')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_sysinfo')." ADD `smtp_key` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'id')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `uniacid` int(11) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'm1_name')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `m1_name` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'm2_name')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `m2_name` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'm3_name')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `m3_name` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'm4_name')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `m4_name` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'm1_path')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `m1_path` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'm2_path')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `m2_path` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'm3_path')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `m3_path` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'm4_path')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `m4_path` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'm1_img')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `m1_img` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'm2_img')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `m2_img` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'm3_img')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `m3_img` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'm4_img')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `m4_img` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'm1_selimg')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `m1_selimg` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'm2_selimg')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `m2_selimg` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'm3_selimg')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `m3_selimg` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'm4_selimg')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `m4_selimg` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'border_color')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `border_color` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'background_color')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `background_color` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'color')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `color` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('yyf_company_tabbar',  'sel_color')) {
	pdo_query("ALTER TABLE ".tablename('yyf_company_tabbar')." ADD `sel_color` varchar(255) NOT NULL;");
}

?>