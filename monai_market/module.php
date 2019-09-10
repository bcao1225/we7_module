<?php

if (!defined('IN_IA')) {

	exit('Access Denied');

}

define('MODEL_NAME','monai_market');

require_once IA_ROOT . '/addons/'.MODEL_NAME.'/application/bootstrap.php';

class Monai_marketModule extends WeModule

{

	public function welcomeDisplay()

	{

		header('location: ' . webUrl());

		exit();

	}

}





?>