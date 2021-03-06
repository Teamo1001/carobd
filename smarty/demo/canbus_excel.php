<?php


/**
 * export xls
 * */
require ('../libs/Smarty.class.php');
include dirname(dirname(dirname(__FILE__))).'/service/tj_canbusAPI.php';


$startDate = isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : null;
$stopDate = isset($_REQUEST['stopDate']) ? $_REQUEST['stopDate'] : null;
$devices=isset($_REQUEST['devices']) ? $_REQUEST['devices'] : null;
//echo "abcde";

$obj = new tj_canbus();
//if ($obj ==nothing) echo "nothing";
$objArray = $obj->getTjCanbus($startDate,$stopDate,$devices,true);
//$objArray = array();
//$objArray[0]= array();
//$objArray[0]["a"]="abc";
//$objArray[0]["b"]="xyz";
//$objArray[1]= array();
//$objArray[1]["a"]="1abc";
//$objArray[1]["b"]="2xyz";


//print_r($objArray);

$numberArray = array();
$numRows = count($objArray);
for ($i = 0; $i < $numRows; $i++) {
	array_push($numberArray, ($i +1));
}
 
$list = array (
	"序号",
	"终端号",
	"车牌号",
	"客户名称",
	"车型",
	"时间",
	"地址",
	"转速",
	"冷却液温度",
	"故障码个数",
	"车速",
	"剩余油量",
	"电瓶电压",
);	//	"进气温度",

//$list = array (
//	"序号",
//	"DeviceID",
//	"终端号",
//);

$starDay=substr($startDate,0,10);
$stopDay=substr($stopDate,0,10);

$title = "CANBUS报表（".$starDay."~".$stopDay."）";
$filename = iconv("utf-8", "gb2312", "CANBUS报表（".$starDay."~".$stopDay."）");
$smarty = new Smarty;
$smarty->assign("title", $title);
$smarty->assign("list", $list);
$smarty->assign("objArray", $objArray);
$smarty->assign("numberArray", $numberArray);
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:filename=$filename.xls");
$smarty->display("canbus.tpl");
?>
