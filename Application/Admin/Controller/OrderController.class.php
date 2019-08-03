<?php
namespace Admin\Controller;
use Think\Controller;
class OrderController extends CommonController {
	public function orderlist()
	{
		$pp = 0;
		if(isset($_GET["p"])){
			$pp = I('get.p');
		}
		$flag =3;
		if(isset($_GET["flag"])){
			$flag =I('get.flag');;
		}
		//$fromdate = date('Y-m-d',strtotime('1016-01-30'));//
		//$todate = date('Y-m-d',strtotime('3016-01-30'));//
		/*$fromdate = "1000-10-10 00:00:00";
		//echo $fromdate;
		$todate = "3000-10-10 00:00:00";
		$newfrom = "";
		$newto = "";
		if(isset($_GET["fromdate"]) && $_GET["fromdate"] != ""){
			$fromdate = $_GET["fromdate"];
			$newfrom = $fromdate;

		}
		if(isset($_GET["todate"]) && $_GET["todate"] != ""){
			$todate = $_GET["todate"];
			$newto = $todate;
		}*/
		//echo $todate;
		$se_condition = "";
		$se_conditionall = "";
		$search = "";
		$order_conditon = "";

		if(!empty(I('get.search'))){
			$search = trim(I('get.search'));
			//echo strpos($search,"DD:",0); //CD: createdate DD
			//echo strpos($search,"DD:");
			//exit(0);
			//print_r($search);
			if($search == "warning"){
				$se_condition = 'AND ((db_worker_order.w_state = 1) AND now() >= date_sub(db_worker_order.w_deadline,interval 24 hour))';
				$se_conditionall = '((db_worker_order.w_state = 1) AND now() >= date_sub(db_worker_order.w_deadline,interval 24 hour))';
			}else if($search == "paylate"){
				$se_condition = 'AND (((db_worker_order.w_state = 2 OR db_worker_order.w_state = 3) AND db_guest_order.g_state != 2 ) AND now() >= date_add(db_worker_order.w_deadline,interval 72 hour))';
				$se_conditionall = '(((db_worker_order.w_state = 2 OR db_worker_order.w_state = 3) AND db_guest_order.g_state != 2 ) AND now() >= date_add(db_worker_order.w_deadline,interval 72 hour))';
			}
			else if($search == "unpaid"){
				$order_conditon = "db_workers.wxid asc,";
				$se_condition = 'AND (db_worker_order.w_state = 2 AND db_guest_order.g_state = 2 )';
				$se_conditionall = '(db_worker_order.w_state = 2 AND db_guest_order.g_state = 2)';
			}
			else if($search == "nogive"){
				$se_condition = 'AND (db_worker_order.w_state = 4 )';
				$se_conditionall = '(db_worker_order.w_state = 4)';
			}
			else if($search == "unsetting"){
				$se_condition = 'AND (db_worker_order.w_state = 0 or  db_worker_order.w_state is null)';
				$se_conditionall = '(db_worker_order.w_state = 0 or  db_worker_order.w_state is null)';
			}else if($search == "wdoing"){
				$se_condition = 'AND (db_worker_order.w_state = 1 )';
				$se_conditionall = '(db_worker_order.w_state = 1)';
			}
			else if(strpos($search,"CD:") !== false){
				//echo "aa";
				//exit();
				$search = str_replace("CD:","",$search);
				//echo $dd;
				//exit();
				$se_condition = 'AND (db_orders.createtime like "%'.$search.'%")';
				$se_conditionall = '(db_orders.createtime like "%'.$search.'%" )';
			}
			else if(strpos($search,"DD:") !== false){
				//echo "aa";
				//exit(0);
				$search = str_replace("DD:","",$search);
				//echo $search;
				//exit(0);
				$se_condition = 'AND (db_worker_order.w_deadline like "%'.$search.'%")';
				$se_conditionall = '(db_worker_order.w_deadline like "%'.$search.'%")';
			}
			else{

				$se_condition = 'AND (db_guests.wxid Like "%'.$search.'%" OR db_guests.wxname Like "%'.$search.'%" OR db_workers.wxid like "%'.$search.'%" OR db_workers.wxname Like "%'.$search.'%")';
				$se_conditionall = '(db_guests.wxid Like "%'.$search.'%" OR db_guests.wxname Like "%'.$search.'%" OR db_workers.wxid like "%'.$search.'%" OR db_workers.wxname Like "%'.$search.'%")';
			}

			// 赋值分页输出
			//print($se_condition);

		}
		$Model = M('orders');
		$orderinfolist = [];
		$count = 0;
		/*

			0. guest have no paid gurrentee
			1. guest have paid gurrentee
			2. guest have pain all money
			-->
			<!--
			0. no
			1. worker is doing
			4. worker is completed and no give
			2. worker has completed and no pay
			3. worker has completed and paid
		*/
		switch($flag){
			case 1:
				//echo "completed orders";
				$orderinfolist = $Model->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_orders.paymethod,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_guest_order.g_state = 2 AND db_worker_order.w_state = 3 '.$se_condition)->order(''.$order_conditon.'db_orders.createtime desc')->page($pp.',20')->select();
				$count = $Model->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_guest_order.g_state = 2 AND db_worker_order.w_state = 3 '.$se_condition)->count();
				break;
			case 2:
				//echo "incomplte orders";
				//echo "completed orders";
				//$todate = "2017-12-25 00:00:00";
				$orderinfolist = $Model->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_orders.paymethod,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('(db_guest_order.g_state != 2 OR db_worker_order.w_state != 3) '.$se_condition)->order(''.$order_conditon.'db_orders.createtime asc')->page($pp.',20')->select();
				$count = $Model->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('(db_guest_order.g_state != 2 OR db_worker_order.w_state != 3) '.$se_condition)->count();
				break;
			case 4:
				//echo "warning incomplte orders";
				//echo "completed orders";
				//$todate = "2017-12-25 00:00:00";
				$orderinfolist = $Model->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_orders.paymethod,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('(db_worker_order.w_state = 1 or db_worker_order.w_state = 4) AND now() >= date_sub(db_worker_order.w_deadline,interval 24 hour) '.$se_condition)->order(''.$order_conditon.'db_orders.createtime asc')->page($pp.',20')->select();
				$count = $Model->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('(db_worker_order.w_state = 1 or db_worker_order.w_state = 4) AND now() >= date_sub(db_worker_order.w_deadline,interval 24 hour) '.$se_condition)->count();
				break;
			default:
				//$orderinfolist = $Model->select();db_workers.wxname
				//$orderinfolist = $Model->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->field('db_orders.orderid')->select();
				//echo $fromdate;
				//$fromdate = "2017-12-25 00:00:00";
				//$todate = "2017-12-28 00:00:00";
				$orderinfolist = $Model->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_orders.paymethod,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where($se_conditionall)->order(''.$order_conditon.'db_orders.createtime desc')->page($pp.',20')->select();
				$count = $Model->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where($se_conditionall)->count();

				//echo "hahah";
				//echo "all";
		}
		//$resarray = [];
		/*
		* warning step 0 : >24
		* warning step 1 : 12 - 24
		* warning step 2 : 0 - 12
		* warning step 3 : high level
		*/
		$td = time(); //当前时间
		foreach($orderinfolist as $k=>$v){
			$step = round(($td-strtotime($v["w_deadline"]))/3600,2);//hours
			if( $step < 0 && abs($step) <= 24 && abs($step) >12  && ($v["w_state"] == 1 || $v["w_state"] == 4)){
				$v["warningflag"] = 1;
			}else if( $step < 0 && abs($step) <=12 && ($v["w_state"] == 1 || $v["w_state"] == 4)){
				$v["warningflag"] = 2;
			}
			else if($step >= 0 && ($v["w_state"] == 1 || ($v["w_state"] == 1 || $v["w_state"] == 4))){

				$v["warningflag"] = 3;
			}else{
				$v["warningflag"] = 0;
			}
			$gstep =  round(($td-strtotime($v["w_deadline"]))/3600,2);//hours
			if($gstep > 0 && abs($gstep) >= 24 *2 && abs($gstep) < 24 *3 && ($v["w_state"] == 2 || $v["w_state"] == 3) && ($v["g_state"] != 2)){
				$v["gwarningflag"] = 1;

			}else if($gstep > 0 && abs($gstep) >= 24 *3 && abs($gstep) < 24 *4 && ($v["w_state"] == 2 || $v["w_state"] == 3) && ($v["g_state"] != 2) ){
				$v["gwarningflag"] = 2;

			}else if($gstep > 0 && abs($gstep) >= 24 *4 && ($v["w_state"] == 2 || $v["w_state"] == 3)  && ($v["g_state"] != 2)){
				$v["gwarningflag"] = 3;

			}else{
				$v["gwarningflag"] = 0;
			}
			//echo $step;
			//print_r($v);
			//echo "<br>";
			$orderinfolist[$k] = $v;
		}
		//print_r($orderinfolist);
		//$orderinfolist = arraySequence($orderinfolist, 'warningflag', $sort = 'SORT_DESC');
		/* ongoing orders*/

		//dump($orderinfolist);
		//echo $count;
		$Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
		$Page->setConfig('prev','last');
		$Page->setConfig('next','next');
		$Page->setConfig('first','first');
		$Page->setConfig('last','last');
		$Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER% ');
		$show = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('orders',$orderinfolist);//
		/*search default*/
		$this->assign('search',I('get.search'));
		$this->assign('fflag',$flag);// 赋值分页输出
		$this->assign('newfrom',$newfrom);// 赋值分页输出
		$this->assign('newto',$newto);// 赋值分页输出
		$this->display(T('admin/orders_all'));
	}
	public function orderaddpage(){
		$Model = M('workers');
		$workers = $Model->field("wxid as email,wxname as name")->where("status = 0")->order('wxname asc')->select();
		$this->assign('workers',$workers);
		/* get technology list*/
		$Model = M('technologies');
		$teches = $Model->field("techid as email,content as name")->select();
		$this->assign('teches',$teches);

		$flag =3;
		if(isset($_GET["flag"])){
			$flag =I('get.flag');
		}

		$this->assign('fflag',$flag);// 赋值分页输出
		$this->display(T('admin/orders_add'));
	}
	public function ordernew(){
		$projectname = "";
		if(!empty(I('post.projectname'))){
			// projectname is [1,2,3,4,5]
			$digitstr = "";
			$projectnum = I('post.projectname');
			foreach($projectnum as $v){
				if (ctype_digit($v)) {
					//echo "The string $testcase consists of all digits.\n";

					if ($digitstr == "") {
						$digitstr = $v;
					}else{
						$digitstr = $digitstr.",".$v;
					}

				} else {
					if($projectname == ""){
						$projectname = $v;
					}else{
						$projectname = $projectname."^".$v;
					}

				}
			}

			//print($digitstr);
			if(strlen($digitstr) >0){
				//print_r($digitstr);
				$Model = M('technologies');
				$m['techid'] = array('in',$digitstr);
				//print_r($m);
				$techinfos = $Model->field("techid,content")->where($m)->select();
				//print_r($techinfos);
				$keywordsid = "";
				foreach($techinfos as $k=>$v){
						//echo $v[content];
						$keywordsid = $keywordsid."#".$v["techid"];
						if($projectname == ""){
							$projectname = $v[content];
						}else{
							$projectname = $projectname."^".$v[content];
						}
				}
				$projectname = $projectname."$".$keywordsid;
			}

		}
		//print($projectname);
		$orderid = uniqid('cs_');
		$data['orderid'] = $orderid;
		$data['createtime'] = date('Y-m-d H:i:s',time());//
		$data['projectname'] = $projectname;//
		$data['paymethod'] = I('post.paymethod','','htmlspecialchars');;//
		$data['moneytype'] = I('post.moneytype','','htmlspecialchars');//
		$data['totalprice'] = I('post.totalprice','','htmlspecialchars');//
		$data['guarantee'] = I('post.guarantee','','htmlspecialchars');//
		$data['description'] = I('post.description','','htmlspecialchars');//
		//dump($data);
		$Model = M('orders');
		$Model->data($data)->add();
		/*guest*/
		$cond['wxid'] = trim(I('post.guest_wxid','','htmlspecialchars'));//
		$guest_wxid = $cond['wxid'];
		$cell['wxname'] = trim(I('post.guest_wxname','','htmlspecialchars'));//
		$Model = M('guests');
		//dump($cond);
		$guestinfo = $Model->where($cond)->find();
		//dump($guestinfo);
		if(!empty($guestinfo)){
			//echo "nonull";
			$Model->where($cond)->save($cell);
		}else
		{
			//echo "null";
			$cell['wxid'] = $guest_wxid;
			$Model->data($cell)->add();
		}
		/*guest_order*/
		$Model = M('guest_order');
		$go['wxid'] = $guest_wxid;
		$go['orderid'] = $orderid;
		$go['g_deadline'] = I('post.g_deadtime','','htmlspecialchars');//
		$go['g_state'] = I('post.g_state','','htmlspecialchars');//
		$Model->data($go)->add();
		/* worker_order */
		$workers = I('post.wxid','','htmlspecialchars');
		$map['wxid'] = "";
		if(count($workers)>0){
			$map['wxid'] = $workers[0];
		}
		$map['orderid'] = $orderid;//
		$map['w_deadline'] = I('post.w_deadline','','htmlspecialchars');//
		$map['w_payment'] = I('post.w_payment','','htmlspecialchars');//
		$map['w_state'] = I('post.w_state','','htmlspecialchars');//

		//dump($map);
		if($map['wxid'] != ""){
			$Order = M('worker_order');
			$Order->data($map)->add();
		}
		$flag =2;
		if(isset($_GET["flag"])){
			$flag =I('get.flag');
		}
		$this->assign('fflag',$flag);// 赋值分页输出
		$this->success('Add a new order successfully!',U('Order/orderlist?flag='.$flag),1);
	}
	public function ordereditpage(){
		$encurl= I('get.encurl');//encurl;

		$orderid = I('get.orderid','','htmlspecialchars');//
		//dump($condition);
		$Model = M('orders');
		$orderinfo = $Model->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_orders.paymethod,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_orders.orderid =  "'.$orderid.'"' )->find();
		//dump($orderinfo);

		$Model = M('workers');
		//echo $orderinfo[wxid];

		$mmap['status'] = 0;
		$workers = $Model->field("wxid as email,wxname as name")->where($mmap)->order('wxname asc')->select();
		$this->assign('workers',$workers);
		$mmap['wxid'] = $orderinfo[wxid];
		$worker = $Model->field("wxid as email,wxname as name")->where($mmap)->select();
		$this->assign('worker',$worker);
		//print($worker);

		/* get technology list*/
		$Model = M('technologies');
		$teches = $Model->field("techid as email,content as name")->select();
		$this->assign('teches',$teches);
		/* projectname*/
		//print_r($orderinfo);
		$resstr = array();
		$resstr = explode('$', $orderinfo["projectname"]);
		//echo $resstr[1];
		$digitstr = "";
		$title ="";
		if(count($resstr)>=2){
			$techids = explode('#', $resstr[count($resstr)-1]);
			$title = $resstr[0];
			//print($techids);
			foreach($techids as $v){
				if (ctype_digit($v)) {
					//echo "The string $testcase consists of all digits.\n";

					if ($digitstr == "") {
						$digitstr = $v;
					}else{
						$digitstr = $digitstr.",".$v;
					}

				}
			}
		}else if(count($resstr) == 1){
			$title = $resstr[0];
		}

		//echo $title;
		$techinit = array();
		if(strlen($digitstr)>0){
			//echo $digitstr;
			$m['techid'] = array('in',$digitstr);
			//print_r($m);
			$techinit = $Model->field("techid as email,content as name")->where($m)->select();

		}

		if($title != ""){
			$titlearrs = explode('^', $title);
			$techtmp = array();
			if(count($techinit) == 0){
				$techtmp = array();
			}else{
				$techtmp = $techinit;
			}

			//print_r(count($techtmp));
			//echo $title;
			//print_r($titlearrs);
			//print_r($titlearrs);
			foreach($titlearrs as $v){

				if(count($techinit)>0){
					$cff = 0;
					foreach($techinit as $k){
						//echo $v;

						if($k["name"] == $v && $v !=""){
							$cff = 1;
							break;
						}
					}
					if($cff == 0){
						$item = array("email" => $v,"name" =>$v);
						array_push($techtmp,$item);
					}else{
						$cff = 0;
					}


				}else{
					//echo $v;
					if($v !=""){
						$item = array("email" => $v,"name" =>$v);
						//print($techtmp);
						array_push($techtmp,$item);
						//print_r($techtmp);

					}
				}

			}
			$techinit = $techtmp;
		}
		//print_r($techtmp);
		$this->assign('techinit',$techinit);
		$flag =3;
		if(isset($_GET["flag"])){
			$flag =I('get.flag');
		}
		$this->assign('encurl',$encurl);
		$this->assign('fflag',$flag);// 赋值分页输出
		$this->assign("orderinfo",$orderinfo);
		$this->display(T('admin/orders_edit'));
		/*
		$Model = M('workers');
        $workers = $Model->select();
		$this->assign('workers',$workers);
        $this->display(T('admin/orders_add'));
				*/
	}
	public function orderupdate(){
		$encurl= I('get.encurl');//encurl;
		$decurl = base64_decode(str_replace(array('-', '_'), array('+', '/'),$encurl));
		$tmp = str_replace(__APP__."/","",$decurl);
		$newpara = str_replace("Order/orderlist/","",$tmp);
		$projectname = "";
		if(!empty(I('post.projectname'))){
			// projectname is [1,2,3,4,5]
			$digitstr = "";
			$projectnum = I('post.projectname');
			foreach($projectnum as $v){
				if (ctype_digit($v)) {
					//echo "The string $testcase consists of all digits.\n";

					if ($digitstr == "") {
						$digitstr = $v;
					}else{
						$digitstr = $digitstr.",".$v;
					}

				} else {
					if($projectname == ""){
						$projectname = $v;
					}else{
						$projectname = $projectname."^".$v;
					}

				}
			}

			//print($digitstr);
			if(strlen($digitstr) >0){
				//print_r($digitstr);
				$Model = M('technologies');
				$m['techid'] = array('in',$digitstr);
				//print_r($m);
				$techinfos = $Model->field("techid,content")->where($m)->select();
				//print_r($techinfos);
				$keywordsid = "";
				foreach($techinfos as $k=>$v){
						//echo $v[content];
						$keywordsid = $keywordsid."#".$v["techid"];
						if($projectname == ""){
							$projectname = $v[content];
						}else{
							$projectname = $projectname."^".$v[content];
						}
				}
				$projectname = $projectname."$".$keywordsid;
			}

		}
		$orderid = I('post.orderid','','htmlspecialchars');
		$ORDER = M('orders');
		$condition['orderid'] = $orderid;
		$orderinfo = $ORDER->where($condition)->find();
		$flag =3;
		if(isset($_GET["flag"])){
			$flag =I('get.flag');
		}

		//dump($orderinfo);
		if(!empty($orderinfo)){
			$data['projectname'] = $projectname;//
			$data['paymethod'] = I('post.paymethod','','htmlspecialchars');//
			$data['moneytype'] = I('post.moneytype','','htmlspecialchars');//
			$data['totalprice'] = I('post.totalprice','','htmlspecialchars');//
			$data['guarantee'] = I('post.guarantee','','htmlspecialchars');//
			$data['description'] = I('post.description','','htmlspecialchars');//
			$ORDER->where($condition)->save($data);
			$GUEST = M('guests');
			$cond['wxid'] = I('post.guest_wxid','','htmlspecialchars');//
			$guestinfo = $GUEST->where($cond)->find();

			if(!empty($guestinfo)){
				//echo "nonull";
				$cell['wxname'] = I('post.guest_wxname','','htmlspecialchars');//
				$GUEST->where($cond)->save($cell);

				/*guest_order*/
				$GUESTORDER = M('guest_order');
				$go['wxid'] = $cond['wxid'];
				$go['orderid'] = $orderid;
				$goadd['g_deadline'] = I('post.g_deadtime','','htmlspecialchars');//
				$goadd['g_state'] = I('post.g_state','','htmlspecialchars');//
				$guestorders = $GUESTORDER->where($go)->find();
				if(!empty($guestorders)){
					$GUESTORDER->where($go)->save($goadd);
					//dump($guestinfo);
					/*worker*/
					/* worker_order */
					$workers = I('post.wxid','','htmlspecialchars');//
					$map['wxid'] = "";
					if(count($workers)>0){
						$map['wxid'] = $workers[0];
					}
					$map['orderid'] = $orderid;//
					$mapadd['w_deadline'] = I('post.w_deadline','','htmlspecialchars');//
					$mapadd['w_payment'] = I('post.w_payment','','htmlspecialchars');//
					$mapadd['w_state'] = I('post.w_state','','htmlspecialchars');//


					$WORKEROORDER = M('worker_order');

					if($map['wxid'] != ""){
						//echo $map['wxid'];
						//echo $map['orderid'];
						$mapadd['wxid'] = $map['wxid'];
						$connd['orderid'] = $map['orderid'];
						$workerorders = $WORKEROORDER->where($connd)->select();
						if(count($workerorders)>0){
							$WORKEROORDER->where($connd)->save($mapadd);
						}else{
							$mapadd['orderid'] = $map['orderid'];
							$WORKEROORDER->add($mapadd);
						}
						//dump($ii);
					}else
					{
						$map['wxid'] = I('post.oriid','','htmlspecialchars');//
						$WORKEROORDER->where($map)->delete();
					}
					//$this->success('Update order #'.$orderid.' successfully!',U('Order/orderlist?flag='.$flag),1);
					$this->success('Update order #'.$orderid.' successfully!',U('Order/orderlist/'.$newpara),1);


				}else{
						$this->success('Update order #'.$orderid.' successfully!',U('Order/orderlist/'.$newpara),1);
				}
				//$Model->data($go)->add();

			}else
			{
					$this->success('Update order #'.$orderid.' successfully!',U('Order/orderlist/'.$newpara),1);

			}
		}else{
				$this->success('Update order #'.$orderid.' successfully!',U('Order/orderlist/'.$newpara),1);
		}
		/*
		$data['projectname'] = I('post.projectname','','htmlspecialchars');//
		$data['moneytype'] = I('post.moneytype','','htmlspecialchars');//
		$data['totalprice'] = I('post.totalprice','','htmlspecialchars');//
		$data['guarantee'] = I('post.guarantee','','htmlspecialchars');//
		$data['description'] = I('post.description','','htmlspecialchars');//
		*/
		//dump($data);
	}
	public function orderdelete(){
		$flag =3;
		if(isset($_GET["flag"])){
			$flag =I('get.flag');
		}
		$data['orderid'] = I('get.orderid');
		$Model = M('orders');
		$Model->where($data)->delete();
		/*if(isset($_GET["go"]) && $_GET["go"] == 1){
			//$this->error('Update order #'.$orderid.' failure!',U('Order/orderlist_ongoing'),1);
			$this->success('Delete order #'.$data['orderid'].' successfully!',U('Order/orderlist?fflag=2'),1);
		}else{
			$this->success('Delete order #'.$data['orderid'].' successfully!',U('Order/orderlist'),1);
		}*/
		$this->success('Delete order #'.$data['orderid'].' successfully!',U('Order/orderlist?flag='.$flag),1);
		//$this->success('Delete order #'.$data['orderid'].' successfully!',U('Order/orderlist'),1);
	}
	public function orderdetailpage()
	{
		$flag =3;
		if(isset($_GET["flag"])){
			$flag =I('get.flag');
		}
		$orderid = I('get.orderid');
		$Model = M('orders');
		$orderinfo = $Model->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_orders.orderid =  "'.$orderid.'"' )->find();
		//dump($orderinfo);
		$this->assign("orderinfo",$orderinfo);
		$this->assign('fflag',$flag);
		$this->display(T('admin/orders_detail'));

	}
	public function orderremark(){
		$flag =3;
		if(isset($_GET["flag"])){
			$flag =I('get.flag');
		}
		$orderid = I('get.orderid');
		$Model = M('guest_order');
		$cond['orderid'] = $orderid;
		$cell['remark'] = I('post.remarkoption');
		$Model->where($cond)->save($cell);
		$this->success('Remark order #'.$cond['orderid'].' successfully!',U('Order/orderlist?flag='.$flag),1);
	}
	public function ajaxRecommand(){
		//$data = 'ok';
		$data = I('post.data');
		//$data = array(array("email"=>"1","name"=>"php"),array("email"=>"9","name"=>"C"));


		$res = Recommand($data);

		$this->ajaxReturn($res);
	}
	public function ajaxGetGwname(){
		//$data = 'ok';
		$Model = M('guests');
		$map['wxid'] = I('post.data');
		$res = $Model->field("wxname")->where($map)->find();
		$this->ajaxReturn($res);

		//


		//$res = Recommand($data);

		//$this->ajaxReturn($res);
	}
	public function showDataAnalysisPage(){
		$res = [];
		$res = getAllData();
		$this->assign('revenues',$res[0]);
		$this->assign('salary',$res[1]);

		$this->assign('revenuesarr',$res[3]);
		$this->assign('ordernum',$res[4]);
		$this->assign('ongoingrevenues',$res[5]);
		$this->assign('ongoingsalary',$res[6]);
		$this->assign('ongoingprofit',$res[7]);
		$this->assign('ongoingrevenuesarr',$res[8]);
		$this->assign('profitavg',$res[9]);
		$this->assign('ongoingunpaid',$res[10]);
		$this->assign('ongoingdoing',$res[11]);
		$this->assign('ongoingunset',$res[12]);

		$DATEYEAR = date("Y");
		/*[createyear] => [salarysum] => 0 [revenuesum] => 0 [moneyinfo] => [profitsum] => 0 [ordernum] => 0 [profitavg] => 0 [datas] => Array ( ) )*/
		$salarysum = 0;
		$revenuesum = 0;
		$profitsum  = 0;
		$ordernum = 0;
		$daystep = 0;
		$datas = [];
		for($i=C(DATEORIYEAR);$i<=$DATEYEAR;$i++){
				$cydata = [];
				$cydata = getYearData($i);
				$profitsum = $profitsum + $cydata["profitsum"];
				$ordernum = $ordernum + $cydata["ordernum"];
				$daystep = $daystep + $cydata["daystep"];
				if($cydata["ordernum"] == 0){
					$cydata["profitperorder"] =  0;
				}else{
					$cydata["profitperorder"] = round($cydata["profitsum"]/$cydata["ordernum"],2);
				}

				array_push($datas,$cydata);
		}
		$cydata["createyear"] = "Total" ;
		if($ordernum == 0){
			$cydata["profitperorder"] = 0;
		}else{
			$cydata["profitperorder"] = round($profitsum/$ordernum,2);
		}

		$cydata["ordernum"] = $ordernum;
		if($daystep == 0){
			$cydata["ordernumavg"] = 0;
			$cydata["profitavg"] = 0;
		}else{
			$cydata["ordernumavg"] = round($ordernum/$daystep,2);
			$cydata["profitavg"] = round($profitsum/$daystep,2);
		}
		$this->assign('profitavg',$cydata["profitavg"]);
		$this->assign('ordernumavg',$cydata["ordernumavg"]);
		$this->assign('profitperorder',$cydata["profitperorder"]);

		array_push($datas,$cydata);
		//print_r($datas);
		$this->assign('datas',$datas);//current day data info

		//print_r($cydata);

		$fromdate = date("Y-m-d");
		$this->assign('today',$fromdate);
		$this->assign('todaymonth',date("Y-m"));
		$this->assign('todayyear',$year);


		$this->display(T('admin/orders_analysis'));

	}
	public function getWeekData(){
		//$fd = I('post.fromdate','','htmlspecialchars');//
		//$td = I('post.todate','','htmlspecialchars');//
		$fd = "2018-01-01";//
		$td = "2019-12-31";//
		if(date('w') == 0){
			$td = date('Y-m-d', (time() + (7 - (date('w') == 0 ? 7 : date('w'))) * 24 * 3600));;
		}else{
			$td = date('Y-m-d', strtotime('-1 sunday', time()));
		}
		//echo $td;
		//date('Y-m-d', (time() + (7 - (date('w') == 0 ? 7 : date('w'))) * 24 * 3600)); //同样使用w,以现在与周日相关天数算
		$res = [];
		$res = getWeekDataOrder($fd,$td);
		$this->ajaxReturn($res);
	}
	/* one year */
    public function getDayToDay(){

      $fd = I('post.fromdate','','htmlspecialchars');//
      $td = I('post.todate','','htmlspecialchars');//
      $DATEYEAR = date("Y",time());

      $res = [];
      for($i=C(DATEORIYEAR);$i<=$DATEYEAR;$i++){
        $fromdate = $i."-".$fd;
        $todate = $i."-".$td;
        $tmp = getDayToDay($fromdate,$todate);
        if(!empty($tmp['values'])){
             array_push($res,$tmp);
        }

      }
      $this->ajaxReturn($res);
    }
	/*get days of potin month*/
    public function getEachMonth(){
      $m = I('post.month','','htmlspecialchars');//
      //$m = "02";
      $res = [];
      $res0 = [];
      //echo C(DATEORIYEAR);
      $DATEYEAR = date("Y",time());
      //echo $DATEYEAR;
      for($i=C(DATEORIYEAR);$i<=$DATEYEAR;$i++){
        //echo $i;
        $fromdate = $i."-".$m."-01";
        $todate = $i."-".$m."-31";
        $res0 = getDayToDayYears($fromdate,$todate);
        if(empty($res0)){
          $res0 = [];
        }
        $res = array_merge($res,$res0);
        //print_r($res0);

      }
      $i = 0;
      for($i = 0; $i<count($res);$i++){
        //echo ($k["name"]);
        //echo (count($k["values"]));
        //print_r($res[$i]["values"]);
        if(count($res[$i]["values"]) == 0){
          unset($res[$i]);
        }
        //echo "<br>";
      }
      //print_r($res);
      $this->ajaxReturn($res);
    }
	public function getMonths(){
        $fy = C(DATEORIYEAR);
        $ty = date("Y",time());
        $res = getMonthsData($fy,$ty,1);
        $this->ajaxReturn($res);
    }
	public function getQdatas(){
        $fy = C(DATEORIYEAR);
        $ty = date("Y",time());
        /*
        q1: 1-31 -3-31
        q2: 4-1  6-30
        q3  7-1  9-30
        q4  10-1 12-31
        */

        /*get Q1*/
        $res1 = getQData($fy,$ty,"Q1",1);
        $res2 = getQData($fy,$ty,"Q2",1);
        $res3 = getQData($fy,$ty,"Q3",1);
        $res4 = getQData($fy,$ty,"Q4",1);
        $res = [];
        array_push($res,$res1,$res2,$res3,$res4);
        //print_r($res);
        $this->ajaxReturn($res);
    }

}
