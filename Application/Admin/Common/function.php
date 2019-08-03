<?php
function encryptDecrypt($key, $string, $decrypt){
   $method = 'DES-ECB';//加密方法
   $options = 0;//数据格式选项（可选）
   $iv = '';//加密初始化向量（可选）
    if($decrypt){
        $decrypted = openssl_encrypt($string, $method, md5(md5($key)), $options);
        return $decrypted;
    }else{
        $encrypted = openssl_decrypt($string, $method, md5(md5($key)), 0);
        return $encrypted;
    }
}
function checkSession(){
  session('admin_uid',null);
  $data['username']= I('post.username','','htmlspecialchars');//get name
  $data['password'] = md5($data['username'].I('post.password','','htmlspecialchars'));//get name
  $data['code'] = I('post.code','','htmlspecialchars');//get code
  $Model = M('admin');
  //$data['uid'] = uniqid('cs_');
  //print_r($data);
  $content = $Model->field('uid,username')->where($data)->find();
  //dump($content);
  if(!empty($content))//exist
  {
    //echo "hahah";
          //session('admin_id',$content['uid'],36000);
          $pwdtxt = encryptDecrypt('3330', uniqid('520_').$content['uid'],1);
          session('admin_uid',$pwdtxt,36000);
          //echo session('admin_uid');
          //exit(0);
          return 1;


  }else
  {
    session('admin_uid',null);
    return 0;
  }
}
/**
 *
 * @params array $array target array
 * @params string $field field
 * @params string $sort flag SORT_DESC ；SORT_ASC
 */
function arraySequence($array, $field, $sort = 'SORT_DESC')
{
    $arrSort = array();
    foreach ($array as $uniqid => $row) {
        foreach ($row as $key => $value) {
            $arrSort[$key][$uniqid] = $value;
        }
    }
    array_multisort($arrSort[$field], constant($sort), $array);
    return $array;
}
/*get year total data
* if year = null  : all data
*/
/*
  get a year data
*/
function getAllData(){
    $ORDER = M('orders');
    $revenuesarr = [];
    $revenues = 0.0;
    $profit = 0;
    $salary = 0;
    $unpaidsalary = 0;
    /* salary with RMB */
    $salary = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->where('db_guest_order.g_state = 2 AND db_worker_order.w_state = 3')->sum('db_worker_order.w_payment');
    $unpaidsalary = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->where('db_guest_order.g_state = 2 AND db_worker_order.w_state = 2')->sum('db_worker_order.w_payment');
    /* usd */
    $revenuesarr  = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.moneytype,SUM(db_orders.totalprice) as revenues')->where('db_guest_order.g_state = 2 AND db_worker_order.w_state = 3')->group('db_orders.moneytype')->select();
    $ordernum = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_guest_order.g_state = 2 AND db_worker_order.w_state = 3')->count();
    $ordermindate = $ORDER->min('createtime');
    /* cal total incomplete income  */
    $ongoingsalary = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->where('db_guest_order.g_state != 2 OR db_worker_order.w_state != 3')->sum('db_worker_order.w_payment');
    $ongoingrevenuesarr  = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.moneytype,SUM(db_orders.totalprice) as revenues')->where('db_guest_order.g_state != 2 OR db_worker_order.w_state != 3')->group('db_orders.moneytype')->select();

    $ongoingunpaid = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_worker_order.w_state = 2')->count();
    $ongoingdoing = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_worker_order.w_state = 1')->count();
    $ongoingunset = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_worker_order.wxid is null')->count();
    if($salary == ''){
        $salary = 0;
    }
    if($ongoingsalary == ''){
        $ongoingsalary = 0;
    }
    //dump($revenuesarr);
    $revenuesarrnew = [];
    foreach($revenuesarr as $k=>$v){
        $Model = M('configure_exchange');
        $cc['currency'] = $v['moneytype'];
        $item = $Model->where($cc)->find();
        $v['rating'] = $item['rating'];
        $revenuesarr[$k] = $v;
        array_push($revenuesarrnew ,$revenuesarr[$k]);
        $revenues = $revenues + $v['revenues']*$item['rating'];
        //echo $v['revenues']*$item['rating'];
    }
    $profit = $revenues - $salary;
    //dump($revenuesarr);
    $ongoingrevenues = 0;
    $ongoingrevenuesarrnew = [];
    foreach($ongoingrevenuesarr as $k=>$v){
        $Model = M('configure_exchange');
        $cc['currency'] = $v['moneytype'];
        $item = $Model->where($cc)->find();
        $v['rating'] = $item['rating'];
        $ongoingrevenuesarr[$k] = $v;
        array_push($ongoingrevenuesarrnew ,$ongoingrevenuesarr[$k]);
        $ongoingrevenues = $ongoingrevenues + $v['revenues']*$item['rating'];
        //echo $v['revenues']*$item['rating'];
    }
    $ongoingprofit = $ongoingrevenues - $ongoingsalary;
    $datetime_start = new \DateTime();
    $datetime_end = new \DateTime($ordermindate);
    $daystep = $datetime_start->diff($datetime_end)->days;
    $profitavg = round($profit/$daystep);
    return array($revenues, $salary, $profit, $revenuesarrnew,$ordernum,$ongoingrevenues,$ongoingsalary,$ongoingprofit,$ongoingrevenuesarrnew,$profitavg,$ongoingunpaid,$ongoingdoing,$ongoingunset,$unpaidsalary);


}
/*get year data*/
function getYearData($year){
	/*year show*/
	$ORDER = M('orders');
	$fromdate = date('Y-m-d', strtotime($year."-01-01")); //start of year
	$todate = date('Y-m-d', strtotime($year."-12-31"));//end of year

    $datetime_start = new \DateTime();
    $datetime_end = new \DateTime($fromdate);

    $daystep = $datetime_start->diff($datetime_end)->days + 1;
    //var_dump($datetime_start->diff($datetime_end));
    $year_ordernum = $ORDER->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->count();
    $year_revenuescc  = $ORDER->field('db_orders.moneytype,SUM(db_orders.totalprice) as revenues')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('db_orders.moneytype')->select();
	$year_revenuesarr  = $ORDER->field('DATE_FORMAT(db_orders.createtime,"%Y-%m") as createday,db_orders.moneytype,SUM(db_orders.totalprice) as revenues')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('DATE_FORMAT(db_orders.createtime,"%Y-%m"),db_orders.moneytype')->select();
	$year_revenuearray = [];
	$day_all = [];

	foreach($year_revenuesarr as $k=>$v){
        //print_r($v);
        $Model = M('configure_exchange');
        $cc['currency'] = $v['moneytype'];
        $item = $Model->where($cc)->find();
        $year_revenuearray[$v['createday']] = $year_revenuearray[$v['createday']] + $v['revenues']*$item['rating'];

        $day_all[$v['createday']][$v['moneytype']] = $v['revenues'];
	}
	//print_r($year_revenuearray);
	$year_salaryarr  = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('DATE_FORMAT(db_orders.createtime,"%Y-%m") as createday,SUM(db_worker_order.w_payment) as salary')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('DATE_FORMAT(db_orders.createtime,"%Y-%m")')->select();
	$year_profitarray = [];
	//print_r($year_salaryarr);
	foreach($year_salaryarr as $k=>$v){
	  //print_r($v);
	  $Model = M('configure_exchange');
	  $year_profitarray[$v['createday']] = $year_revenuearray[$v['createday']] - $v['salary'];
	}
    /*get orders num*/
    //$year_ordernum = [];
    $year_ordernumarray = [];
    $year_ordernumarr  = $ORDER->field('DATE_FORMAT(db_orders.createtime,"%Y-%m") as createday,Count(*) as ordernum')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('DATE_FORMAT(db_orders.createtime,"%Y-%m")')->select();

    $year_ordernumarray=[];
    foreach($year_ordernumarr as $k=>$v){
        $year_ordernumarray[$v['createday']] = $v["ordernum"];
    }
    //print_r($year_ordernumarray);
	$datas = [];
	$salarysum = 0;
	$revenuesum = 0;
	foreach($year_salaryarr as $k=>$v){
        $room['profit'] = $year_profitarray[$v['createday']];
        $room['salary'] = $v['salary'];
        $salarysum = $salarysum + $v['salary'];
        $room['revenuearray'] = $year_revenuearray[$v['createday']];
        $room['createday'] = $v['createday'];
        $room['datas'] = $day_all[$v['createday']];
        $revenuesum = $revenuesum  + $year_revenuearray[$v['createday']];
        $room['ordernum'] = $year_ordernumarray[$v['createday']];
        array_push($datas ,$room);
	}
	$res["createyear"] = $year;
	$res["salarysum"] = $salarysum;
	$res["revenuesum"]=$revenuesum;
    $res["moneyinfo"] = $year_revenuescc;
	$res["profitsum"]=($revenuesum - $salarysum);
    $res["ordernum"] = $year_ordernum;
    if($daystep == 0){
        $res["profitavg"] = 0;
        $res["ordernumavg"] = 0;
    }else{
        $res["profitavg"] = round($res["profitsum"]/$daystep, 3);
        $res["ordernumavg"] = round($res["ordernum"]/$daystep, 3);
    }

    $res["daystep"] = $daystep;
	$res["datas"] = $datas;
	return $res;
}
/*get days of one month*/
function getMonthData($daydata){
    $res = [];
    $ORDER = M('orders');
    //$month =  date("Y-m");
    $month =  $daydata;
    $fromdate = date('Y-m-d', strtotime($month."-01")); //月初
    $todate = date('Y-m-d', strtotime("$fromdate +1 month -1 day"));//月末
    /*get month length*/
    $nowdate = date("Y-m");
    $monthlength = 1;
    if($nowdate == $daydata){//current month
        $datetime_start = new \DateTime();
        $datetime_end = new \DateTime($fromdate);
        $monthlength = $datetime_start->diff($datetime_end)->days + 1;
    }else{
        $datetime_start = new \DateTime($todate);
        $datetime_end = new \DateTime($fromdate);
        $monthlength = $datetime_start->diff($datetime_end)->days + 1;
    }
    /*  month day revenues*/
    $month_revenuesarr  = $ORDER->field('DATE_FORMAT(db_orders.createtime,"%Y-%m-%d") as createday,db_orders.moneytype,SUM(db_orders.totalprice) as revenues')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('db_orders.moneytype,DATE_FORMAT(db_orders.createtime,"%Y-%m-%d")')->select();
    //print($fromdate);
    //print($todate);
    //print_r($month_revenuesarr);
    $day_revenuearray = [];
    $day_all = [];

    foreach($month_revenuesarr as $k=>$v){
      //print_r($v);
      $Model = M('configure_exchange');
      $cc['currency'] = $v['moneytype'];
      $item = $Model->where($cc)->find();
      $day_revenuearray[$v['createday']] = $day_revenuearray[$v['createday']] + $v['revenues']*$item['rating'];

          $day_all[$v['createday']][$v['moneytype']] = $v['revenues'];
    }
    //print_r($day_revenuearray);
    /*  month day salary */
    $month_salaryarr  = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('DATE_FORMAT(db_orders.createtime,"%Y-%m-%d") as createday,SUM(db_worker_order.w_payment) as salary')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('DATE_FORMAT(db_orders.createtime,"%Y-%m-%d")')->select();
    //print_r($month_salaryarr);
    $day_profitarray = [];

    foreach($month_salaryarr as $k=>$v){
      //print_r($v);
      $Model = M('configure_exchange');
      $day_profitarray[$v['createday']] = $day_revenuearray[$v['createday']] - $v['salary'];
    }
    $day_ordernumarray = [];
    $day_ordernumarr  = $ORDER->field('DATE_FORMAT(db_orders.createtime,"%Y-%m-%d") as createday,Count(*) as ordernum')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('DATE_FORMAT(db_orders.createtime,"%Y-%m-%d")')->select();

    $day_ordernumarray=[];
    foreach($day_ordernumarr as $k=>$v){
        $day_ordernumarray[$v['createday']] = $v["ordernum"];
    }
    //print_r($day_profitarray);
    $datas = [];
    $salarysum = 0;
    $revenuesum = 0;
    foreach($month_salaryarr as $k=>$v){
        $cell['profit'] = $day_profitarray[$v['createday']];
        $cell['salary'] = $v['salary'];
        $salarysum = $salarysum + $v['salary'];
        $cell['revenuearray'] = $day_revenuearray[$v['createday']];
        $cell['createday'] = $v['createday'];
        $cell['datas'] = $day_all[$v['createday']];
        $revenuesum = $day_revenuearray[$v['createday']] + $revenuesum;
        $cell['ordernum'] = $day_ordernumarray[$v['createday']];
        array_push($datas ,$cell);
    }
    $res["createdate"] = $daydata;
    $res["salarysum"] = $salarysum;
    $res["revenuesum"]=$revenuesum;
    $res["profitsum"]=($revenuesum - $salarysum);
    $res["datas"] = $datas;
    $res["profitavg"] = round(($revenuesum - $salarysum)/$monthlength,2);
    return $res;
}

/**************    Graph datas     *******************/
/* get data from day to day*/
function getDayToDayYears($fromdate,$todate){
    $res = [];
    $ORDER = M('orders');
    $fromdate = date('Y-m-d', strtotime($fromdate)); //月初
    $todate = date('Y-m-d', strtotime($todate));//月末

    $yearlength =  intval(date("Y",strtotime($todate))) - intval(date("Y",strtotime($fromdate)));
    //echo $yearlength;

    for ($x=0; $x <= $yearlength; $x++) {
      $fd = date('Y-m-d', strtotime($fromdate ." +".$x." year")); //月初
      $flagyear = date("Y",strtotime($fd));
      if(date('Y', strtotime($todate)) == $flagyear){
        $td = date('Y-m-d', strtotime($todate));//月末
      }else
      {
        $td = date('Y-m-d', strtotime($flagyear."-12-31")); //月初
      }
      //echo $fd." ".$td ."<br>";
      /*  month day revenues*/
      $month_revenuesarr  = $ORDER->field('DATE_FORMAT(db_orders.createtime,"%Y-%m-%d") as createday,db_orders.moneytype,SUM(db_orders.totalprice) as revenues')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('db_orders.moneytype,DATE_FORMAT(db_orders.createtime,"%Y-%m-%d")')->select();
      //print($fromdate);
      //print($todate);
      //print_r($month_revenuesarr);
      $day_revenuearray = [];
      $day_all = [];

      foreach($month_revenuesarr as $k=>$v){
        //print_r($v);
        $Model = M('configure_exchange');
        $cc['currency'] = $v['moneytype'];
        $item = $Model->where($cc)->find();
        $day_revenuearray[$v['createday']] = $day_revenuearray[$v['createday']] + $v['revenues']*$item['rating'];

            $day_all[$v['createday']][$v['moneytype']] = $v['revenues'];
      }
      //print_r($day_revenuearray);
      /*  month day salary */
      $month_salaryarr  = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('DATE_FORMAT(db_orders.createtime,"%Y-%m-%d") as createday,SUM(db_worker_order.w_payment) as salary')->where('db_orders.createtime >=  "'.$fd.' 00:00:00" AND db_orders.createtime <= "'.$td.' 23:59:59"')->group('DATE_FORMAT(db_orders.createtime,"%Y-%m-%d")')->select();
      //print_r($month_salaryarr);
      $day_profitarray = [];

      foreach($month_salaryarr as $k=>$v){
        //print_r($v);
        $Model = M('configure_exchange');
        $day_profitarray[$v['createday']] = $day_revenuearray[$v['createday']] - $v['salary'];
      }
      //print_r($day_profitarray);
      $day_ordernumarray = [];
      $day_ordernumarr  = $ORDER->field('DATE_FORMAT(db_orders.createtime,"%Y-%m-%d") as createday,Count(*) as ordernum')->where('db_orders.createtime >=  "'.$fd.' 00:00:00" AND db_orders.createtime <= "'.$td.' 23:59:59"')->group('DATE_FORMAT(db_orders.createtime,"%Y-%m-%d")')->select();
      $day_ordernumarray=[];
      foreach($day_ordernumarr as $k=>$v){
        $day_ordernumarray[$v['createday']] = $v["ordernum"];
      }
      $datas = [];
      foreach($month_salaryarr as $k=>$v){
        $cell['profit'] = $day_profitarray[$v['createday']];
        $cell['salary'] = $v['salary'];
      //$salarysum = $salarysum + $v['salary'];
        $cell['revenuearray'] = $day_revenuearray[$v['createday']];
        $cell['createday'] = date('m-d', strtotime($v['createday']));
        $cell['fullcreateday'] = date('Y-m-d', strtotime($v['createday']));
        $cell['datas'] = $day_all[$v['createday']];
        $cell['ordernum'] = $day_ordernumarray[$v['createday']];
        if($cell['ordernum'] == 0){
            $cell['profitavg'] = 0;
        }else{
            $cell['profitavg'] = round($cell['profit']/$cell['ordernum'],2);
        }
        //$revenuesum = $day_revenuearray[$v['createday']] + $revenuesum;
        array_push($datas ,$cell);
      }
      $it["values"] = $datas;
      $it["name"] = $flagyear;
      array_push($res ,$it);

    }

    return $res;
}
/* one year */
function getDayToDay($fromdate,$todate){
    $res = [];
    $ORDER = M('orders');
    $fromdate = date('Y-m-d', strtotime($fromdate)); //月初
    $todate = date('Y-m-d', strtotime($todate));//月末
    $flagyear =  date('Y', strtotime($fromdate));
    //echo $yearlength;
    $month_revenuesarr  = $ORDER->field('DATE_FORMAT(db_orders.createtime,"%Y-%m-%d") as createday,db_orders.moneytype,SUM(db_orders.totalprice) as revenues')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('db_orders.moneytype,DATE_FORMAT(db_orders.createtime,"%Y-%m-%d")')->select();
    //print($fromdate);
    //print($todate);
    //print_r($month_revenuesarr);
    $day_revenuearray = [];
    $day_all = [];

    foreach($month_revenuesarr as $k=>$v){
      //print_r($v);
      $Model = M('configure_exchange');
      $cc['currency'] = $v['moneytype'];
      $item = $Model->where($cc)->find();
      $day_revenuearray[$v['createday']] = $day_revenuearray[$v['createday']] + $v['revenues']*$item['rating'];

          $day_all[$v['createday']][$v['moneytype']] = $v['revenues'];
    }
    //print_r($day_revenuearray);
    /*  month day salary */
    $month_salaryarr  = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('DATE_FORMAT(db_orders.createtime,"%Y-%m-%d") as createday,SUM(db_worker_order.w_payment) as salary')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('DATE_FORMAT(db_orders.createtime,"%Y-%m-%d")')->select();
    //print_r($month_salaryarr);
    $day_profitarray = [];

    foreach($month_salaryarr as $k=>$v){
      //print_r($v);
      $Model = M('configure_exchange');
      $day_profitarray[$v['createday']] = $day_revenuearray[$v['createday']] - $v['salary'];
    }

    $day_ordernumarray = [];
    $day_ordernumarr  = $ORDER->field('DATE_FORMAT(db_orders.createtime,"%Y-%m-%d") as createday,Count(*) as ordernum')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('DATE_FORMAT(db_orders.createtime,"%Y-%m-%d")')->select();
    $day_ordernumarray=[];
    foreach($day_ordernumarr as $k=>$v){
        $day_ordernumarray[$v['createday']] = $v["ordernum"];
    }

    //print_r($day_profitarray);
    $datas = [];
    foreach($month_salaryarr as $k=>$v){
      $cell['profit'] = round($day_profitarray[$v['createday']],2);
      $cell['salary'] = $v['salary'];
    //$salarysum = $salarysum + $v['salary'];
      $cell['revenuearray'] = $day_revenuearray[$v['createday']];
      $cell['createday'] = date('m-d', strtotime($v['createday']));
      $cell['fullcreateday'] = date('Y-m-d', strtotime($v['createday']));
      $cell['datas'] = $day_all[$v['createday']];
      $cell['ordernum'] = $day_ordernumarray[$v['createday']];
      //$revenuesum = $day_revenuearray[$v['createday']] + $revenuesum;
      array_push($datas ,$cell);
    }
    $res["values"] = $datas;
    $res["name"] = $flagyear;
    return $res;
}

function getDayData($daydata){
      $ORDER = M('orders');
      //echo $daydata;
      /*
          today show
      */
      $fromdate = $daydata ;
      $todate = $daydata;
      $today_ordernum = $ORDER->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->count();
      $today_salary = 0;
      $today_salary = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->sum('db_worker_order.w_payment');
      //print($today_salary);
      $today_revenuesarr  = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.moneytype,SUM(db_orders.totalprice) as revenues')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('db_orders.moneytype')->select();
      $today_array = [];
      $today_revenues = 0;
      foreach($today_revenuesarr as $k=>$v){
          //print_r($v);
          $Model = M('configure_exchange');
          $cc['currency'] = $v['moneytype'];
          $item = $Model->where($cc)->find();
          $v['rating'] = $item['rating'];
          $today_item[$k] = $v;
          array_push($today_array ,$today_item[$k]);
          $today_revenues = $today_revenues + $v['revenues']*$item['rating'];

      }
      //print($today_revenues);
      $datas['today_day'] = $daydata;
      $datas['today_salary'] = $today_salary;
      $datas['today_revenues'] = $today_revenues;
      $datas['today_alldata'] = $today_revenuesarr;
      $datas['today_ordernum'] = $today_ordernum;
      $datas['today_profit'] = round($today_revenues - $today_salary,2);
      return $datas;
}
function getMonthsData($fy,$ty,$cf){
    /*year show*/

	$ORDER = M('orders');
	$fromdate = date('Y-m-d', strtotime($fy."-01-01")); //start of year
	$todate = date('Y-m-d', strtotime($ty."-12-31"));//end of year

    $datetime_start = new \DateTime();
    $datetime_end = new \DateTime($fromdate);

    $daystep = $datetime_start->diff($datetime_end)->days + 1;
    //var_dump($datetime_start->diff($datetime_end));
    $year_ordernum = $ORDER->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->count();
    $year_revenuescc  = $ORDER->field('db_orders.moneytype,SUM(db_orders.totalprice) as revenues')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('db_orders.moneytype')->select();
	$year_revenuesarr  = $ORDER->field('DATE_FORMAT(db_orders.createtime,"%Y-%m") as createday,db_orders.moneytype,SUM(db_orders.totalprice) as revenues')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('DATE_FORMAT(db_orders.createtime,"%Y-%m"),db_orders.moneytype')->select();
	$year_revenuearray = [];
	$day_all = [];

	foreach($year_revenuesarr as $k=>$v){
	  //print_r($v);
	  $Model = M('configure_exchange');
	  $cc['currency'] = $v['moneytype'];
	  $item = $Model->where($cc)->find();
	  $year_revenuearray[$v['createday']] = $year_revenuearray[$v['createday']] + $v['revenues']*$item['rating'];

	  $day_all[$v['createday']][$v['moneytype']] = $v['revenues'];
	}
	//print_r($year_revenuearray);
	$year_salaryarr  = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('DATE_FORMAT(db_orders.createtime,"%Y-%m") as createday,SUM(db_worker_order.w_payment) as salary')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('DATE_FORMAT(db_orders.createtime,"%Y-%m")')->select();
	$year_profitarray = [];
	//print_r($year_salaryarr);
	foreach($year_salaryarr as $k=>$v){
	  //print_r($v);
	  $Model = M('configure_exchange');
	  $year_profitarray[$v['createday']] = $year_revenuearray[$v['createday']] - $v['salary'];
	}
    $day_ordernumarray = [];
    $day_ordernumarr  = $ORDER->field('DATE_FORMAT(db_orders.createtime,"%Y-%m") as createday,Count(*) as ordernum')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('DATE_FORMAT(db_orders.createtime,"%Y-%m")')->select();
    $day_ordernumarray=[];
    foreach($day_ordernumarr as $k=>$v){
        $day_ordernumarray[$v['createday']] = $v["ordernum"];
    }


	$datas = [];
	$salarysum = 0;
	$revenuesum = 0;
    $datacell = [];
	foreach($year_salaryarr as $k=>$v){
        $room['profit'] = round($year_profitarray[$v['createday']],2);
        $room['salary'] = round($v['salary'],2);
        $room['revenuearray'] = round($year_revenuearray[$v['createday']],2);
        $room['createday'] = $v['createday'];
        //$room['datas'] = $day_all[$v['createday']];
        $mm = date('M', strtotime($v['createday']));
        $room['month'] = date('m', strtotime($v['createday']));
        $room['year'] = date('Y', strtotime($v['createday']));
        $room['ordernum'] = $day_ordernumarray[$v['createday']];
        if($room['ordernum'] == 0){
            $room['profitavg'] = 0;
        }else{
            $room['profitavg'] = round($room['profit']/$room['ordernum'],2);
        }
        $monthlength = 1;
        $nowdate = date("Y-m");
        if($nowdate == $v['createday']){//current month
            $datetime_start = new \DateTime();
            $datetime_end = new \DateTime($v['createday']."-01");
            $monthlength = $datetime_start->diff($datetime_end)->days + 1;
        }else{
            $monthlength = date("t",strtotime($v['createday']));
        }
        //echo $monthlength;
        $room['profitavgperday'] = round($room['profit']/$monthlength,2);
        //$datacell[$room['month']];
        if (key_exists($mm,$datacell))
        {
          array_push($datacell[$mm] ,$room);
        }
        else
        {
          $datacell[$mm] = [];
          array_push($datacell[$mm] ,$room);
        }
	}
  //print_r($datacell);
    /*
    [{
    'month': 'Jan',
    '2018': 2704659,
    '2019': 4499890
    }]
    */
    $dateii = [];
    $dataend = [];
    foreach($datacell as $k=>$v){
      //print_r($k);
      $dataii["month"] = $k;
      foreach($datacell[$k] as $c){
        //print_r($c);
        if($cf == 0){
          $dataii[$c["year"]] = $c["profit"];
        }else if($cf == 1)
        {
          $dataii[$c["year"]] = intval($c["ordernum"]);
        }else if($cf == 2){
          $dataii[$c["year"]] = intval($c["profitavgperday"]);
        }
        //$dataii[$c["year"]] = $c["profit"];

      }
      //echo "######";
      //print_r($dataii);
      array_push($dataend,$dataii);
      unset($dataii);
      //echo "<br>";
    }
    //print_r($dataend);
    //print_r($datacell);
	  $res= $dataend;
    //print_r($res);
    return $res;
}

/*
q1: 1-31 -3-31
q2: 4-1  6-30
q3  7-1  9-30
q4  10-1 12-31
*/
function getQData($fy,$ty,$qname,$cf){
    /*year show*/
  if($qname == "Q1"){
    $start = "-01-01";
    $end = "-03-31";
  }else if($qname == "Q2"){
    $start = "-04-01";
    $end = "-06-30";
  }else if($qname == "Q3"){
    $start = "-07-01";
    $end = "-09-30";
  }else if($qname == "Q4"){
    $start = "-10-01";
    $end = "-12-31";
  }
	$ORDER = M('orders');
    $year_revenuesarr = [];
    $year_revenuearray = [];
    $year_profitarray = [];
    $year_day_all = [];
    $year_salaryarr = [];
    $year_ordernum = [];
    //print_r($year_salaryarr);
    for($i=$fy;$i<=$ty;$i++){
        $fromdate = date('Y-m-d', strtotime(strval($i.$start))); //start of year
        $todate = date('Y-m-d', strtotime(strval($i.$end)));//end of year

        $datetime_start = new \DateTime();
        $datetime_end = new \DateTime($fromdate);
        //var_dump($datetime_start->diff($datetime_end));
        $revenuesarr  = $ORDER->field('CONCAT("Y",DATE_FORMAT(db_orders.createtime,"%Y")) as createday,db_orders.moneytype,SUM(db_orders.totalprice) as revenues')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('DATE_FORMAT(db_orders.createtime,"%Y"),db_orders.moneytype')->select();
        $revenuearray = [];
        $day_all = [];
        $salaryarr = [];
        //print_r($revenuesarr);
        foreach($revenuesarr as $k=>$v){
          //print_r($v);
          $Model = M('configure_exchange');
          $cc['currency'] = $v['moneytype'];
          $item = $Model->where($cc)->find();
          $revenuearray[$v['createday']] = $revenuearray[$v['createday']] + $v['revenues']*$item['rating'];

          $day_all[$v['createday']][$v['moneytype']] = $v['revenues'];
        }
        //print_r($year_revenuearray);

        $salaryarr  = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('CONCAT("Y",DATE_FORMAT(db_orders.createtime,"%Y")) as createday,SUM(db_worker_order.w_payment) as salary')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('DATE_FORMAT(db_orders.createtime,"%Y")')->select();
        $profitarray = [];
        if(empty($salaryarr)){
          $salaryarr = [];
        }
        //print_r($salaryarr);
        foreach($salaryarr as $k=>$v){
          //print_r($v);
          $Model = M('configure_exchange');
          $profitarray[strval($v['createday'])] = $revenuearray[$v['createday']] - $v['salary'];

        }

        $day_ordernumarray = [];
        $day_ordernumarr  = $ORDER->field('CONCAT("Y",DATE_FORMAT(db_orders.createtime,"%Y"))  as createday,Count(*) as ordernum')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('DATE_FORMAT(db_orders.createtime,"%Y")')->select();
        //print_r($day_ordernumarr);
        $day_ordernumarray=[];
        foreach($day_ordernumarr as $k=>$v){
            $day_ordernumarray[strval($v['createday'])] = $v["ordernum"];
        }
        //print_r( $day_ordernumarray);
        $year_salaryarr = array_merge($year_salaryarr,$salaryarr);
        $year_revenuearray = array_merge($year_revenuearray,$revenuearray);
        $year_profitarray = array_merge($year_profitarray,$profitarray);
        $year_day_all = array_merge($year_day_all,$day_all);
        $year_ordernum = array_merge($year_ordernum,$day_ordernumarray);
    }
    //print_r($year_day_all);

	$datas = [];
	$salarysum = 0;
	$revenuesum = 0;
  $datacell = [];
  //print_r($year_salaryarr);
  //print_r($year_revenuearray);
  //print_r($year_revenuearray);
  //print_r($year_salaryarr);
	foreach($year_salaryarr as $k=>$v){
        $room['profit'] = round($year_profitarray[$v['createday']],2);
        $room['salary'] = round($v['salary'],2);
        $room['revenuearray'] = round($year_revenuearray[$v['createday']],2);
        $room['datas'] = $year_day_all[$v['createday']];
        $room['year'] =$v['createday'];
        $room['ordernum'] = $year_ordernum[$v['createday']];
        //array_push($datas ,$room);
        array_push($datas,$room);
	}
    //print_r($datas);
    /*
    [{
    'month': 'Jan',
    '2018': 2704659,
    '2019': 4499890
    }]
    */
    //print_r($datas);
    $dateii = [];
    $dataend = [];
    foreach($datas as $k=>$v){
      //print_r($v);
      if($cf == 0){
          $dataii[$v["year"]] = $v["profit"];
      }else if($cf == 1)
      {
          $dataii[$v["year"]] = intval($v["ordernum"]);
      }

      //echo "######";
      //print_r($dataii);

      //echo "<br>";
    }
    //print_r($dataii);
    $dataii["qname"] = $qname;
    //print_r($dataend);
    //print_r($datacell);
	  $res= $dataii;
    //print_r($res);
    return $res;
}

/* get orders num of every week*/
function getWeekDataOrder($fromdate,$todate){
    $res = [];
    $ORDER = M('orders');
    $fromdate = date('Y-m-d', strtotime($fromdate)); //月初
    $todate = date('Y-m-d', strtotime($todate));//月末
    $flagyear =  date('Y', strtotime($fromdate));
    //echo $yearlength;
    $res = [];
    $res  = $ORDER->field('DATE_FORMAT(db_orders.createtime,"%W") as label,DATE_FORMAT(db_orders.createtime,"%w") as label0, Count(*) as count')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('DATE_FORMAT(db_orders.createtime,"%W")')->select();

	$year_revenuesarr  = $ORDER->field('DATE_FORMAT(db_orders.createtime,"%W") as createday,db_orders.moneytype,SUM(db_orders.totalprice) as revenues')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('DATE_FORMAT(db_orders.createtime,"%W"),db_orders.moneytype')->select();
	$year_revenuearray = [];
	$day_all = [];

	foreach($year_revenuesarr as $k=>$v){
	  //print_r($v);
	  $Model = M('configure_exchange');
	  $cc['currency'] = $v['moneytype'];
	  $item = $Model->where($cc)->find();
	  $year_revenuearray[$v['createday']] = $year_revenuearray[$v['createday']] + $v['revenues']*$item['rating'];

	  $day_all[$v['createday']][$v['moneytype']] = $v['revenues'];
	}
	//print_r($year_revenuearray);
	$year_salaryarr  = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('DATE_FORMAT(db_orders.createtime,"%W") as createday,SUM(db_worker_order.w_payment) as salary')->where('db_orders.createtime >=  "'.$fromdate.' 00:00:00" AND db_orders.createtime <= "'.$todate.' 23:59:59"')->group('DATE_FORMAT(db_orders.createtime,"%Y-%m")')->select();
	$year_profitarray = [];
	//print_r($year_salaryarr);
	foreach($year_salaryarr as $k=>$v){
	  //print_r($v);
	  $Model = M('configure_exchange');
	  $year_profitarray[$v['createday']] = $year_revenuearray[$v['createday']] - $v['salary'];
	}

    $dataset = [];
    foreach($res as $k=>$v){
        //$room = [];
        //echo $v;
        $room["label"] =  $v["label"];
        $room["label0"] =  $v["label0"];
        $room["count"] =  $v["count"];
        $room["profit"] =  $year_profitarray[$v["label"]];
        array_push($dataset,$room);
    }
    //print_r($year_profitarray);
    $names = array_column($dataset,'label0');
    array_multisort($names,SORT_DESC,$dataset);
    //$item["label"] = "Mon";
    //$item["count"] = $res["c"]
    //array_push($rr,);
    //print_r($week_orderinfo);
    //return $res;

    //$res["values"] = $datas;
    //$res["name"] = $flagyear;
    return array_values($dataset);
}
/*get order infomation */
/*

  0. guest have no paid gurrentee
  1. guest have paid gurrentee
  2. guest have pain all money
  -->
  <!--
  0. no
  1. worker is doing
  2. worker has completed and no pay
  3. worker has completed and paid
*/
function getWorkerInfo($wxid){
  $ORDER = M('orders');
  /* complete orders*/
  $ordercomplete = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_workers.wxid = "'.$wxid.'" AND db_worker_order.w_state = 3')->count();
  $orderremark = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_workers.wxid = "'.$wxid.'" AND db_guest_order.g_state = 2 AND db_worker_order.w_state = 3')->avg('db_guest_order.remark');
  $income = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_workers.wxid = "'.$wxid.'" AND db_worker_order.w_state = 3')->sum('db_worker_order.w_payment');
  /* onging  */
  $orderonging = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_workers.wxid = "'.$wxid.'" AND db_worker_order.w_state = 1')->count();
  /* onging  */
  $orderunpaid = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_workers.wxid = "'.$wxid.'" AND db_worker_order.w_state = 2')->count();
  /* order all  */
  $orderall = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_workers.wxid = "'.$wxid.'"')->count();
  return array($ordercomplete, $orderremark, $income,$orderonging,$orderunpaid,$orderall);
}
/*
*
*
$v['ordercomplete'] = $ordercomplete;//$ordercomplete;
$v['orderonging'] = $orderonging;//$orderonging;
$v['orderunpaid'] = $orderunpaid;//$order unpaid;
$v['orderall'] = $orderall;//$orderonging + $ordercomplete;
$v['income'] = $income;
$v['remark'] = round($orderremark,2);
*/
function Recommand($data){

  $datasql = "";
  foreach($data as $v ){
    if($datasql == ""){
      $datasql = '(db_worker_tech.techid = '.$v["email"];//$v["email"];
    }else{
      $datasql = $datasql." OR db_worker_tech.techid = ".$v["email"];
    }
  }
  if($datasql != ""){
    $datasql = $datasql.")";
  }
  //print($datasql);
  /*
  * get dataset from workers in technology
  */
  $workerOld = array();
  $workerNew = array();
  if(!empty($datasql))
  {
    $Model = M('workers');
    $workers = $Model->order('addtime asc')->select();
    //dump($techstr);
    $worklist = [];
    foreach($workers as $k=>$v){
      $MM = M('worker_tech');

      $techeslist = $MM->join('left join db_technologies on db_worker_tech.techid = db_technologies.techid')->field('db_worker_tech.wxid')->where('db_worker_tech.wxid = "'.$v['wxid'].'" and '.$datasql.'')->order('db_technologies.sortid asc')->select();
      //dump($techeslist);
      if($techeslist != NULL){
        array_push($worklist ,$techeslist[0]['wxid']);
      }
    }
    //dump($worklist);
    //$workliststr = rtrim($workliststr,",");
    //dump($workliststr);
    //$max_ordercomplete = 0;
    //$min_ordercomplete = 0;
    $max_ordercomplete = 0;
    $max_orderonging = 0;;//$orderonging;
    $max_orderunpaid = 0;;//$order unpaid;
    $max_orderall = 0;//$orderonging + $ordercomplete;
    $max_income = 0;
    $max_remark = 0;

    $min_ordercomplete= 0;
    $max_orderonging = 0;//$orderonging;
    $min_ordercomplete_orderunpaid = 0;//$order unpaid;
    $min_orderall = 0;//$orderonging + $ordercomplete;
    $min_income = 0;
    $min_remark = 0;
    foreach($workers as $k=>$v){
      $MM = M('worker_tech');
      if(in_array($v['wxid'],$worklist)){
        $teches = $MM->join('left join db_technologies on db_worker_tech.techid = db_technologies.techid')->field('db_worker_tech.techid,db_technologies.content,db_technologies.attr')->where('db_worker_tech.wxid = "'.$v['wxid'].'"')->order('db_technologies.sortid asc')->select();
        $techsss = "";
        $techattrflag = 0;
        $score_techattr = 0;
        foreach($teches as $c){
          $techsss = $techsss.'#'.$c["techid"].'#';
          //attr
          if($c["attr"] >=$techattrflag){
            $score_techattr = $score_techattr + $c["attr"];
          }
        }
        //echo $score_techattr."<br>";
        $v['techarr'] = $teches;
        $v['techarr_attr_score'] = $score_techattr;
        #print_r($teches);
        $ORDER = M('orders');
        /* complete orders*/
        $ordercomplete = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_workers.wxid = "'.$v['wxid'].'" AND db_worker_order.w_state = 3')->count();
        $orderremark = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_workers.wxid = "'.$v['wxid'].'" and db_worker_order.w_state = 3 AND db_guest_order.g_state = 2')->avg('db_guest_order.remark');
        $income = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_workers.wxid = "'.$v['wxid'].'" AND db_worker_order.w_state = 3')->sum('db_worker_order.w_payment');
        /* onging  */
        $orderonging = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_workers.wxid = "'.$v['wxid'].'" AND db_worker_order.w_state = 1')->count();
        /* onging  */
        $orderunpaid = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_workers.wxid = "'.$v['wxid'].'" AND db_worker_order.w_state = 2')->count();
        /* order all  */
        $orderall = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_orders.createtime,db_guests.wxid as gwxid,db_guests.wxname as gwxname,db_orders.projectname,db_guest_order.g_deadline,db_orders.moneytype,db_orders.totalprice,db_orders.guarantee,db_guest_order.g_state,db_workers.wxid,db_workers.wxname,db_worker_order.w_deadline,db_worker_order.w_payment,db_worker_order.w_state,db_guest_order.remark as gremark,db_orders.description')->where('db_workers.wxid = "'.$v['wxid'].'"')->count();

        $v['ordercomplete'] = $ordercomplete;//$ordercomplete;
        /*if($ordercomplete >$max_ordercomplete){
          $max_ordercomplete = $ordercomplete;
        }
        if($min_ordercomplete == 0 || ($ordercomplete < $min_ordercomplete && $ordercomplete > $min_ordercomplete)){
          $min_ordercomplete = $ordercomplete;
        }*/
        $v['orderonging'] = $orderonging;//$orderonging;
        $v['orderunpaid'] = $orderunpaid;//$order unpaid;
        $v['orderall'] = $orderall;//$orderonging + $ordercomplete;
        $v['income'] = $income;


        $v['remark'] = round($orderremark,2);
        /* handle fontend show attrs*/
        $attrarr = array();
        $attrarr = explode(',', $v["attrs"]);
        $attrsimple = "";
        if(count($attrarr)>=1){
            foreach($attrarr as $m){
                if($attrsimple == ""){
                    $attrsimple = $m[0];
                }else{
                    $attrsimple = $attrsimple."|".$m[0];
                }
            }

        }
        $v["attrsimple"] = $attrsimple;

        $workers[$k] = $v;
        $ffc = 0;
        foreach($data as $v ){
          if(strstr($techsss, "#".$v["email"]."#") === false){
            $ffc = 1;
            break;
          }
        }
        if($ffc == 0){
          //print($techsss);
          if($workers[$k]["status"] == 0 && $workers[$k]["ordercomplete"] > 0 ){
            if($ordercomplete >$max_ordercomplete){
              $max_ordercomplete = $ordercomplete;
            }
            if($min_ordercomplete == 0 || ($ordercomplete < $min_ordercomplete && $ordercomplete > 0)){
              $min_ordercomplete = $ordercomplete;
            }
            if($orderonging >$max_orderonging){
              $max_orderonging = $orderonging;
            }
            if($min_orderonging == 0 || ($orderonging < $min_orderonging && $orderonging > 0)){
              $min_orderonging = $orderonging;
            }
            if($orderunpaid >$max_orderunpaid){
              $max_orderunpaid = $orderunpaid;
            }
            if($min_orderunpaid == 0 || ($orderunpaid < $min_orderunpaid && $orderunpaid > 0)){
              $min_orderunpaid = $orderunpaid;
            }
            if($orderall >$max_orderall){
              $max_orderall = $orderall;
            }
            if($min_orderall == 0 || ($orderall < $min_orderall && $orderall > 0)){
              $min_orderall = $orderall;
            }
            if($income >$max_income){
              $max_income = $income;
            }
            if($min_income == 0 || ($income < $min_income && $income > 0)){
              $min_income = $income;
            }
            if($workers[$k]['remark'] >$max_remark){
              $max_remark = $workers[$k]['remark'];
            }
            if($min_remark == 0 || ($workers[$k]['remark'] < $min_remark && $workers[$k]['remark'] > 0)){
              $min_remark = $workers[$k]['remark'];
            }
            //print("<br>");
            //print($workers[$k]['remark']);
            array_push($workerOld,$workers[$k]);
          }
          if($workers[$k]["status"] == 0 && $workers[$k]["ordercomplete"] == 0 ){
            array_push($workerNew,$workers[$k]);
          }

        }

        //array_push($workeroutput ,$workers[$k]);
      }
    }
  }
  //print_r($workerOld);
  /*print("<br>=== len =<br>");
  print(count($workerOld));
  print("<br>=== len =<br>");
  #print("<br>====<br>");
  #print_r($workerNew);
  print($max_ordercomplete);
  print("<br>=max=<br>");
  print($min_ordercomplete);
  print("<br>=min=<br>");
  print($max_orderonging);
  print("<br>====<br>");
  print($min_orderonging);
  print("<br>====<br>");
  print($max_orderunpaid);
  print("<br>====<br>");
  print($min_orderunpaid);
  print("<br>====<br>");
  print($max_orderall);
  print("<br>====<br>");
  print($min_orderall);
  print("<br>====<br>");
  print($max_income);
  print("<br>====<br>");
  print($min_income);
  print("<br>====<br>");
  print($max_remark);
  print("<br>====<br>");
  print($min_remark);
  print("<br>====<br>");
  */
  /*
  * recommand old worker
  * total score  ?
  * ordercomplete  10
  * orderonging >= 3 -10
                >=2  -6
                >=1  -2
                >=0  +10
  * orderunpaid passed
  * orderall  passed
  * remark    15
  * attr remark 5
  * fresh    60*10 / today - recent orders time
  * work type:
  *             stu  +3
  *             v2ex +2
  *             cs   +1
  *             abroad +2
  *             other  +0
  * tech attr 6 :
  *             0  0  common
  *             1  6  rare
  *             2  3  important
  */
  $rate_ordercomplete = 10;
  $rate_remark = 15;
  foreach($workerOld as $k=>$v){
    /* cal ordercomplete */
    $diff_ordercomplete = 1;
    $mark_ordercomplete = 0;
    if($max_ordercomplete == $min_ordercomplete ){
      $diff_ordercomplete = 1;
      $mark_ordercomplete = 1*$rate_ordercomplete;
    }else{
      $diff_ordercomplete = $max_ordercomplete - $min_ordercomplete;
      $mark_ordercomplete = ($v['ordercomplete']-$min_ordercomplete)*$rate_ordercomplete/$diff_ordercomplete;
    }
    $workerOld[$k]["mark_ordercomplete"] = $mark_ordercomplete;
    //print("<br>=== mark complete =<br>");
    /* cal orderonging */
    $mark_orderongoing = 10;
    if($v['orderonging']>=3 ){
      $mark_orderongoing = -10;
    }else if($v['orderonging'] == 2){
      $mark_orderongoing = -6;
    }else if($v['orderonging'] == 1){
      $mark_orderongoing = -2;
    }else if($v['orderonging'] == 0){
      $mark_orderongoing = +5;
    }
    $workerOld[$k]["mark_orderongoing"] = $mark_orderongoing;
    /* cal order remark*/
    $diff_remark = 1;
    $mark_remark = 0;
    if($max_remark == $min_remark ){
      $diff_remark = 1;
      $mark_remark = 1*$rate_remark;
    }else{
      $diff_remark = $max_remark - $min_remark;
      $mark_remark = ($v['remark']-$min_remark)*$rate_remark/$diff_remark;
    }
    $workerOld[$k]["mark_remark"] = $mark_remark;
    /*$attrsche = ["v2ex","student","cs","abroad","other"];*/
    //$attrsche = ["v2ex","student","cs","abroad","other"];
    $attr_mark = 0;
    if(strpos($workerOld[$k]["attrs"], "student") !== false){
        $attr_mark = $attr_mark + 3;
    }
    if(strpos($workerOld[$k]["attrs"], "cs") !== false){
        $attr_mark = $attr_mark + 1;
    }
    if(strpos($workerOld[$k]["attrs"], "v2ex") !== false){
        $attr_mark = $attr_mark + 2;
    }
    if(strpos($workerOld[$k]["attrs"], "abroad") !== false){
        $attr_mark = $attr_mark + 2;
    }
    $workerOld[$k]["mark_attrs"] = $attr_mark;
    /* total */
    $workerOld[$k]["mark_sum"] = $workerOld[$k]["mark_ordercomplete"] + $workerOld[$k]["mark_orderongoing"] +$workerOld[$k]["mark_remark"] + $workerOld[$k]["mark_attrs"]+$workerOld[$k]["techarr_attr_score"]/2;

    $workerOld[$k]["mark_ordercomplete"] = round($workerOld[$k]["mark_ordercomplete"],1);
    $workerOld[$k]["mark_orderongoing"] = round($workerOld[$k]["mark_orderongoing"],1);
    $workerOld[$k]["mark_remark"] = round($workerOld[$k]["mark_remark"],1);
    $workerOld[$k]["mark_sum"] = round($workerOld[$k]["mark_sum"],1);


  }

  $workerOld = arraySequence($workerOld, "mark_sum", $sort = 'SORT_DESC');

  //print_r($workerOld);
  /*
  New worker cal
  */
  foreach($workerNew as $k=>$v){

    /* cal worker type*/
    $attr_mark = 0;
    if(strpos($workerNew[$k]["attrs"], "student") !== false){
        $attr_mark = $attr_mark + 3;
    }
    if(strpos($workerNew[$k]["attrs"], "cs") !== false){
        $attr_mark = $attr_mark + 1;
    }
    if(strpos($workerNew[$k]["attrs"], "v2ex") !== false){
        $attr_mark = $attr_mark + 2;
    }
    if(strpos($workerNew[$k]["attrs"], "abroad") !== false){
        $attr_mark = $attr_mark + 2;
    }
    $workerNew[$k]["mark_attrs"] = $attr_mark;
    /* cal total score*/
    $workerNew[$k]["mark_sum"] = $workerNew[$k]["mark_attrs"]+$workerNew[$k]["techarr_attr_score"]/2;
  }

  $workerNew = arraySequence($workerNew, "mark_sum", $sort = 'SORT_DESC');
  return array($workerOld,$workerNew);


}
/*
* transfer code to timezone
*/
function CodeToTimeZone($code){
    /*
    <option value="BJT" selected>北京</option>
    <option  value="EST">美东</option>
    <option  value="PST">美西</option>
    <option  value="CST">美中</option>
    <option  value="MST">美山地</option>
    <option  value="ENT">英国</option>
    <option  value="AEST">澳东</option>
    <option  value="ACST">澳中</option>
    <option  value="AWST">澳西</option>
    <option  value="NZLT">新西兰</option>
    <option  value="GET">德国</option>
    */
    switch ($code)
    {
    case "BJT":
      $tz = "Asia/Shanghai";
      break;
    case "EST":
      $tz = 'America/New_York';
      break;
    case "PST":
      $tz = 'America/Los_Angeles';
      break;
    case "CST":
      $tz = 'America/Chicago';
      break;
    case "MST":
      $tz = 'America/Phoenix';
      break;
    case "ENT":
      $tz = 'Europe/London';
      break;
    case "AEST":
      $tz = 'Australia/Canberra';
      break;
    case "ACST":
      $tz = 'Australia/Adelaide';
      break;
    case "AWST":
      $tz = 'Australia/Perth';
      break;
    case "NZLT":
      $tz = 'Pacific/Auckland';
      break;
    case "GET":
      $tz = 'Europe/Berlin';
      break;
    default:
      $tz = "Asia/Shanghai";
    }
    return $tz;
}
/*
**###################################################################################
********************************** workers data api *********************************
**###################################################################################
*/
function getAllWorkerData(){
	$data = [];
	$Model = M('workers');
	$workers = $Model->order('addtime asc')->select();
	$data["worker_total"] = $Model->order('addtime asc')->count();
	$ORDER = M('orders');
	//$ordercomplete = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_orders.orderid,db_workers.wxid,db_worker_order.w_state')->where('db_workers.wxid = "'.$wxid.'" AND db_worker_order.w_state = 3')->count();
	$worker_doing = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_workers.wxid,count(*) as num')->where('db_worker_order.w_state = 1')->group("db_workers.wxid")->select();
	$data["worker_doing"]  = count($worker_doing);
	$worker_unpaid = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_workers.wxid,count(*) as num')->where('db_worker_order.w_state = 2')->group("db_workers.wxid")->select();
	$data["worker_unpaid"]  = count($worker_unpaid);
	$worker_old = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_workers.wxid,count(*) as num')->group("db_workers.wxid")->select();
	//print_r($worker_old);
	$data["worker_oldnum"]  = count($worker_old);
	$data["worker_free"]  = $data["worker_total"] - $data["worker_doing"];
	$data["worker_salary"] = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->sum('db_worker_order.w_payment');
	$data["worker_unpaidsalary"] = $ORDER->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->where('db_worker_order.w_state = 2')->sum('db_worker_order.w_payment');
	return $data;
	//echo count($worker_old);

	//dump($workers);
/*
	foreach($workers as $k=>$v){
		$MM = M('worker_tech');
		$map['wxid'] = $v['wxid'];
		$teches = $MM->join('left join db_technologies on db_worker_tech.techid = db_technologies.techid')->field('db_worker_tech.techid,db_technologies.content')->where('db_worker_tech.wxid = "'.$v['wxid'].'"')->select();
		$v['techarr'] = $teches;
		$ORDER = M('orders');
		$workitem = getWorkerInfo($v['wxid']);

		$v['ordercomplete'] = $workitem[0];//$ordercomplete;
		$v['orderonging'] = $workitem[3] ;//$orderonging;
		$v['orderunpaid'] = $workitem[4] ;//$order unpaid;
		$v['orderall'] = $workitem[5];//$orderonging + $ordercomplete;
		$v['income'] = $workitem[2];
		$v['remark'] = round($workitem[1],2);

		$workers[$k] = $v;
		//dump($workers[$k]);
		//echo $k;
		switch($type){
			case 1:
			  if($v['income'] == 0.00 && $workers[$k]["status"] == 0){
				array_push($workersres,$workers[$k]);
			  }
			  break;
			case 2:
			  if($v['income'] > 0.00 && $workers[$k]["status"] == 0){
				array_push($workersres,$workers[$k]);
			  }
			  break;
			case 3:
			  if($workers[$k]["status"] == 1){
				array_push($workersres,$workers[$k]);
			  }
			  break;
			case 4:
			  if($workers[$k]["orderonging"] == 0 && $workers[$k]["status"] == 0){
				array_push($workersres,$workers[$k]);
			  }
			  break;
			case 5:
			  if($workers[$k]["orderonging"] > 0 && $workers[$k]["status"] == 0){
				array_push($workersres,$workers[$k]);
			  }
			  break;
			default:
			  if($workers[$k]["status"] == 0){
				array_push($workersres,$workers[$k]);
			  }
		}

	}
*/
}
/* one tech -> workers statistics */
function getTechWorkerDatas(){
    $techlist = M('technologies')->field("techid,content as techname")->order('sortid asc')->select();
    $worker_doing_wxids = M('orders')->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_workers.wxid')->where('db_worker_order.w_state = 1')->group("db_workers.wxid")->select();
    $worker_unpaid_wxids = M('orders')->join('left join db_worker_order on db_worker_order.orderid = db_orders.orderid')->join('left join db_workers on db_worker_order.wxid = db_workers.wxid')->join('left join db_guest_order on db_guest_order.orderid = db_orders.orderid')->join('left join db_guests on db_guest_order.wxid = db_guests.wxid')->field('db_workers.wxid')->where('db_worker_order.w_state = 2')->group("db_workers.wxid")->select();
    //$worker_doing_wxidsstr = implode(",",$worker_doing_wxids);
    //$worker_unpaid_wxidsstr = implode(",",$worker_unpaid_wxids);
    $worker_doing_wxidsstr = "";
    $worker_unpaid_wxidsstr = "";
    foreach($worker_doing_wxids as $k => $v){
        if($worker_doing_wxidsstr == ""){
            $worker_doing_wxidsstr = '"'.$v["wxid"].'"';
        }else{
             $worker_doing_wxidsstr = $worker_doing_wxidsstr.',"'.$v["wxid"].'"';
        }
    }
    foreach($worker_unpaid_wxids as $k => $v){
        if($worker_unpaid_wxidsstr == ""){
            $worker_unpaid_wxidsstr = '"'.$v["wxid"].'"';
        }else{
             $worker_unpaid_wxidsstr = $worker_unpaid_wxidsstr.',"'.$v["wxid"].'"';
        }
    }
    //print_r($worker_doing_wxids);
    //echo $worker_doing_wxidsstr;
    //echo "<br>";
    //echo $worker_unpaid_wxidsstr;
    $Tech= M('worker_tech');
    $dataset = [];
    foreach($techlist as $k => $v){
        $item = [];
        //echo $v["techid"];
        //echo $v["techname"];
        //echo "#########";
        //$techeslist = $Tech->join('left join db_technologies on db_worker_tech.techid = db_technologies.techid')->where('db_worker_tech.wxid in ("'.$worker_doing_wxidsstr.'") and db_technologies.techid = "'.$v["techid"].'"')->select();
        $doing = $Tech->join('left join db_technologies on db_worker_tech.techid = db_technologies.techid')->where('db_worker_tech.wxid in ('.$worker_doing_wxidsstr.') and db_technologies.techid = "'.$v["techid"].'"')->count();
        $unpaid = $Tech->join('left join db_technologies on db_worker_tech.techid = db_technologies.techid')->where('db_worker_tech.wxid in ('.$worker_unpaid_wxidsstr.') and db_technologies.techid = "'.$v["techid"].'"')->count();
        $total = $Tech->join('left join db_technologies on db_worker_tech.techid = db_technologies.techid')->where('db_technologies.techid = "'.$v["techid"].'"')->count();
        $item["name"] = $v["techname"];
        $item["total"] =  intval($total);
        $item["doing"] =  intval($doing);
        $item["unpaid"] =  intval($unpaid);
        $item["free"] =  $total - $doing - $unpaid;
        $item["formular"] = $item["free"]."/".$total;

        if($total == 0){
          $item["rate"] = 0;
        }else{
          $item["rate"] = round($item["free"]/$total,2);
        }
        array_push($dataset,$item);
        //echo "<br>";
    }
    //print_r($dataset);
    return $dataset;
	//$Tech= M('worker_tech');
	//$techeslist = $Tech->join('left join db_technologies on db_worker_tech.techid = db_technologies.techid')->select();
	//print_r($techeslist);
}
