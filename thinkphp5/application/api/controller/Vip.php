<?php
namespace app\api\controller;

class Vip extends \think\Controller
{
	function index()
	{
            //答主列表
            $type = input('type');
            $user_id = input('id');
            if($type == 'hotList'){
                  $vip_list = db("vip")->alias('v')
                        ->order('v.listen_num DESC')
                        ->field('v.id,v.price,v.real_name,v.identity,v.status,v.introduce,v.listen_num,v.answer_num,v.head_img,vc.cate_name,v.is_real')
                        ->join('vip_cate vc','vc.id = v.vip_cate_id')
                        ->select();
                  $user_listen = db('vip_listen')
                        ->field('vip_id')
                        ->where('user_id',$user_id)
                        ->select();
                  $list[0] = $vip_list;
                  $list[1] = $user_listen;
                  return json($list);
            }else if($type == 'newList'){
                  $vip_list = db("vip")->alias('v')
                        ->order('v.become_time DESC')
                        ->field('v.id,v.price,v.real_name,v.identity,v.status,v.introduce,v.listen_num,v.answer_num,v.head_img,vc.cate_name,v.is_real')
                        ->join('vip_cate vc','vc.id = v.vip_cate_id')
                        ->select();
                  $user_listen = db('vip_listen')
                        ->field('vip_id')
                        ->where('user_id',$user_id)
                        ->select();
                  $list[0] = $vip_list;
                  $list[1] = $user_listen;
                  return json($list);
            }else if($type = 'vpcate'){
                  $cate_id = input('cate_id');
                  $user_id = input('user_id');
                  $vip_list = db("vip_cate")->alias('vc')
                        ->where('vc.id',$cate_id)
                        ->field('v.id,vc.cate_name,v.real_name,v.identity,v.status,v.price,v.introduce,v.head_img,v.is_real')
                        ->join('vip v','vc.id = v.vip_cate_id')
                        ->select();
                  $user_listen = db('vip_listen')
                        ->field('vip_id')
                        ->where('user_id',$user_id)
                        ->select();
                  $list[0] = $vip_list;
                  $list[1] = $user_listen;
                  return json($list);
            }
            else{
                  $list = db("vip")
                        ->field('id,real_name,identity,status,price,introduce,is_real')
                        ->select();
                  return json($list);
            }
		// //答主列表
		// $list = db("vip")
		// 	->field('id,real_name,identity,status,price,introduce')
		// 	->select();
		// return json($list);
	}
      function read()
      {
            $vip_id = input('id');
            $user_id = input('user_id');
            $vip_list = db("vip")->alias('a')
                  ->where('a.id',$vip_id)
                  ->field('a.id,real_name,identity,a.status,a.price,a.listen_num,a.answer_num,a.introduce,a.head_img,a.vip_cate_id,b.cate_name,a.head_img,is_real')
                  ->join('vip_cate b','a.vip_cate_id = b.id')
                  ->find();
            $vip_listen_id = db("vip_listen")
                  ->where('vip_id',$vip_id)
                  ->where('user_id',$user_id)
                  ->field('id')
                  ->find();
            $list[0] = $vip_list;
            $list[1] = $vip_listen_id;
            return json($list);
            // $vip_id = input('id');
            // $list = db("vip")->alias('a')
            //       ->where('a.id',$vip_id)
            //       ->field('a.id,real_name,identity,a.status,a.price,a.listen_num,introduce,vip_cate_id,b.cate_name')
            //       ->join('vip_cate b','a.vip_cate_id = b.id')
            //       ->find();
            
            // return json($list);
      }
      function update() 
      {
            $followed =  input('followed');
            $vip_id = input('id');
            //echo $followed;
            if($followed != 'true'){
                  $list = db("vip")->alias('a')
                  ->where('a.id',$vip_id)
                  ->join('vip_cate b','a.vip_cate_id = b.id')
                  ->setInc('a.listen_num');
            }else{
                  $list = db("vip")->alias('a')
                  ->where('a.id',$vip_id)
                  ->join('vip_cate b','a.vip_cate_id = b.id')
                  ->setDec('a.listen_num');
            }
            
      }

}


?>