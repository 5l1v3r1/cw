<?php
namespace Admin\Controller;
use Think\Controller;
class ToolController extends CommonController {
	public function exchangeToolPage(){
		$Model = M('configure_exchange');
		$data["currency"] = "USD";
		$rate = $Model->field("rating")->where($data)->find();
		$this->assign('rate',$rate["rating"]);
		$Model = M('technologies');
		$teches = $Model->field("techid as email,content as name")->select();
		$this->assign('teches',$teches);
		//print_r($rate);
		//$this->assign('currencies',$output);
		$this->display(T('admin/tools_exchange'));

	}
	public function timePie(){
		//$timestr = I('get.currency');
		$data = I('post.data');
		$tzf = CodeToTimeZone($data["ftz"]);
		$ttf = CodeToTimeZone($data["ttz"]);




		$date = new \DateTime($data["timestr"], new \DateTimeZone($tzf));
		//echo $date->format('Y-m-d H:i:s') . "<br>";

		$date->setTimezone(new \DateTimeZone($ttf));
		$target =  $date->format('Y-m-d H:i:s');
		//echo $target;
		$this->ajaxReturn($target);

		//print($times);
		//print_r($teches);
		//$this->display(T('admin/conf_tech_list'));

	}


}
