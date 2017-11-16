<?php
namespace app\api\controller;
use \think\Session;

class QuickaskAnswer extends \think\Controller
{
	function index()
	{
		//快问回答
		$type = input('type');
		if($type == 'qanswer'){
            //可移动到quickask_answer的api
            //$vip_id = Session::get('vip_id');
            $vip_id = input('vid');

            $info = db('quickask_answer')
                    ->alias('qas')
                    ->join('quickask qa','qa.id=qas.quickask_id')
                    ->join('user u','u.id=qa.user_id')
                    ->join('vip v','v.id=qas.vip_id')
                    ->field('qa.id,qa.content as question,qa.price,qa.user_id,u.head_img as u_img,qas.id as qas_id,qas.content,qas.create_time,qas.listen_num,qas.like_num,qas.vip_id,v.head_img as v_img,qa.is_anonymous,qas.answer_flag')
                    ->where("qas.vip_id='$vip_id'")
                    ->select();

            return json($info);
        }else{
        	$list = db("quickask_answer")
				->field('id,content,is_select,create_time,listen_num,like_num')
				->select();
			return json($list);
        }
		
	}
	function read()
	{
		$id = input("id");
		$status = db('quickask')->where("id='$id'")->value('status');
		if($status==1){ //status:1 正在进行  status:2 已解决
			$list = db("quickask_answer")
				->alias('qa')
				->field('qa.content,qa.create_time,qa.like_num,qa.listen_num,qa.vip_id,v.real_name,v.identity,v.head_img,qa.quickask_id,qa.id,q.status,qa.answer_flag')
				->join('quickask q','q.id=qa.quickask_id')
				->join('vip v','qa.vip_id = v.id')
				->where("qa.quickask_id = $id")   // and is_select =1
				->order('qa.create_time')
				->select();
		}else{
			$list = db("quickask_answer")
				->alias('qa')
				->field('qa.content,qa.create_time,qa.like_num,qa.listen_num,qa.vip_id,v.real_name,v.identity,v.head_img,qa.quickask_id,qa.id,q.status,qa.price,qa.answer_flag')
				->join('vip v','qa.vip_id = v.id')
				->join('quickask q','q.id=qa.quickask_id')
				->where("qa.quickask_id = $id and qa.is_select=1")   // and is_select =1
				->order('qa.create_time')
				->select();
		}
		return json($list);
	}

	function save(){
		$data = json_decode(input('data'),true);
		$award = round(10 / count($data),2);
		$quickask_id = input('quickask_id');
		foreach ($data as $key => $value) {
			db('quickask_answer')->where("id=".$value['answer_id'])->update(['is_select'=>1,'price'=>$award]);
			db('user')
				->alias('u')
				->join('vip v','u.id=v.user_id')
				->where("v.id=".$value['vip_id'])->setField('u.total_income',$award);
		}
		db('quickask')->where("id=".$quickask_id)->setField('status',2);
		return $award;
	}
}


?>