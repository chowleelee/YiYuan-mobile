<?php
namespace app\admin\model;

class Myask extends \think\Model{
 
	public function getMyaskList()
	{
		//条件查询
		$searchParam = ['query'=>[]];
		$pageParam = ['query'=>[]];

		$user_name = input('user_name');
		$vip_name = input('vip_name');
		$is_public = input('is_public');

		/*if(!$vip_name && !$vip_cate && !$floor_price && !$ceil_price){
			return db('vip')->paginate(8);
		}*/

		//提问者
		if($user_name){
			$searchParam['query']['user_name'] = ['like','%'.$user_name.'%'];
			$pageParam['query']['user_name'] = $user_name;
		}

		//答主
		if($vip_name){
			$searchParam['query']['real_name'] = ['like','%'.$vip_name.'%'];
			$pageParam['query']['vip_name'] = $vip_name;
		}

		//是否公开
		if($is_public){
			$searchParam['query']['is_public'] = $is_public;
			$pageParam['query']['is_public'] = $is_public;
		}

		$ask_list = db('ask')
    				->alias('a')
    				->join('user u','u.id=a.user_id')
    				->join('vip v','v.id=a.vip_id')
    				->field('u.user_name,v.real_name,a.*')
					->where($searchParam['query'])
					->paginate(8,false,$pageParam);

		return $ask_list;
	}
}

?>