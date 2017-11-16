<?php
namespace app\api\controller;

class Quickask extends \think\Controller
{
	function index()
	{
		//快问
		$list = db("quickask")
			->alias('q')
			->join('user u','u.id=q.user_id')
			->field('q.id,q.user_id,q.quickask_cate_id,q.content,q.price,q.is_anonymous,q.create_time,q.duration,q.status,u.user_name')
			->order('q.create_time desc')
			->paginate(5);
		return json($list);
	}

	function read(){
		$type = input('type');
		if($type == 'cate'){
			$cate_id=input("id");
			$lista = db("quickask")
				->alias('q')
				->field('q.id,q.user_id,q.quickask_cate_id,q.content,q.price,q.is_anonymous,q.create_time,q.duration,q.status')
				->where("quickask_cate_id=$cate_id")
				->paginate(5);
			return json($lista);
		}else if($type == 'quickask'){
			$id=input("id");
			$lista = db("quickask")
				->alias('q')
				->field('q.id,q.user_id,q.quickask_cate_id,q.content,q.price,q.is_anonymous,q.create_time,q.duration,u.user_name,u.head_img,q.status')
				->join('user u','u.id = q.user_id')
				->where("q.id=$id")
				->find();
			return json($lista);
		}
	}

	function save(){
    	$data['user_id'] = input('uid');
		$data['content'] = input('content');
		$data['price'] = input('price');
		$data['is_anonymous'] = input('is_anonymous')=='true'?1:0;
		$data['quickask_cate_id'] = input('quickask_cate_id');
		$data['create_time'] = time();
		db("quickask")->insert($data);
	}
}


?>