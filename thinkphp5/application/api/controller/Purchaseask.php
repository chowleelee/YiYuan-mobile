<?php
namespace app\api\controller;

class Purchaseask extends \think\Controller
{
	function index()
	{	
		$id = input('id');
		$list = db("ask")
			->alias('a')
			->field('a.ask_content,a.vip_id,a.create_time,a.listen_num,a.like_num,v.real_name,a.status,a.answer_content,v.head_img,v.is_real,a.id,a.answer_flag')
			->join('vip v','a.vip_id = v.id')
			->where("a.user_id=$id")
			->order('a.create_time desc')
			->select();
		return json($list);
	}
	// function read(){
	// 	$cat_id = input('id');
	// 	$list = db("smalltalk")
	// 		->alias('s')
	// 		->join('vip v','s.vip_id = v.id')
	// 		->where("smalltalk_cate_id=".$cat_id)
	// 		->select();
	// 	return json($list);
	// }

	function save(){
		$data['user_id'] = input('uid');
		$data['ask_id'] = input('ask_id');
		$user_id = input('user_id');
		db("ask_buy")->insert($data);
		db('user')->where('id='.$user_id)->setInc('total_income', 1);
	}
}


?>