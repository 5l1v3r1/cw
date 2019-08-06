<?php
namespace Admin\Controller;
use Think\Controller;
class FinanceController extends CommonController {
	public function assetsPage(){
		$Model = M('finance_assets');
		$assets = $Model ->select();
		$total = array();
		$account_sum = 0;
		$interest_sum = 0;
		foreach($assets as $k=>$v){
			$datetime_start = new \DateTime($v["startdate"]);
			$datetime_end = new \DateTime($v["enddate"]);
			$daystep = $datetime_start->diff($datetime_end)->days + 1;
			//print($daystep);
			$ME = M('configure_exchange');
			$cc['currency'] = $v['moneytype'];
			$item = $ME->where($cc)->find();
			$v["interest"] = round($v["amount"] * $item['rating']* ($v["year_rate"] * 0.01/365) *$daystep,3);
			$assets[$k] = $v;
			$account_sum = $v["amount"] * $item['rating'] + $account_sum;
			$interest_sum = $interest_sum + $v["interest"];
			
		}
		$total["account"] = $account_sum;
		$total["interest"] = $interest_sum;
		$total["total"] = $account_sum + $interest_sum;
		//print_r($assets[$k]);
		$this->assign('total',$total);
		$this->assign('assets',$assets);
		$this->display(T('admin/finance_assets_page'));

	}
	public function assetsAddPage(){
		echo "add";
		$this->display(T('admin/finance_assets_add'));

	}
	public function addAssets(){
		$data['name'] = I('post.name');//description
		$data['bank'] = I('post.bank');//sort id docurls
		$data['moneytype'] = I('post.moneytype');//sort id
		$data['amount'] = I('post.amount');//description
		$data['year_rate'] = I('post.yearrate');//sort id docurls
		$data['startdate'] = I('post.startdate');//sort id
		$data['enddate'] = I('post.enddate');
		$data['attr'] = I('post.attr');
		$data['description'] = I('post.description');
		$Model = M('finance_assets');
		$maxid = $Model->max('id');
		$data['id'] = $maxid + 1;
		$Model->data($data)->add();
		//print_r($data);
		$this->success('Add Assets successfully!',U('Finance/assetsPage'),1);
	}
	public function editAssetsPage(){
		//finance_assets_edit.html
		$id = I('get.id');
		$Model = M('finance_assets');
		$cond['id'] = $id;
		$result = $Model->where($cond)->find($cond);
		//dump($result);
		$this->assign('assets',$result);
		$this->display(T('admin/finance_assets_edit'));
	}
	public function editAssets(){
		$cond['id'] = I('post.id');
		$data['name'] = I('post.name');//description
		$data['bank'] = I('post.bank');//sort id docurls
		$data['moneytype'] = I('post.moneytype');//sort id
		$data['amount'] = I('post.amount');//description
		$data['year_rate'] = I('post.yearrate');//sort id docurls
		$data['startdate'] = I('post.startdate');//sort id
		$data['enddate'] = I('post.enddate');
		$data['description'] = I('post.description');

		$Model = M('finance_assets');
		$flag = $Model->where($cond)->save($data);
		$this->success('Update Assets '. $data['name'] .' successfully!',U('Finance/assetsPage'),1);
	}
	public function delassets(){
		$cond['id'] = I('get.id');
		$Model = M('finance_assets');
		$Model->where($cond)->delete();
		$this->success('Delete Assets successfully!',U('Finance/assetsPage'),1);
	}
	public function getAssetsPercent(){
		$type = I('post.type','','htmlspecialchars');//;
		$type = "bank";
		$M = M('finance_assets');
		$types = $M->field($type)->group($type)->select();
		$dataset = [];
		foreach($types as $k=>$v){
			//print_r($v);
			$cond = [];
			$cond[$type] = $v[$type];
			$assets = $M->where($cond)->select();
			$account_sum = 0;
			$interest_sum = 0;
			foreach($assets as $k=>$v){
				$datetime_start = new \DateTime($v["startdate"]);
				$datetime_end = new \DateTime($v["enddate"]);
				$daystep = $datetime_start->diff($datetime_end)->days + 1;
				//print($daystep);
				$ME = M('configure_exchange');
				$cc['currency'] = $v['moneytype'];
				$item = $ME->where($cc)->find();
				$v["interest"] = round($v["amount"] * $item['rating']* ($v["year_rate"] * 0.01/365) *$daystep,3);
				$assets[$k] = $v;
				$account_sum = $v["amount"] * $item['rating'] + $account_sum;
				$interest_sum = $interest_sum + $v["interest"];

			}
			$room["label"] = $v[$type];
			$room["count"] =  $account_sum + $interest_sum;
			array_push($dataset,$room);
			//print_r($room);
			//echo "<br>";
			//echo $v[$type];
		}
		/*$dataset = [];
		foreach($res as $k=>$v){
			//$room = [];
			//echo $v;
			$room["label"] =  $v["month"];
			$room["count"] =  $v[$fy];
			//$room["profit"] =  $year_profitarray[$v["label"]];
			array_push($dataset,$room);
		}*/
		$this->ajaxReturn($dataset);
	}

}
