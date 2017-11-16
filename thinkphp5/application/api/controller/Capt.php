<?php
namespace app\api\controller;
use \think\captcha\Captcha;

class Capt extends \think\Controller
{
    public function index()
    {
        $type = input('type');
        if($type == 'delete'){
            db('cap')->where("phone=".input('phone'))->delete();
        }else{
            $phone = input('phone');
            $phoneCap = input('phoneCap');

            if(db('cap')->where("phone='$phone'")->value('cap') == $phoneCap){
                db('cap')->where("phone='$phone'")->delete();
                return 1;
            }else{
                return 0;
            }
        }
    }

    public function setCaptcha()
    {
        $id = input('id');
        $captcha = new Captcha();
        // 没有混淆线
        $captcha->useCurve = false;

        $captcha->useNoise = false;

        $captcha->length = 4;

        // 生成
        return $captcha->entry($id);
    }

    public function save(){
        $phone = input('phone');
        $imgCap = input('imgCap');
        $id = input('rand');
        
        if(!$this->checkVerify($imgCap, $id)){
            return 0;
        };

        $cap = rand(1000,9999);

        /*if(db('cap')->where("phone='$phone'")->find()){
            db('cap')->where("phone='$phone'")->setField('cap', $cap);
        }else{
            db('cap')->insert(['phone'=>$phone,'cap'=>$cap]);
        }*/
        db('cap')->insert(['phone'=>$phone,'cap'=>$cap]);

        return json($cap);
    }

    public function checkVerify($code, $id){
        $captcha = new Captcha();
        return $captcha->check($code, $id);
    }
}
