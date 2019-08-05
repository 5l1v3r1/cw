<?php
namespace Admin\Controller;
use Think\Controller;
class FinanceController extends CommonController {
	public function assetsPage(){

		$this->display(T('admin/finance_assets_page'));

	}
	public function assetsAddPage(){
		echo "add";
		$this->display(T('admin/finance_assets_add'));

	}


}
