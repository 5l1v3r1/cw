<?php
namespace Admin\Controller;
use Think\Controller;
class ApiController  extends Controller {
	public function tradeApi(){
		$Model = M('configure_trade');
		$res = $Model->where("id = 1")->find();
		$this->assign('res',$res);
		$Model = M('technologies');
		$teches = $Model->order("sortid asc")->select();
		$i = 1;
		foreach($teches as $k=>$v){
			if($v["sortid"]%100 == 0){
				$techinfo = $techinfo."/*****/<br>";
			}
			if($v["description"] != ""){
				$item = "[".$i."] ".$v["content"]." /* ".$v["description"]." */;  <br>";
			}else{
				$item = "[".$i."] ".$v["content"]."".$v["description"].";  <br>";
			}
			$techinfo = $techinfo.$item;
			$i = $i + 1;
		}
		$this->assign('techinfo',$techinfo);
		$this->display(T('admin/apipage'));
	}

}
