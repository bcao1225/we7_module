<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Web_Service extends Web_Base
{

    /*
    * 资金列表
     */
    public function index() 
	{
        include $this->template();
	}
}
?>