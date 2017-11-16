<?php
namespace app\api\controller;

class AskListen extends \think\Controller
{
	function index()
	{
		
	}
	function read()
	{
		$ask_id = input('id'); 
		$uid = input('uid');
		$list = db("ask_listen")
			->where("ask_id = $ask_id and user_id = $uid")
			->value('id');
		return json($list);
	}
	function save()
	{
		$user_id = input('uid');
		$ask_id = input('askId');
		$vip_id = input('vip_id');
		$data = ['ask_id' => $ask_id,'user_id' => $user_id];
		db('ask_listen')->insert($data);
		db('ask')->where('id='.$ask_id)->setInc('listen_num', 1);
		db('vip')->where('id='.$vip_id)->setInc('listen_num', 1);
	}
	
}


?>