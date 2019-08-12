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
		$currency = 0;

		foreach($assets as $k=>$v){
			$datetime_start = new \DateTime($v["startdate"]);
			$datetime_end = new \DateTime($v["enddate"]);
			$daystep = $datetime_start->diff($datetime_end)->days + 1;
			//print($daystep);
			$ME = M('currency_now');
			$cc['para'] = $v['moneytype'];
			$item = $ME->where($cc)->find();
			$v["interest"] = round($v["amount"] * $item['value']* ($v["year_rate"] * 0.01/365) *$daystep,3);
			$assets[$k] = $v;
			//$account_sum = $v["amount"] * $item['rating'] + $account_sum;
			$interest_sum = $interest_sum + $v["interest"];

		}
		$M = M('finance_amounts');
		$amounts = $M ->select();
		foreach($amounts as $k=>$v){
			$ME = M('currency_now');
			$cc['para'] = $v['moneytype'];
			$item = $ME->where($cc)->find();
			$account_sum = round($v["amount"] * $item['value'],3) + $account_sum;
		}
		$total["account"] = $account_sum;
		$total["interest"] = $interest_sum;
		$total["total"] = $account_sum + $interest_sum;
		$ME = M('currency_now');
		$cc['para'] = 'USD';
		$item = $ME->where($cc)->find();
		$currency = $item['value'];
		//print_r($assets[$k]);
		$this->assign('total',$total);
		$this->assign('assets',$assets);
		$this->assign('amounts',$amounts);
		$this->assign('currency',$currency);
		$this->display(T('admin/finance_assets_page'));

	}
	public function assetsAddPage(){
		//echo "add";
		$this->display(T('admin/finance_assets_add'));

	}
	public function amountsAddPage(){
		//echo "add";
		$this->display(T('admin/finance_amounts_add'));

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
	public function addAmounts(){
		$data['bank'] = I('post.bank');//sort id docurls
		$data['moneytype'] = I('post.moneytype');//sort id
		$data['amount'] = I('post.amount');//description
		$data['builddate'] = I('post.builddate');//sort id
		$data['description'] = I('post.description');
		$Model = M('finance_amounts');
		$maxid = $Model->max('id');
		$data['id'] = $maxid + 1;
		$Model->data($data)->add();
		//print_r($data);
		$this->success('Add Amount successfully!',U('Finance/assetsPage'),1);
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
	public function editAmountsPage(){
		//finance_assets_edit.html
		$id = I('get.id');
		$Model = M('finance_amounts');
		$cond['id'] = $id;
		$result = $Model->where($cond)->find($cond);
		//dump($result);
		$this->assign('amounts',$result);
		$this->display(T('admin/finance_amounts_edit'));
	}
	public function editAmounts(){
		$cond['id'] = I('post.id');
		$data['bank'] = I('post.bank');//sort id docurls
		$data['moneytype'] = I('post.moneytype');//sort id
		$data['amount'] = I('post.amount');//description
		$data['builddate'] = I('post.builddate');//sort id
		$data['description'] = I('post.description');
		$Model = M('finance_amounts');
		$flag = $Model->where($cond)->save($data);
		$this->success('Update Amount '. $cond['id'] .' successfully!',U('Finance/assetsPage'),1);

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
		$data['attr'] = I('post.attr');
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
	public function delamounts(){
		$cond['id'] = I('get.id');
		$Model = M('finance_amounts');
		$Model->where($cond)->delete();
		$this->success('Delete Amounts successfully!',U('Finance/assetsPage'),1);
	}
	public function getAssetsPercent(){
		$type = I('post.type','','htmlspecialchars');//;
		//$type = "bank";
		$M = M('finance_assets');
		$types = $M->field($type)->group($type)->select();
		$dataset = [];
		foreach($types as $k=>$v){
			//print_r($v);
			$cond = [];
			$cond[$type] = $v[$type];
			$assets = $M->where($cond)->select();
			//$account_sum = 0;
			$interest_sum = 0;
			foreach($assets as $k=>$v){
				$datetime_start = new \DateTime($v["startdate"]);
				$datetime_end = new \DateTime($v["enddate"]);
				$daystep = $datetime_start->diff($datetime_end)->days + 1;
				//print($daystep);
				$ME = M('currency_now');
				$cc['para'] = $v['moneytype'];
				$item = $ME->where($cc)->find();
				$v["interest"] = round($v["amount"] * $item['value']* ($v["year_rate"] * 0.01/365) *$daystep,3);
				$assets[$k] = $v;
				$interest_sum = $interest_sum + $v["interest"];

			}
			$room["label"] = $v[$type];
			$room["count"] =  $interest_sum;
			array_push($dataset,$room);
			//print_r($room);
			//echo "<br>";
			//echo $v[$type];
		}
		//print_r($dataset);
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
	public function getAmountsPercent(){
		$type = I('post.type','','htmlspecialchars');//;
		//$type = "bank";
		$M = M('finance_amounts');
		$types = $M->field($type)->group($type)->select();
		$dataset = [];
		foreach($types as $k=>$v){
			$account_sum = 0;
			$cond = [];
			$cond[$type] = $v[$type];
			$amounts = $M->where($cond)->select();
			foreach($amounts as $k=>$v){
				$ME = M('currency_now');
				$cc['para'] = $v['moneytype'];
				$item = $ME->where($cc)->find();
				$account_sum = round($v["amount"] * $item['rating'],3) + $account_sum;

			}
			$room = [];
			$room["label"] = $v[$type];
			$room["count"] =  $account_sum;
			array_push($dataset,$room);
		}

		$this->ajaxReturn($dataset);
	}

}
