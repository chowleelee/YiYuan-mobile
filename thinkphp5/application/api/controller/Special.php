<?php
namespace app\api\controller;

class Special extends \think\Controller
{
	function index()
	{
		//
		$type = input('type');
		if($type == 'recommend_special'){
			$list = db("special")
				->field('id,special_img')
				->limit(4)
				->select();
			return json($list);
		}else{
			$list = db("special")->alias('s')
				->field('s.id,s.title,s.special_img,s.vip_id,s.introduce,v.real_name,v.identity')
				->join('vip v','v.id=s.vip_id')
				->select();
			return json($list);
		}
	}
	function read()
	{
		$special_id=input('id');
		$list = db("special")->alias('s')
			->field('s.id,s.title,s.special_img,s.vip_id,s.introduce,v.real_name,v.identity')
			->where("s.id=$special_id")
			->join('vip v','v.id=s.vip_id')
			->find();
		return json($list);
	}
	
}


?>