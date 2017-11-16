<?php
namespace app\api\controller;

class QuickAskLike extends \think\Controller
{
	function index()
	{
		
	}
	function read()
	{
		$quickask_answer_id = input('id'); 
		$uid = input('uid');
		$list = db("quickask_like")
			->where("quickask_answer_id = $quickask_answer_id and user_id = $uid")
			->value('id');
		return json($list);
	}
	function save()
	{
		$data['user_id'] = input('uid');
		$data['quickask_answer_id'] = input('quickask_answer_id');
		db('quickask_like')->insert($data);
		db('quickask_answer')->where('id='.input('quickask_answer_id'))->setInc('like_num', 1);
	}
	
}


?>