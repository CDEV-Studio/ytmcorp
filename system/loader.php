<?php
class Load {

	public static function model($name) {  
		if (file_exists ( $_SERVER['DOCUMENT_ROOT'].'/model/' . mb_strtolower($name) . '.php' )) {  
			require_once $_SERVER['DOCUMENT_ROOT'].'/model/' . mb_strtolower($name) . '.php';
			$model = new $name ();
			return $model;
		} else {
			
			exit;
		}
	}
 
}