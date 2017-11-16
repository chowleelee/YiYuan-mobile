<?php
namespace app\api\controller;
use \think\Session;
/**
* 
*/
class Serch extends \think\Controller
{
	
	function index()
	{	
		$key_word = input('key_word');
		$vip_list = db('vip')
			->field('id,real_name,identity,listen_num,answer_num,head_img')
			->where('real_name|identity','like',"%$key_word%")
			->order('id asc')
			->select();

		$ask_list = db('ask')
			->alias('a')
			->field('a.id,a.ask_content,v.real_name,v.identity,v.head_img')
			->join('vip v','a.vip_id=v.id')
			->where('ask_content','like',"%$key_word%")
			->order('a.id asc')
			->select();		

		$smalltalk_list = db('smalltalk')
			->alias('s')
			->field('s.id,s.title,s.join_num,v.real_name,v.identity,v.head_img')
			->join('vip v','s.vip_id=v.id')
			->where('title','like',"%$key_word%")
			->order('s.id asc')
			->select();

		$recommend_list = db('smalltalk')
			->alias('s')
			->field('s.title,s.join_num,v.real_name,v.identity')
			->join('vip v','s.vip_id=v.id')
			->where("title='$key_word'")
			->order('s.id asc')
			->select();	

		$want_list = [
			'vip_list'=>$vip_list,
			'ask_list'=>$ask_list,
			'smalltalk_list'=>$smalltalk_list,
			'recommend_list'=>$recommend_list
		];

		return json($want_list);
	}
	
}


?>