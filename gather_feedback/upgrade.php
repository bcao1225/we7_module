<?php

//1.1.0
if (!pdo_fieldexists('ims_gather_feedback_children_question', 'select_num')) {
    pdo_query("ALTER TABLE ims_gather_feedback_children_question ADD `select_num` INT(11) NULL DEFAULT '0';");
}

//1.2.0
if(!pdo_fieldexists('ims_gather_feedback_system_setting', 'share_title')){
    pdo_query("ALTER TABLE ims_gather_feedback_system_setting ADD `share_title` varchar(255) NOT NULL DEFAULT '';");
    pdo_query("ALTER TABLE ims_gather_feedback_system_setting ADD `share_desc` varchar(1000) NOT NULL DEFAULT '';");
    pdo_query("ALTER TABLE ims_gather_feedback_system_setting ADD `share_img` varchar(255) NOT NULL DEFAULT '';");
}

//1.2.1
if(!pdo_fieldexists('ims_gather_feedback_children_question', 'select_sort')){
    pdo_query("ALTER TABLE ims_gather_feedback_children_question ADD `select_sort` int(11) NOT NULL DEFAULT 0;");
}