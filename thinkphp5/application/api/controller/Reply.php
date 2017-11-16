<?php
namespace app\api\controller;

class Reply extends \think\Controller
{
	function index()
	{
		//小讲回复表
		$comment_id=input('reply_id');
		$list = db("reply")
			->field('id,content,user_id,parent_id,comment_id,status')
			->where("comment_id=$comment_id")
			->select();
		return json($list);
	}
	function read()
	{
		//小讲回复表
		$parent_id=input('id');
		$list = db("reply")
			->field('id,content,user_id,parent_id,comment_id,status')
			->where("id=$parent_id")
			->find();
		return json($list);
	}

	function save(){
		$data['comment_id'] = input('comment_id');
		$data['content'] = input('content');
		$data['parent_id'] = input('parent_id');
		$data['user_id']= input('uid');
		//$data['create_time']  = time();
		$list = db('reply')
			->insert($data);
	}
	
}


?>