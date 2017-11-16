<?php
namespace app\api\controller;
use \think\Session;

class Smalltalk extends \think\Controller
{
	function index()
	{
		//小讲表
		/*$vip_id = Session::get('vip_id')?Session::get('vip_id'):'';
		$where = '';*/

		$list = db("smalltalk")
			->alias('s')
			->field('s.id,s.title,s.smalltalk_img,s.join_num,s.create_time,v.real_name,v.identity,v.head_img,sc.cate_name')
			->join('vip v','s.vip_id = v.id')
			->join('smalltalk_cate sc','s.smalltalk_cate_id = sc.id')
			->order('s.create_time desc')
			->paginate(8);
		//print_r($list);
		return json($list);
	}
	function read(){
		$type = input('type');
		if($type == "cate"){
			$cat_id = input('id');
			$list = db("smalltalk")
			->alias('s')
			->field('s.id,s.title,s.smalltalk_img,s.join_num,s.create_time,v.real_name,v.identity,v.head_img')
			->join('vip v','s.vip_id = v.id')
			->where("smalltalk_cate_id=".$cat_id)
			->order('s.create_time desc')
			->paginate(5);
			return json($list);
		}else if($type == "vip"){
			$vip_id = input('id');
			$smalltalk_list = db("smalltalk")
			->field('title,join_num,id')
			->where('vip_id',$vip_id)
			->select();
			return json($smalltalk_list);
		}else if($type=="smalltalk"){
			$smalltalk_id = input('id');
			$listb = db("smalltalk")->alias('s')
			->field('s.id,s.title,s.smalltalk_cate_id,s.smalltalk_img,s.duration,s.special_id,s.price,s.summary,s.join_num,s.create_time,s.vip_id,v.real_name,v.head_img')
			->where("s.id=$smalltalk_id")
			->join('vip v','s.vip_id = v.id')
			->find();
			// var_dump($listb);
			return json($listb);
		}
		
	}
	function specialwen()
	{
		$smalltalk_special_id = input('smalltalk_special_id');
		$listb = db("smalltalk")
			->field('id,title,smalltalk_cate_id,smalltalk_img,duration,special_id,price,summary,join_num,create_time,vip_id')
			->where("special_id=$smalltalk_special_id")
			->select();
			// var_dump($listb);
		return json($listb);
	}

	function save(){
        //$vip_id = Session::get('vip_id');
        $vip_id = input('vid');
        
        $novel_list = json_decode($_POST['novel_list'],true);

        $img_name = saveAndgetSrc(ROOT_PATH."public/static/api/img/",'img');
	    $smalltalk_img = $img_name;

        db('smalltalk')->insert(['title'=>input('title'),'smalltalk_img'=>$smalltalk_img,'smalltalk_cate_id'=>input('cate_id'),'price'=>input('price'),'summary'=>input('content'),'vip_id'=>$vip_id]);

        $smalltalk_id = db('smalltalk')->getLastInsID();

        foreach ($novel_list as $key => $value) {
            db('smalltalk_content')->insert(['title'=>$value['name'],'smalltalk_id'=>$smalltalk_id]);
            $smalltalk_content_id = db('smalltalk_content')->getLastInsID();
            foreach ($value['sub_list'] as $k => $val) {
                $file_name = 'novel_'.$key.'_'.$k;
                $audio = saveAndgetSrc(ROOT_PATH."public/static/api/audio/",$file_name);
                db('smalltalk_audio')->insert(['audio_name'=>$val['name'],'audio'=>$audio,'smalltalk_content_id'=>$smalltalk_content_id]);
            }
        }

	    return $smalltalk_id;
	}
}


?>