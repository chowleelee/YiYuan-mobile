<?php
namespace app\api\controller;

class Purchasebuy extends \think\Controller
{
	function index()
	{	
		$id = input('id');
		$list = db("ask_buy")
			->alias('ab')
			->field('a.id,a.ask_content,a.create_time,v.price,a.like_num,a.listen_num,v.head_img,u.head_img as user_head,a.answer_content,a.answer_flag,a.vip_id')
			->join('ask a','ab.ask_id = a.id')
			->join('vip v','a.vip_id = v.id')
			->join('user u','a.user_id = u.id')
			->where("ab.user_id=$id")
			->order('ab.id desc')
			->select();
		return json($list);
	}
	function read()
	{	
		$ask_id = input('id'); 
		$uid = input('uid');
		$list = db("ask_buy")
			->field('id')
			->where("ask_id=$ask_id and user_id = $uid")
			->find();
		return json($list);
	}
}


?>