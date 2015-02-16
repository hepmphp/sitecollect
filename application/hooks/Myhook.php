<?php 
class Myhook{

	public function test($params)
	{
		echo __FILE__;
		echo "<pre>";
		print_r($this);
	}
	
	
}