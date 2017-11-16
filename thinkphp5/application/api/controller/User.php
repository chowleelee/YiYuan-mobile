<?php
namespace app\api\controller;
use \think\Session;
use \think\captcha\Captcha;

class User extends \think\Controller
{
	function index()
	{	
		$type = input('type');
        //$id = Session::get('user_id');
        $id = input('uid');
        if($type == 'login'){
            $phone = input('phone');
            $phoneCap = input('phoneCap');

            if(db('cap')->where("phone='$phone'")->value('cap') == $phoneCap){
                return 1;
            }else{
                return 0;
            }
        }else if($type == 'uhome'){

            $info = db('user')->where("id='$id'")->find();

            if($info['status']==3){
                $vip = db('vip')
                        ->field('real_name,head_img,is_real')
                        ->where("user_id='$id'")
                        ->find();
                $info['user_name'] = $vip['real_name'];
                $info['head_img'] = $vip['head_img'];
                $info['is_real'] = $vip['is_real'];
            }
            
            return json($info);
        }else if($type == 'edit'){
            $info = db('user')
                    ->where("id='$id'")
                    ->find();

            $vip = db('vip')
                    ->where("user_id='$id'")
                    ->find();

            if($vip){
                $vip['flag'] = 3;
                return json($vip);
            }else{
                $info['flag'] = 1;
                return json($info);
            }
        }else if($type == 'openvip'){
            $info = db('user')
                    ->field('user_name,id,head_img')
                    ->where("id='$id'")
                    ->find();

            $tovip = db('tovip')
                    ->where("user_id='$id'")
                    ->find();

            if($tovip){
                $tovip['flag'] = 2;
                $reason = db('reject_tovip')->where("user_id=".$tovip['user_id'])->value('reason');
                $tovip['reason'] = $reason;
                return json($tovip);
            }else{
                $info['flag'] = 1;
                return json($info);
            }
        }else if($type == 'totrue'){
            $info = db('vip')->field('id,is_real')->where("user_id='$id'")->find();
           
            $reason = db('reject')->where("vip_id=".$info['id'])->value('reason');

            if($reason){
                $info['reason'] = $reason;
            }

            return json($info);
        }else if($type == 'reset'){
            $id = input('uid');
            return db('user')->where("id='$id'")->value('phone');
        }else if($type == 'reset_phone'){
            $phone = input('phone');
            $info = db('user')->where("phone='$phone'")->find();
            if($info){
                return 1;
            }else{
                return 0;
            }
        }else{
        	$user_name = input('username');
			$user_pwd = input('userpwd');
			//用户表
			$list = db("user")
				->field('id,user_name,phone')
				->where("user_name='$user_name' and user_pwd=$user_pwd")
				->find();
			return json($list);
        }
		
	}
	function read()
	{
		$user_id=input('id');
		$list = db("user")
			->field('id,user_name,phone')
			->where("id=$user_id")
			->find();
		return json($list);
	}

	public function save(){
        $type = input('type');
        //$id = Session::get('user_id');
        $id = input('uid');
        if($type == 'login'){
            $phone = input('phone');

            $user_id = db('user')->where("phone='$phone'")->value('id');

            if(!$user_id){
                db('user')->insert(['phone'=>$phone,'head_img'=>'headbg.png']);
                $user_id = db('user')->getLastInsID();
            }

            //Session::set('user_id',$user_id);

            $vip_id = db('vip')->where("user_id='$user_id'")->value('id');

            /*if($vip_id){
                Session::set('vip_id',$vip_id);
            }*/

            return json(['user_id'=>$user_id,'vip_id'=>$vip_id]);
        }else if($type == 'edit'){
            $info = json_decode($_POST['info'],true);
            if(isset($_FILES['file'])){
                $name = saveAndgetSrc(ROOT_PATH."public/static/api/img/",'file');
                $info['head_img'] = $name;
            }
            if($info['flag']==1){
                unset($info['flag']);
                db('user')->update($info);
            }else if($info['flag']==3){
                unset($info['flag']);
                db('vip')->update($info);
            }
        }else if($type == 'openvip'){
            db('reject_tovip')->where("user_id='$id'")->delete();
            db('tovip')->where("user_id='$id'")->delete();
            $info = $_POST;
            if(isset($_FILES['file'])){
                $name = saveAndgetSrc(ROOT_PATH."public/static/api/img/",'file');
                $info['head_img'] = $name;
            }
            unset($info['type']);
            unset($info['uid']);
            $info['user_id'] = $id;
            db('tovip')->insert($info);
            db('user')->where("id='$id'")->setField('status', 2);
        }else if($type == 'totrue'){
            $vip_id = db('vip')->where("user_id='$id'")->value('id');
            db('reject')->where("vip_id='$vip_id'")->delete();

            db('vip')->where("user_id='$id'")->setField('is_real',1);
        }else if($type == 'reset'){
            $phone = input('phone');
            db('user')->where("id='$id'")->setField('phone',$phone);
        }
    }
	
}


?>