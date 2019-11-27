<?php

//1.1.0
if (!pdo_fieldexists('ims_gather_feedback_children_question', 'select_num')) {
    pdo_query("ALTER TABLE ims_gather_feedback_children_question ADD `select_num` INT(11) NULL DEFAULT '0';");
}