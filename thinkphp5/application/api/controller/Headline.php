<?php
namespace app\api\controller;
use \think\Session;

class Headline extends \think\Controller
{
	function index()
	{
		$id = input('id');
		if($id == 'all'){
			$list = db("headline")
				->alias('h')
				->field('h.id,h.title,h.summary,h.content,h.create_time,h.vip_id,h.heading_img,v.real_name,v.identity')
				->join('vip v','h.vip_id = v.id')
				->order('create_time desc')
				->paginate(8);
		}else{
			$list = db("headline")
				->alias('h')
				->field('h.id,h.title,h.summary,h.content,h.create_time,h.vip_id,h.heading_img,v.real_name,v.identity')
				->join('vip v','h.vip_id = v.id')
				->order('create_time desc')
				->where("vip_id='$id'")
				->paginate(8);
		}
		return json($list);
	}
	function read(){
		$type = input('type');
		if($type== 'headline'){
			$headline_id = input('id');
			$list = db("headline")
					->alias('h')
					->field('h.id,h.title,h.summary,h.content,h.create_time,h.vip_id,v.head_img,v.real_name,v.identity,v.is_real')
					->join('vip v','h.vip_id = v.id')
					->where("h.id=".$headline_id)
					->find();
			return json($list);
		}else if($type == 'other_headline'){
			$headline_id = input('id');
			//$vip_id = db('headline')->where("id=".$headline_id)->value('vip_id');
			$list = db("headline")
					->alias('h')
					->field('h.id,h.title,h.summary,v.real_name,v.identity')
					->join('vip v','h.vip_id = v.id')
					->where("h.id!=".$headline_id/*." and h.vip_id='$vip_id'"*/)
					->limit(3)
					->order('rand()')
					->select();
			return json($list);
		}else if($type == 'vip'){
			$vip_id = input('id');
			$headline = db("headline")
						->where('vip_id',$vip_id)
						->field('id,title,summary,create_time')
						->find();
			return json($headline);
		}
		
	}

	function save(){
	    $info = $_POST;
	    //$vip_id = Session::get('vip_id');
	    //$vip_id = input('vid');

	    //$info['vip_id'] = $vip_id;
	    $info['create_time'] = time();

	    //$name = saveAndgetSrc($_SERVER['DOCUMENT_ROOT'].'/project/fenda/vue/static/img/','file');
	    //$info['heading_img'] = '/static/img/'.$name;

	    $name = saveAndgetSrc(ROOT_PATH."public/static/api/img/",'file');
	    $info['heading_img'] = $name;

	    unset($info['type']);
	    db('headline')->insert($info);

	    $id = db('headline')->getLastInsID();
	    return $id;
	}
	
}


?>