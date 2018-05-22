<?php
namespace Home\Controller;
use Think\Controller;
class CollectController extends Controller {
    public function index(){

    }

    //获取比赛结果
    public function result()
    {

    }

    //获取竞彩 赛程
    public function schedule(){

    }

    //获取快3开奖数据
    public function k3(){
        $filename="http://www.aicai.com/lottery/kc!kc3.jhtml?time=1395728183377&gameIndex=311";
        $content=file_get_contents($filename);
        echo $content;
    }
}
