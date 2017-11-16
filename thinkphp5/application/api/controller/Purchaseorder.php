<?php
namespace app\api\controller;

class Purchaseorder extends \think\Controller
{
	function index()
	{	
		$id = input('id');
		// echo $id;
		$list = db("smalltalk_buy")
			->alias('sb')
			->field('s.id,s.title,v.real_name,v.identity,sc.cate_name,s.join_num,v.head_img,s.create_time')
			->join('user u','sb.user_id = u.id')
			->join('smalltalk s','sb.smalltalk_id = s.id')
			->join('vip v','s.vip_id = v.id','left')
			->join('smalltalk_cate sc','s.smalltalk_cate_id = sc.id')
			->where("sb.user_id=$id")
			->order('s.create_time desc')
			->select();
		return json($list);
	}
	function read(){
		$smalltalk_id = input('id'); 
		$uid = input('uid');
		$list = db("smalltalk_buy")
			->field('id')
			->where("smalltalk_id=$smalltalk_id and user_id = $uid")
			->find();
		return json($list);
	}

	function save(){
		$data['user_id'] = input('uid');
		$data['smalltalk_id']= input('smalltalk_id');
		db("smalltalk_buy")->insert($data);
		db("smalltalk")->where('id='.$data['smalltalk_id'])->setInc('join_num', 1);
	}
}


?>