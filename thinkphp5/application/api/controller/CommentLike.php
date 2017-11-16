<?php
namespace app\api\controller;

class CommentLike extends \think\Controller
{
	function index()
	{
		
	}
	function read(){
		$uid = input('uid');
		$id = input('id');
		$list = db('comment_like')
			->field('id')
			->where("user_id=$uid and comment_id=$id")
			->find();
	
		return json($list);
	}
	function save(){
		$uid = input('uid');
		$id = input('id');
		db('comment_like')
			->data(['comment_id'=>$id,'user_id'=>$uid])
			->insert();
	}
	
}


?>