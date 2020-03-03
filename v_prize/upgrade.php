<?php
//1.1
if(!pdo_fieldexists('ims_v_prize_system_setting', 'company_img')) {
    pdo_query("ALTER TABLE "."ims_v_prize_system_setting"." ADD `company_img` varchar(255) NOT NULL DEFAULT '';");
    pdo_query("ALTER TABLE "."ims_v_prize_system_setting"." ADD `company_particulars` text NOT NULL;");
}