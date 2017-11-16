<?php
namespace app\api\controller;

class AskLike extends \think\Controller
{
	function index()
	{
		
	}
	function read()
	{
		$ask_id = input('id'); 
		$uid = input('uid');
		$list = db("ask_like")
			->field('id')
			->where("ask_id = $ask_id and user_id = $uid")
			->find();
		return json($list);
	}
	function save()
	{
		$user_id = input('uid');
		$ask_id = input('askId');
		$data = ['ask_id' => $ask_id,'user_id' => $user_id];
		$like_list = db('ask_like')
			->insert($data);
		//db('ask')->where('id='.$ask_id)->setInc('like_num', 1);
	}
	
}


?>