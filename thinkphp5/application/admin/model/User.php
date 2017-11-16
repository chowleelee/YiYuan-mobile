<?php
namespace app\admin\model;

class User extends \think\Model{
 
	public function addUser(){
		$head_img = saveAndgetSrc(ROOT_PATH."public/static/api/img/",'head_img');
		db('user')->insert([
			'user_name'=>input('user_name'),
			'total_income'=>input('total_income'),
			'total_profit'=>input('total_profit'),
			'phone'=>input('phone'),
			'head_img'=>$head_img,
			'create_time'=>time()
			]);
	}

	public function updateUser(){
		if(!empty($_FILES['head_img']['tmp_name'])){
            $head_img = saveAndgetSrc(ROOT_PATH."public/static/api/img/",'head_img');
        }else{
            $head_img = input('img');
        }
		db('user')->where('id='.input('id'))->update([
			'user_name'=>input('user_name'),
			'total_income'=>input('total_income'),
			'total_profit'=>input('total_profit'),
			'phone'=>input('phone'),
			'head_img'=>$head_img
			]);
	}

	public function getUserInfo()
	{	
		return db('user')
			->where("id=".input('id'))
			->find();
	}

	public function getUserList()
	{
		//条件查询
		$searchParam = ['query'=>[]];
		$pageParam = ['query'=>[]];

		$user_name = input('user_name');
		$phone = input('phone');

		/*if(!$vip_name && !$vip_cate && !$floor_price && !$ceil_price){
			return db('vip')->paginate(8);
		}*/

		//用户姓名
		if($user_name){
			$searchParam['query']['user_name'] = ['like','%'.$user_name.'%'];
			$pageParam['query']['user_name'] = $user_name;
		}

		//手机号
		if($phone){
			$searchParam['query']['phone'] = ['like',$phone.'%'];
			$pageParam['query']['phone'] = $phone;
		}

		$user_list = db('user')
					->where($searchParam['query'])
					->paginate(8,false,$pageParam);

		return $user_list;
	}

	function addVip(){
		$info = db('tovip')
				->field('user_id,price,identity,introduce,real_name,head_img,vip_cate_id')
				->where('user_id='.input('user_id'))
				->find();
		$info['become_time'] = time();

		db('vip')->insert($info);
		db('user')->where('id='.input('user_id'))->update(['user_name'=>$info['real_name'],'head_img'=>$info['head_img']]);
	}
}

?>