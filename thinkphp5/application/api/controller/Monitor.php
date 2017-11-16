<?php
namespace app\api\controller;

class Monitor extends \think\Controller
{
	public function index(){
		return $this->moniQuickask();
		$this->moniAsk();
	}

	function moniQuickask(){
		$now = time();
		$list = db('quickask')->field('id,create_time,status')->where('status=1')->select();
		foreach ($list as $key => $value) {
			$left_time = $now - $value['create_time'];
			if($left_time >= 48*60*60){
				$answer_list = db('quickask_answer')->field('id')->where('quickask_id='.$value['id'])->order('like_num desc')->limit(3)->select();
				if(count($answer_list)>0){
					db('quickask')->where('id='.$value['id'])->setField('status',3);
					$price = round(10 / count($answer_list),2);
					foreach ($answer_list as $key => $value) {
						db('quickask_answer')->where('id='.$value['id'])->update(['price'=>$price,'is_select'=>1]);
					}
				}else{
					db('quickask')->where('id='.$value['id'])->setField('status',4);
				}
			}
		}
	}

	function moniAsk(){
		$now = time();
		$list = db('ask')->field('id,create_time,status,answer_flag')->where('status=1')->select();
		foreach ($list as $key => $value) {
			$left_time = $now - $value['create_time'];
			if($left_time >= 48*60*60){
				if($value['answer_flag']==0){
					db('ask')->where('id='.$value['id'])->setField('status',3);
				}
			}
		}	
	}
}
?>