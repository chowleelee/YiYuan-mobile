<?php
namespace app\api\controller;

class VipListen extends \think\Controller
{
	function index()
	{
		//收听答主表
		$id = input('id');
            $list = db("vip_listen")->alias('v')
                  ->where("v.user_id='$id'")
                  ->join('vip b','b.id = v.vip_id')
                  ->join('user u','u.id = v.user_id')
                  ->field('b.listen_num,b.id,b.real_name,b.identity,b.head_img,b.is_real')
                  ->select();
            return json($list);
	}
      function read()
      {
            $id = input('id');
            echo $id;
            $list = db("vip_listen")->alias('v')
                  ->where('v.vip_id',$id)
                  ->field('id')
                  ->select();
            return json($list);
      }
      function update() 
      {
            $followed =  input('followed');
            $vip_id = input('id');
            $user_id = input('uid');
            $data = ['vip_id' => $vip_id,'user_id' => $user_id];
            if($followed == 'true'){
                  $list = db("vip_listen")
                        ->insert($data);
            }else if($followed == 'false'){
                 $list = db("vip_listen")
                        ->where("vip_id=$vip_id and user_id=$user_id")
                        ->delete();
            }
            
      }
	
}


?>