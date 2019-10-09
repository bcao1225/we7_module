<?php

require_once __DIR__.'/WebRoot.php';

class Question extends WebRoot{
    /**
     * 所有问题页面
     */
    public function all_page(){
        include $this->template('question_all');
    }
}