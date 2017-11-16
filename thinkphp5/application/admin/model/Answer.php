<?php
namespace app\admin\model;

class Answer extends \think\Model{
 
	public function getAnswerList()
	{
		//条件查询
		$searchParam = ['query'=>[]];
		$pageParam = ['query'=>[]];

		$real_name = input('real_name');
        $quickask_id = input('quickask_id');
        $is_select = input('is_select');

		//答主
		if($real_name){
			$searchParam['query']['real_name'] = ['like','%'.$real_name.'%'];
			$pageParam['query']['real_name'] = $real_name;
		}

		//问题编号
		if($quickask_id){
			$searchParam['query']['quickask_id'] = ['like',$quickask_id.'%'];
			$pageParam['query']['quickask_id'] = $quickask_id;
		}

		//是否被采纳
		if($is_select){
			$searchParam['query']['is_select'] = $is_select;
			$pageParam['query']['is_select'] = $is_select;
		}

		$answer_list = db('quickask_answer')
                    ->alias('qa')
                    ->join('vip v','v.id=qa.vip_id')
                    ->field('v.real_name,qa.*')
					->where($searchParam['query'])
					->paginate(8,false,$pageParam);

		return $answer_list;
	}
}

?>