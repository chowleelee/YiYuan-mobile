<?php 
namespace app\admin\model;
	
class Smalltalk extends \think\Model{
	public function getList(){
		$list = db("smalltalk")
            ->alias('st')
            ->field('st.id,stc.cate_name,st.title as st_title,v.real_name,
                st.price,s.title as sts_title,st.create_time')
            ->join('smalltalk_cate stc','stc.id=st.smalltalk_cate_id')
            ->join('vip v','v.id=st.vip_id')
            ->join('special s','s.id=st.special_id')
            ->order('id asc')
            ->paginate(7);

		return $list;
	}	

	public function getDetails(){
		$details_list = db("smalltalk")
                    ->alias('st')
                    ->field('st.id,stc.title,sta.audio_name')
                    ->join('smalltalk_content stc','stc.smalltalk_id=st.id')
                    ->join('smalltalk_audio sta','sta.smalltalk_content_id=stc.id')
                    ->where('st.id='.input('id'))
                    ->order('id asc')
                    ->paginate(5);
                    
		return $details_list;
	}
}
?>