<?php
namespace app\api\controller;

class Purchasequickask extends \think\Controller
{
	function index()
	{	
		$id = input('id');
		$list = db("quickask")
			->alias('q')
			->field('q.id,q.user_id,q.quickask_cate_id,q.content,q.price,q.is_anonymous,q.create_time,q.duration,q.status')
			->where("user_id=$id")
			->order('create_time desc')
			->select();
		return json($list);
	}
}


?>