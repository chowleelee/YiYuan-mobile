<?php
namespace app\api\controller;

class SmalltalkContent extends \think\Controller
{
	function index()
	{
		//讲单内容表
		$smalltalk_id=input('smalltalk_id');
		$list = db("smalltalk_content")
			->field('id,title')
			->where("smalltalk_id=$smalltalk_id")
			->select();
		return json($list);
	}
	function read()
	{
		$smalltalk_content_id=input('id');
		$list = db("smalltalk_content")
			->field('id,title,smalltalk_id')
			->where("id=$smalltalk_content_id")
			->select();
		return json($list);
	}
	
}


?>