<?php
namespace app\api\controller;

class Slider extends \think\Controller
{
	function index()
	{
		//快问分类
		$list = db("slider")->select();
		return json($list);
	}
	
}


?>