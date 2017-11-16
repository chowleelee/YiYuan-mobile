<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function saveAndgetSrc($path,$imgName){
    /*
    Array ( 
        [image-thumb] => Array ( 
            [name] => 01.jpg 
            [type] => image/jpeg 
            [tmp_name] => E:\wamp64\tmp\php4252.tmp 
            [error] => 0 
            [size] => 13996 
        ) 
    )
    */
    $file_type = explode('/',$_FILES[$imgName]['type']);
    $name = time().rand(10000,999999).'.'.$file_type[1];
    $save_path = $path.$name;
    copy($_FILES[$imgName]['tmp_name'], $save_path);
    return $name;
}