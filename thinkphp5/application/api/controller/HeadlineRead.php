<?php
namespace app\api\controller;

class HeadlineRead extends \think\Controller
{
	function index()
	{
		$uid = input('id');
		$type = input('type');
		if($type == 'today'){
			$list = db("headline_read")
			->alias('hr')
			->field('h.id')
			->where("hr.user_id = $uid")
			->whereTime('h.create_time', 'today')
			->join('headline h','h.id = hr.headline_id')
			->select();
		}else if($type == 'all'){
			$list = db("headline_read")
			->alias('hr')
			->field('h.id')
			->where("hr.user_id = $uid")
			->join('headline h','h.id = hr.headline_id')
			->select();
		}
		return json($list);
	}
	function read(){
		$uid = input('uid');
		$id = input('id');
		$headline_read_id = db('headline_read')
			->field('id')
			->where("user_id=$uid and headline_id=$id")
			->find();
	
		return json($headline_read_id);
	}
	function save(){
		$uid = input('uid');
		$id = input('id');
		db('headline_read')
			->data(['headline_id'=>$id,'user_id'=>$uid])
			->insert();
	}
	
}


?>