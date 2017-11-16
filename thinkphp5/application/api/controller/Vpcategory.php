<?php
namespace app\api\controller;

class Vpcategory extends \think\Controller
{
	function index()
	{
		//答主分类
		$list = db("vip_cate")
			->field('id,cate_name')
			->select();
		return json($list);
	}

	function read()
	{
		$cate_id = input('id');
		$list = db('vip_cate')
			->where('id',$cate_id)
			->field('cate_name')
			->find();
		return json($list);
	}
}


?>