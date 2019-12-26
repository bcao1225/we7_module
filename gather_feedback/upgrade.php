<?php

if (!pdo_fieldexists('ims_gather_feedback_system_setting', 'show_total')) {
    pdo_query("ALTER TABLE ims_gather_feedback_system_setting ADD `show_total` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否向用户展示统计结果，0不展示，1展示';");
}

/*2.3.0*/
if (!pdo_fieldexists('ims_gather_feedback_system_setting', 'copyright')) {
    pdo_query("ALTER TABLE ims_gather_feedback_system_setting ADD `copyright` varchar(255) NOT NULL DEFAULT '' COMMENT '底部版权';");
}



