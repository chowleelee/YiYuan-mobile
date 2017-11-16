<?php
namespace app\admin\model;

class Vip extends \think\Model{
 
	public function addVip(){
		db('vip')->insert([
			'real_name'=>input('real_name'),
			'identity'=>input('identity'),
			'price'=>input('price'),
			'is_real'=>input('is_real'),
			'sort'=>input('sort'),
			'vip_cate_id'=>input('vip_cate_id'),
			'introduce'=>input('introduce'),
			'become_time'=>time()
			]);
	}

	public function getVipInfo()
	{	
		return db('vip')
			->where("id=".input('id'))
			->find();
	}

	public function getVipList()
	{
		//条件查询
		$searchParam = ['query'=>[]];
		$pageParam = ['query'=>[]];

		$vip_name = input('vip_name');
		$vip_cate = input('vip_cate');
		$floor_price = input('floor_price');
		$ceil_price = input('ceil_price');

		/*if(!$vip_name && !$vip_cate && !$floor_price && !$ceil_price){
			return db('vip')->paginate(8);
		}*/

		//答主姓名
		if($vip_name){
			$searchParam['query']['real_name'] = ['like','%'.$vip_name.'%'];
			$pageParam['query']['vip_name'] = $vip_name;
		}

		//答主分类
		if($vip_cate){
			$searchParam['query']['vip_cate_id'] = $vip_cate;
			$pageParam['query']['vip_cate'] = $vip_cate;
		}

		//提问价
		if($floor_price){
			$searchParam['query']['price'] = ['between',$floor_price.','.$ceil_price];
			$pageParam['query']['floor_price'] = $floor_price;
			$pageParam['query']['ceil_price'] = $ceil_price;
		}

		$vip_list = db('vip')
					->alias('v')
					->join('vip_cate vc','vc.id=v.vip_cate_id')
					->field('v.*,vc.cate_name')
					->where($searchParam['query'])
					->paginate(8,false,$pageParam);

		return $vip_list;
	}
}

?>