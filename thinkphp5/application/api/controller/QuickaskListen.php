<?php
namespace app\api\controller;

class QuickaskListen extends \think\Controller
{
	function index()
	{
		
	}
	function read()
	{
		$quickask_answer_id = input('id'); 
		$uid = input('uid');
		$list = db("quickask_listen")
			->where("quickask_answer_id = $quickask_answer_id and user_id = $uid")
			->value('id');
		return json($list);
	}
	function save()
	{
		$vip_id = input('vip_id');
		$data['user_id'] = input('uid');
		$data['quickask_answer_id'] = input('quickask_answer_id');
		db('quickask_listen')->insert($data);
		db('quickask_answer')->where('id='.input('quickask_answer_id'))->setInc('listen_num', 1);
		db('vip')->where('id='.$vip_id)->setInc('listen_num', 1);
	}
	
}


?>