<?php
namespace app\api\controller;
use \think\Session;

class Ask extends \think\Controller
{
	function index()
	{
		$type = input('type');
		if($type == 'ask'){
            //可移动到ask的api
            $ask_id = input('id');
            $info = db('ask')
                    ->alias('a')
                    ->join('user u','u.id=a.user_id')
                    //->join('vip v','v.id=a.vip_id')
                    ->field('a.ask_content,a.create_time,a.user_id,a.status,u.user_name,u.head_img,a.id,a.price,a.is_public,a.vip_id')
                    ->where("a.id='$ask_id'")
                    ->find();

            return json($info);
        }else if($type == 'quickask'){
            //可移动到ask的api
            $ask_id = input('id');
            $info = db('quickask')
                    ->alias('qa')
                    ->join('user u','u.id=qa.user_id')
                    ->field('qa.content,qa.create_time,qa.user_id,qa.status,u.user_name,u.head_img,qa.id,qa.price,qa.is_anonymous')
                    ->where("qa.id='$ask_id'")
                    ->find();

            return json($info);
        }else if($type == 'answer'){
            //可移动到ask的api
            $vip_id = input('vid');

            $info = db('ask')
                    ->alias('a')
                    ->join('user u','u.id=a.user_id')
                    //->join('vip v','v.id=a.vip_id')
                    ->field('a.ask_content,a.create_time,a.user_id,a.status,u.user_name,u.head_img,a.id,a.price')
                    ->where("a.vip_id='$vip_id'")
                    ->select();

            return json($info);
        }else{
        	$list = db("ask")->alias('a')
				->field('a.id,a.ask_content,a.create_time,a.answer_content,a.like_num,a.listen_num,is_public,a.duration,a.vip_id,a.user_id,b.real_name,b.head_img,a.answer_flag')
                ->join('vip b','a.vip_id = b.id')
                ->join('user u','u.id=a.user_id')
                ->where('a.status=2 and a.is_public=1')
				->paginate(8);
			return json($list);
        }
	}
	function read()
	{
		$id = input('id');
		$type = input('type');
		if($type == 'vip'){
			//$user_id = Session::get('user_id');
			//$vip_id = Session::get('vip_id');
			$user_id = input('uid');
			$vip_id = input('vid');
			$where = '';
			if($vip_id == $id){
				$where = "a.vip_id='$id' and a.status=2";
			}else{
				$where = "a.vip_id='$id' and a.status=2 and (a.is_public=1 or a.user_id='$user_id')";
			}
			$list = db("ask")->alias('a')
		            ->where($where)
		            //->join('vip v','a.vip_id = v.id')
		            ->join('user u','a.user_id = u.id')
		            ->field('a.id,a.user_id,u.head_img,a.ask_content,a.create_time,a.like_num,a.listen_num,a.price,a.vip_id,a.answer_flag,a.answer_content')
		            ->select();
	      	return json($list);
		}else if($type == 'ask'){
			$list = db("ask")->alias('a')
		            ->where('a.id',$id)
		            ->join('user u','a.user_id = u.id')
		            //->join('vip v','a.vip_id = v.id')
		            ->field('a.id,u.head_img,a.user_id,u.user_name,a.ask_content,a.create_time,a.like_num,a.listen_num,a.vip_id,a.price,a.answer_flag,a.answer_content')
		            ->find();
	      	return json($list);
		}else if($type=='other_ask'){
			$list = db("ask")->alias('a')
		            ->where("a.id!=$id and a.status=2")
		            ->order('rand()')
		            ->join('user u','a.user_id = u.id')
		            ->join('vip v','a.vip_id = v.id')
		            ->field('a.id,u.head_img,u.user_name,a.ask_content,a.create_time,a.like_num,a.listen_num,v.real_name,v.identity,a.answer_flag,a.answer_content')
		            ->find();
	      	return json($list);
		}else if($type == 'listen'){
			$list = db("ask")->alias('a')
				->field('a.id,ask_content,create_time,answer_content,like_num,a.listen_num,is_public,duration,a.vip_id,a.answer_flag,a.answer_content')
	                  ->join('vip b','a.vip_id = b.id')
	                  ->field('b.real_name')
				->select();
			return json($list);
		}else if($type == '默认'){
			/*$user_id = Session::get('user_id');
			$vip_id = Session::get('vip_id');*/
			$user_id = input('uid');
			$vip_id = input('vid');
			$where = '';
			if($vip_id == $id){
				$where = "a.vip_id='$id' and a.status=2";
			}else{
				$where = "a.vip_id='$id' and a.status=2 and (a.is_public=1 or a.user_id='$user_id')";
			}
			$list = db("ask")->alias('a')
		            ->where($where)
		            ->order('a.id')
		            ->join('user u','a.user_id = u.id')
		            //->join('vip v','a.vip_id = v.id')
		            ->field('a.id,u.head_img,a.ask_content,a.create_time,a.like_num,a.listen_num,a.price,a.answer_flag,a.answer_content')
		            ->select();
	      	return json($list);
		}else if($type == '最新'){
			/*$user_id = Session::get('user_id');
			$vip_id = Session::get('vip_id');*/
			$user_id = input('uid');
			$vip_id = input('vid');
			$where = '';
			if($vip_id == $id){
				$where = "a.vip_id='$id' and a.status=2";
			}else{
				$where = "a.vip_id='$id' and a.status=2 and (a.is_public=1 or a.user_id='$user_id')";
			}
			$list = db("ask")->alias('a')
		            ->where($where)
		            ->order('a.create_time DESC')
		            ->join('user u','a.user_id = u.id')
		            //->join('vip v','a.vip_id = v.id')
		            ->field('a.id,u.head_img,a.ask_content,a.create_time,a.like_num,a.listen_num,a.price,a.answer_flag,a.answer_content')
		            ->select();
	      	return json($list);
		}else if($type == '热门'){
			/*$user_id = Session::get('user_id');
			$vip_id = Session::get('vip_id');*/
			$user_id = input('uid');
			$vip_id = input('vid');
			$where = '';
			if($vip_id == $id){
				$where = "a.vip_id='$id' and a.status=2";
			}else{
				$where = "a.vip_id='$id' and a.status=2 and (a.is_public=1 or a.user_id='$user_id')";
			}
			$list = db("ask")->alias('a')
		            ->where($where)
		            ->order('a.listen_num DESC')
		            ->join('user u','a.user_id = u.id')
		            //->join('vip v','a.vip_id = v.id')
		            ->field('a.id,u.head_img,a.ask_content,a.create_time,a.like_num,a.listen_num,a.price,a.answer_flag,a.answer_content')
		            ->select();
	      	return json($list);
		}
		
	}

	function update()
	{
		$ask_id = input('id');
		$type = input('type');
		if($type == 'ask'){
			db('ask')->where('id='.$ask_id)->setField('status',4);
		}else{
			$list = db('ask')
			->where('id',$ask_id)
			->setInc('like_num');
		}
		

	}

	function save(){
		$type = input('type');
		$vip_id = input('vip_id');
		$answer_method = input('answer_method');
		$answer_flag = input('answer_flag');
		if($type == 'ask'){
            $ask_id = input('id');
            if($answer_method == 'yuyin'){
	            if(isset($_FILES['file'])){
	                //$name = saveAndgetSrc($_SERVER['DOCUMENT_ROOT'].'/project/fenda/vue/static/audio/','file');
	                //$answer_content = '/static/audio/'.$name;
	                $name = saveAndgetSrc(ROOT_PATH."public/static/api/audio/",'file');
	                $answer_content = $name;
	            }
	        }else if($answer_method == 'text'){
            	$answer_content = input('answer_content');
            }

            db('ask')->where("id='$ask_id'")->update(['status'=>2,'answer_content'=>$answer_content,'answer_flag'=>$answer_flag]);

            $price = input('price');
            $user_id = db('vip')->where("id='$vip_id'")->value('user_id');
            db('user')->where("id='$user_id'")->setInc('total_income',$price);

        }else if($type == 'quickask'){
        	//$data['vip_id'] = Session::get('vip_id');
        	$data['vip_id'] = $vip_id;
            $data['quickask_id'] = input('id');
            $data['answer_flag'] = input('answer_flag');
            $data['create_time'] = time();
            if($answer_method == 'yuyin'){
	            if(isset($_FILES['file'])){
	                //$name = saveAndgetSrc($_SERVER['DOCUMENT_ROOT'].'/project/fenda/vue/static/audio/','file');
	                //$data['content'] = '/static/audio/'.$name;
	                $name = saveAndgetSrc(ROOT_PATH."public/static/api/audio/",'file');
	                $data['content'] = $name;
	            }
	        }else if($answer_method == 'text'){
            	$data['content'] = input('answer_content');
            }
            db('quickask_answer')->insert($data);
        }else{
        	$data['user_id'] = input('uid');
			$data['vip_id']= input('vip_id');
			$data['ask_content'] = input('ask_content');
			$data['is_public'] = input('is_public')=='true'?1:0;
			$data['create_time'] = time();
			$data['price'] = input('price');
			$list = db("ask")
	               ->insert($data);
	    }
	}
	
}


?>