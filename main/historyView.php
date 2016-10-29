<?php include("session.php"); ?>
<?php 

//	url: http://127.0.0.1:8080/obd_web/main/historyView.php?deviceId=6C4C76C1&licenseNumber=GL8(CBD)
//	url: http://192.168.0.163/obd_web/main/historyView.php?deviceId=6C4C76C1&licenseNumber=GL8(CBD)

	$deviceId = $_GET["deviceId"];
	$licenseNumber = $_GET["licenseNumber"];
	
	//echo "licenseNumber:" . $licenseNumber . "<BR>"; 	
	
	function url4RequestTrips($deviceId, $condition){
		switch ($condition){
			case "last3hr":
						$startTime =  date('Y-m-d H:i:s',time() - 60*60*3);
						$stopTime = date('Y-m-d H:i:s');
						break;
			case "last6hr":
						$startTime =  date('Y-m-d H:i:s',time() - 60*60*6);
						$stopTime = date('Y-m-d H:i:s');
						break;			
			case "last12hr":
						$startTime =  date('Y-m-d H:i:s',time() - 60*60*12);
						$stopTime = date('Y-m-d H:i:s');
						break;			
			case "today":
			default:	
						$startTime = date('Y-m-d') . "%2000:00:00";
						$stopTime = date('Y-m-d') . "%2023:59:59";
		}
		return 	"./tripListView.php?deviceId=" . $deviceId . "&startTime=" . $startTime . "&stopTime=" . $stopTime;	
	}	
						 			
?>
<!DOCTYPE html> 
<html>   
<head>   
<meta name="viewport" content="initial-scale=1.0, user-scalable=yes" />   
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />   
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="../themes/default/easyui.css">
	<link rel="stylesheet" type="text/css" href="../themes/icon.css">
	<link rel="stylesheet" type="text/css" href="../demo.css">
	<script type="text/javascript" src="../jquery.min.js"></script>
	<script type="text/javascript" src="../jquery.easyui.min.js"></script>
	<script type="text/javascript" src="../locale/easyui-lang-zh_CN.js"></script> 
    <SCRIPT type="text/javascript" src="../jquery.layout.js"></SCRIPT>
    <SCRIPT type="text/javascript" src="../jquery.layout.extend.js"></SCRIPT>
	<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=oCbw1Qz8ayXfZKlgDHKyfsWG"></script>  
	<script type="text/javascript" src="http://developer.baidu.com/map/jsdemo/demo/convertor.js"></script>  
<!--	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAKKcnYLgJKhO6-yOav7Qzn-1EFBn814Lg&sensor=FALSE"></script>
--> 
    <script type="text/javascript" src="../timeUtils.js"></script>
    <script type="text/javascript" src="../js/history.js"></script>

<title>历史行程</title>   
<style type="text/css">   
html{height:100%}   
body{height:100%;margin:0px;padding:0px}  
#container{height:100%;width:100%}  


.window-shadow,
.panel.window{background: rgba(255,255,255,.3) !important;}

.panel.window div{background: transparent !important;}

.conterid-height{height:100%;}
#open_data:hover,#close_data:hover{color: rgb(0,87,0);background-color:rgba(255,0,0,.2); border-color:rgb(149,184,231);}
</style>   
	<script>
		function showAlert( obj){
			alert (obj);
		}
		
		function isValidGps(lat,lng){
			return ((lat>+1e-5 && lat<=90.0)|| (lat<-1e-5 && lat >=-90.0)) && ((lng>+1e-5 && lng<=180.0)|| (lng<-1e-5 && lng >=-180.0));
		}
	</script>  
	
</head>   

<body class="easyui-layout"  id="cc" > 


<div id="open_data" style="width:80px;height:20px;border:2px solid rgba(149,184,231,.8);z-index:1111;position:absolute;
            right:10px;bottom:125px;line-height: 20px;padding-left: 10px;border-radius: 10px;cursor:pointer;display:none;">
    <strong>打开数据窗口</strong>
</div>
  
<!-- 数据窗口 -->
<div id="load_data" data-options="region:'south',split:false" style="height:150px;width:100%">
	<div id="close_data" title="关闭数据窗口" style="width:20px;height:20px;border:2px solid rgba(149,184,231,.8);z-index:1111;position:absolute;
    			right:10px;top:3px;line-height: 21px;padding-left: 10px;border-radius: 10px;cursor:pointer;">
    	<strong>X</strong>
    </div>
	<table id="dg" class="easyui-datagrid"  style="height:148px;">
					<thead>
						<tr>
						
			                <th data-options="field:'gpsStamp',width:150">时间</th>
<!--							<th data-options="field:'gpsTime',width:200">时间</th> -->
							<th data-options="field:'longitude',width:100">经度</th>
							<th data-options="field:'latitude',width:100">纬度</th>
							<th data-options="field:'address_num',width:300,align:'right'">地址</th>
							<th data-options="field:'heading',width:60">方向</th>
							<th data-options="field:'code',width:100">事件类型</th>
						
						</tr>
					</thead>
				</table>	   
</div>


<script>
	$(function(){
		$("#close_data").click(function(){
			$("#load_data").hide(250);
			$("#open_data").show(250);
			$(".layout-panel-center").addClass("conterid-height");
			$("#conterid").addClass("conterid-height");
		});	
		
		$("#open_data").click(function(){
			$("#load_data").show(250);
			$("#open_data").hide(250);
			$(".layout-panel-center").removeClass("conterid-height");
			$("#conterid").removeClass("conterid-height");
		});	
	});
</script>


<div data-options="region:'center',iconCls:'icon-ok'" id="conterid">

  
<div style="margin:20px 0 10px 0; position:absolute; z-index:10;  visibility:hidden">
		<a id="Test1" href="#" class="easyui-linkbutton" onclick="JAVASCRIPT:onTest1();">Test1</a>  
		<a id="Test2" href="#" class="easyui-linkbutton" onclick="javascript:">Test2</a>   
</div>

<!-- 历史行程列表 -->
<div id="histWin" class="easyui-window" title="历史行程" data-options="minimizable:false,maximizable:false,resizable:false,closable:false,tools:[{
			iconCls:'pagination-load',
			handler:function(){
                var refresh_tab = $('#tabRecentTrips').tabs('getSelected'); 
                if(refresh_tab && refresh_tab.find('iframe').length > 0){  
                    var _refresh_ifram = refresh_tab.find('iframe')[0]; 
                    var refresh_url = _refresh_ifram.src;
                    _refresh_ifram.contentWindow.location.href=refresh_url;  
                }
			}
		}]" 
style="width:450px;height:600px;top:45px;left:80px; padding:0px;z-index:3 ;" >
	
	<div id="tabRecentTrips" class="easyui-tabs" style="margin-top:0px;width:436px;height:547px;padding:0px">
   			<!-- <div style="border:1px solid red;width:20px;height:20px;position:relative;"></div>-->
		<div title="本月" style="padding:10px">
			<div style="height: 0;">
				<table><tr><td>					
				<!--	<input id="searchDate" class="easyui-datetimebox" required style="width:200px"/>  	-->	
				</td><td>
				<!--	<input type="button"  style="width:150px" value="按日期查询" onclick="JAVASCRIPT:alert("ok");searchByDate();"/> -->
				</td></tr>
				</table>
			</div>	
			<div id="div_triplist1" style = "height:496px">
				<iframe src="" id="triplist1" name="triplist1" frameBorder=0 scrolling=yes width="100%" height="100%" ></iframe>
			</div>	
		</div>
		<div title="本周" style="padding:10px">
			<div style="height: 0;">
				<table><tr><td>					
				<!--	<input class="easyui-datetimebox" required style="width:200px"/>  	  -->	
				</td><td>
				<!--	<input type=”button"  style="width:150px" value="按日期查询" />  -->
				</td></tr>
				</table>
			</div>	
			<div id="div_triplist2" style = "height:496px">
				<iframe src="" id="triplist2" name="triplist2" frameBorder=0 scrolling=yes width="100%" height="100%" ></iframe>
			</div>	
		</div>
		<div title="当天" style="padding:10px">
			<div style="height: 0;">
				<table><tr><td>					
				<!--	<input class="easyui-datetimebox" required style="width:200px"/>  	 -->	
				</td><td>
				<!--	<input type="button"  style="width:150px" value="按日期查询" />  -->
				</td></tr>
				</table>
			</div>	
			<div id="div_triplist3" style = "height:496px">
				<iframe id="tripOfTodayFrm" src="" name="triplist3" frameBorder=0 scrolling=yes width="100%" height="100%" ></iframe>
			</div>	
		</div>
		<div title="最近10笔(倒序)" style="padding:10px" data-options="selected:true">
			<div style="height: 0">
				<table><tr><td>					
					<!-- <input class="easyui-datetimebox" required style="width:200px"/>  		 -->
				</td><td>
				<!--	<input type="button"  style="width:150px" value="按日期查询" />  -->
				</td></tr>
				</table>
			</div>	
			<div id="div_triplist4" style = "height:496px">
				<iframe  id="triplist4" name="triplist4" 
                	frameBorder=0 src="<?php 		
						echo "./tripListView.php?deviceId=" . $deviceId . "&optype=lastTen";
					?>"  scrolling=yes width="100%" height="100%" ></iframe>
			</div>	
		</div>
        
        
        
		<div title="自定义时间段查询" style="padding:10px">
			
			<div id="div_triplist5" style = "height:496px">
                <strong style="margin-left:10px;">开始时间:</strong>
                <input class="easyui-datetimebox"  style="width:125px;"
                 data-options="formatter:myformatter2,parser:myparser2,required:true,showSeconds:false"
                    id='h_startDate'></input>
                    
                <strong style="margin-left:10px;">结束时间:</strong>
                <input class="easyui-datetimebox" style="width:125px;"
                 data-options="formatter:myformatter2,parser:myparser2,required:true,showSeconds:false"
                    id='h_stopDate'></input>
                
                <br>
                  
            	<strong style="color:red;font-family:'微软雅黑';font-size:14px;
                		margin-top:6px;background:#000;border-radius:4px;margin-left:10px;
                        padding:4px;display:inline-block;font-weight:bolder;">
                	(注意:最大跨度不能超过 31 天!)
                </strong>
                <a href='#'
                    class='easyui-linkbutton' 
                    data-options='iconCls:"icon-search"'
                    style='width: 100px;float:right;margin: 6px 26px;' 
                    onclick='javascript:searchBySelf()'>查询</a>
                <br>
                
				<iframe  id="triplist5" name="triplist5" 
                	frameBorder=0 src="" scrolling=yes width="100%" height="88%" ></iframe>
			</div>	
		</div>	
        
        
        	
	</div>
	<!--	tab带下拉菜单-->
	<div id="tabByHours">
		<div>最近3小时</div>
		<div>最近6小时</div>
		<div>最近12小时</div>
		<div>当天</div>
	</div>
    
    <script type="text/javascript">
		//datebox的格式 化
        function myformatter(date){
            var y = date.getFullYear();
            var m = date.getMonth()+1;
            var d = date.getDate();
            return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
        }

		function myparser(s){
            if (!s) return new Date();
            var ss = (s.split('-'));
            var y = parseInt(ss[0],10);
            var m = parseInt(ss[1],10);
            var d = parseInt(ss[2],10);
            if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
                return new Date(y,m-1,d);
            } else {
                return new Date();
            }
        }
		
		//datetimebox的格式化
		function myformatter2(date){
            var y = date.getFullYear();  
            var m = date.getMonth()+1;  
            var d = date.getDate();  
            var h = date.getHours();  
            var min = date.getMinutes();  
            var sec = date.getSeconds();  
            var str = y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d)+' '+(h<10?('0'+h):h)+':'+(min<10?('0'+min):min);  
            return str;
        }
		
		function myparser2(sdate){
            if (!sdate) return new Date();
			
			var s = sdate.split(" ");
						
            var ss = (s[0].split('-'));
            var y = parseInt(ss[0],10);
            var m = parseInt(ss[1],10);
            var d = parseInt(ss[2],10);
			
			var sss = (s[1].split(':'));
			var hour = parseInt(sss[0],10);
			var minute = parseInt(sss[1],10);
			
			
            if (!isNaN(y) && !isNaN(m) && !isNaN(d) && !isNaN(hour) && !isNaN(minute)){
                return new Date(y,(m-1),d,hour,minute,0);//"2013/10/01,18:25:00"				
            } else {
                return new Date();
            }
        }
		
		
		$(document).ready(function(){
			setMonthlyPostValue();//为月报表的年份进行设置
			setDateFirst();//为各个控件设置初始值 以当前日期为基准设置
		});
		
		function setMonthlyPostValue(){
			var date = new Date();
			var y = date.getFullYear();//但到今年	
			
			var str = [];
			for(var i=2;i>=0;i--){
				str.push({"text": y-i, "value": y-i});				
			}
			$("#yearSelect").combobox('loadData', str);
		}
			
		//给各日期搜索框增加默认值
		function setDateFirst(){
			var date = new Date();
			var y = date.getFullYear();//年
			var m = date.getMonth()+1;//月
			var d = date.getDate();//日
			
			var hour = date.getHours();//小时
			var minute = date.getMinutes(); //分
			
			var thisMonthFirst = y+"-"+(m<10?('0'+m):m)+"-01";//当前月的第一天
			var thisMonthToday = y+"-"+(m<10?('0'+m):m)+"-"+(d<10?('0'+d):d);//当前月的今天
			
			var thisMonthFirst_hour = y+"-"+(m<10?('0'+m):m)+"-01 00:00";//当前月第一天,精确到分钟
			var thisMonthToday_hour = y+"-"+(m<10?('0'+m):m)+"-"+(d<10?('0'+d):d)+" "+(hour<10?('0'+hour):hour)+":"+(minute<10?('0'+minute):minute);//当前月的今天,精确到分钟
			
			$("#h_startDate").datetimebox ("setValue",thisMonthFirst_hour);
			$("#h_stopDate").datetimebox ("setValue",thisMonthToday_hour);

		}
		
		function searchBySelf(){
			
			var startDate=$('#h_startDate').datetimebox('getValue');
			
			var stopDate=$('#h_stopDate').datetimebox('getValue');

			if(startDate == null || startDate ==''){
				alert("请输入开始时间！");
				return ;
			}
			if(stopDate == null || stopDate ==''){
				alert("请输入结束时间！");
				return ;
			}
			if(stopDate<startDate){
				alert("结束时间必须晚于开始时间！");
				return ;
			}		
			
			strStartDate = startDate.replace(/-/g,"/");
			var date1 = new Date(strStartDate);
			
			strStopDate = stopDate.replace(/-/g,"/");
			var date2 = new Date(strStopDate);
			
		   var date3=date2.getTime()-date1.getTime();
		   //计算出相差天数
			var days=Math.ceil(date3/(24*3600*1000));
		  // alert(days);
		   if(days>31){
			   alert("时间段最多为31天！");
			   return ;
		   }
		   //开始查询按照所输入的日期开始查询		   
		   var tripSelectSrc = "./tripListView.php?deviceId=<?php echo $deviceId; ?>"+"&startTime="+startDate+"&stopTime="+stopDate;
			//console.log(tripSelectSrc);
		   $("#triplist5").attr("src",tripSelectSrc);
	
		}	
		
    </script>
    
    
    
	<script>
		$(function(){	//tab  上面嵌入菜单

			$('#tabRecentTrips').tabs({
				onSelect: function(title,index){					
					switch(index){
						case 0://本月
							var monthSrc = '<?php 
									//本月：1号到今天半夜
									echo "./tripListView.php?deviceId=";
									echo $deviceId; 
									echo "&";
									echo "startTime=" . date("Y"). "-" . date("m") . "-01" ;
									// starttime: 本月1日
									echo "&stopTime=";
									// stoptime: now
									echo date("Y-m-d") . "%2023:59:59";				
							
								?>';
							if($("#triplist1").attr("src") == ""){
								$("#triplist1").attr("src",monthSrc);
							}
							break;
						case 1://本周
							var weekSrc = '<?php 
									//本周：周一到今天半夜
									$dayNumInWeek = (idate("w")>0?idate("w"):7)-1;	//周一为 一周第一天
									$Monday = date('Y-m-d',time() - $dayNumInWeek*24*60*60) . "%2000:00:00";
									$stopTime = date('Y-m-d') . "%2023:59:59";
									echo "./tripListView.php?deviceId=" . 
										$deviceId . "&startTime=" . 
										$Monday . "&stopTime=" . 
										$stopTime;				
			
								?>';
							if($("#triplist2").attr("src") == ""){
								$("#triplist2").attr("src",weekSrc);
							}
							break;
						case 2://当天
							var todaySrc = '<?php 						
								//今天：凌晨到半夜
								$startTime = date('Y-m-d') . "%2000:00:00";
								$stopTime = date('Y-m-d') . "%2023:59:59";
								echo "./tripListView.php?deviceId=" . 
									$deviceId . "&startTime=" . 
									$startTime . "&stopTime=" . 
									$stopTime;				
								?>';
							if($("#tripOfTodayFrm").attr("src") == ""){
								$("#tripOfTodayFrm").attr("src",todaySrc);
							}
							break;
						default:
							break;	
					}
						
				  }
			});
			
				
			var p = $('#tabRecentTrips').tabs().tabs('tabs')[2];
			var mb = p.panel('options').tab.find('a.tabs-inner');
			mb.menubutton({
				menu:'#tabByHours'
			}).click(function(){
				$('#tabRecentTrips').tabs('select',2);
			});
			$('#tabByHours').menu({    
			    onClick:function(item){    
			    	//alert (item.text);
			    	switch (item.text){
			    	case '最近3小时':	$('#tripOfTodayFrm').attr("src","<?php echo url4RequestTrips($deviceId,"last3hr");?>");	//./tripListView.php?deviceId=6C500CBD&startTime=2014-07-02%2013:30:30&stopTime=2014-07-03%2016:30:30");
			    						break;
			    	case '最近6小时':	$('#tripOfTodayFrm').attr("src","<?php echo url4RequestTrips($deviceId,"last6hr");?>");	//./tripListView.php?deviceId=6C500CBD&startTime=2014-07-02%2010:30:30&stopTime=2014-07-03%2016:30:30");	
			    						break;
			    	case '最近12小时':	$('#tripOfTodayFrm').attr("src","<?php echo url4RequestTrips($deviceId,"last12hr");?>");	//./tripListView.php?deviceId=6C500CBD&startTime=2014-07-02%2004:30:30&stopTime=2014-07-03%2016:30:30");
			    						break;
			    	case '当天':			
			    	default:			//今天：凌晨到半夜
			    						$('#tripOfTodayFrm').attr("src","<?php echo url4RequestTrips($deviceId,"today");?>");
			    						break;
			    	};
			    	$('#tabRecentTrips').tabs("select",2);				    	
			 }}); 
			 
			// $('#tabRecentTrips').tabs("select",3);
			 
		});
		
		function searchByDate()	{
			var selectedDate = $("#searchDate").value();
			alert (selectedDate);
			$("#triplist1").attr("src","./tripListView.php?deviceId=6C500CBD&date="+selectedDate+"&mapType=1");
		}
	</script>

</div>

<!-- 行程播放控制面板 -->
<script>
		//alert("002");
</script>    
<div id = "playbackWin" style="position:absolute;z-index:2 ;">
	<table>
	<tbody>
	<tr ><td valign ="top">
<!--	<div id="playbackWin1" class="easyui-panel" style="width:25px;height:120px;padding:0px; " onclick="javascript:playPanelOnOff()"> 
			<br>关<br>闭<br>
		</div> -->
		<div id="playbackWin1" class="easyui-panel" style="width:25px;height:120px;padding:0px" onclick="javascript:playPanelOnOff()"> 
			<div id="playbackWin1_1" style="width:100%; height:100%;background:url('../mapPic/history/btn_pbp_close.png'); background-repeat:no-repeat" >
			</div>
		</div>


	</td>
	<td>
		<div id="playbackWin2" class="easyui-panel" style="height:230px; width:250px;padding:5px; background:rgba(240,240,240,.6); border:2px solid rgba(0,0,0,.5);border-radius:5px;">
			<table border="0" cellspacing="0" cellpadding="1"><tbody>
				<tr><td>
					<table>
						<tbody><tr>
				    		<td width="70px">
				           		<select id ="searchType" class="easyui-combobox" name="state" style="width:80px;">
									<option value="VehNum">车牌号</option>
									<option value="ESN">ESN</option>	
								</select>
							</td><td width="70px">
								<input id="licenseNumber" class="easyui-validatebox textbox" style="width:100%" data-options="required:true,validType:'length[3,10]'" value='<?php echo urldecode($licenseNumber); ?>'>							    
							</td>
							<td><a href="#" class="easyui-linkbutton"     onclick="doSearch()">查询</a></td>
						</tr></tbody>
					</table>             
				</td></tr>
				<tr><td>  		                             
					<table border="0" cellspacing="0" cellpadding="1"><tbody>
						<tr>
	                           <td width="75"><span>时间范围</span></td>
	                           <td width="" align="">
	                           		<select id ="recent" class="easyui-combobox" name="state" style="">
										<option value="last3hr">最近3小时</option>
										<option value="last6hr">最近6小时</option>
										<option value="last12hr">最近12小时</option>
										<option value="today">最近24小时</option>					
									</select>
								</td>
	                    </tr>
	                    <tr>
	                           <td><span>开始时间</span></td>
	                           <td align="right"><input id ="beginTimeV" class="easyui-datetimebox"  style=""/></td>
	                    </tr>
	                    <tr>
	                           <td><span>结束时间</span></td>
	                           <td align="right"><input id ="endTimeV" class="easyui-datetimebox"  style=""/></td>
	            		</tr></tbody>
	           		 </table>
            </td></tr>
            <tr><td>
				<table border="0" cellspacing="0" cellpadding="1" width="100%" align="center"><tbody>
						<tr >
						<td align="center"><a id="playbackHistory" href="#" class="easyui-linkbutton" style="width:60px" onclick="javascript:playbackHistory();">播放</a></td>
						<td align="center"><a href="stopPlayback" class="easyui-linkbutton" style="width:60px" onclick="javascript:stopPlayback()">停止</a></td>
						<td align="center"><a href="" class="easyui-linkbutton" style="width:60px; visibility:hidden" >打开选项</a></td>
						</tr>     
				</tbody>
				</table>       
            </td></tr>
        <tr><td align="center">
				<div id="optionPanel" class="easyui-panel" style="width:210px;background: transparent;padding:4px;">	
					<table border="0" cellspacing="0" cellpadding="0"><tbody>
						<tr>
	                           <td width="80"><span>播放速率</span></td>
	                           <td width="" align="" colspan = 2>
                               
                               		<!--<input id="playSpeed" type="range" min="100" max="500" value="250"  />-->
                               
	                           		<select id="playSpeed" name="state" style="padding:2px;"><!--class="easyui-combobox"-->
										<option value="50">急速</option>
										<option value="150">较快</option>
										<option value="250" Selected>一般</option>
										<option value="350">较慢</option>			
										<option value="450">慢速</option>			
									</select>
								</td>
	                    </tr>
	                   <!-- <tr>
	                           <td><span>屏蔽高速</span></td>
	                           <td width="" align="">
	                           		<select class="easyui-combobox" name="state" style="">
										<option value="speed-none">不屏蔽</option>
										<option value="speed-0">0KM/H速度</option>
										<option value="speed-10">10KM/H以内</option>
										<option value="speed-20">20KM/H以内</option>			
										<option value="speed-60">60KM/H以内</option>			
										<option value="speed-80">80KM/H以内</option>
										<option value="speed">人工输入</option>			
									</select>
								</td>
	            		</tr>-->
	                    </tr>
	                           <td><label><input type="checkbox" value="" id="show_bubble">显示气泡</label></td>
	                           <td><label><input type="checkbox" value="" id="show_path_point">显示轨迹点</label></td>	                    
	                    <tr>
	                    </tr>
	                           <td><label><input type="checkbox" value="" id="center_car" checked="checked" disabled="disabled">车辆居中</label></td>
	                           <td><label><input type="checkbox" value="" id="show_path_line" checked="checked">显示轨迹线</label></td>	                    
	                    <tr>	
	            		</tbody>
	           		 </table>
				</div>
           </td></tr>

	         </tbody></table>            
		</div>
	</td>	 
	</tr>
	</tbody></table>
</div>
	<script>
	
		//left: $(funtion()={alert(\"abc\");}
		//alert("abc");
		dockToRight();
		var playPanelStatus = 1;	//1: on; 0: off

		function playPanelOnOff (){
			if (playPanelStatus ==1){
				document.getElementById("playbackWin1_1").style.background= "url('/obd_web/mapPic/history/btn_pbp_open.png')" ;
				$('#playbackWin1_1').css({"background-repeat":"no-repeat"});
				$('#playbackWin2').panel('close');
				playPanelStatus = 0;
			}else{
				document.getElementById("playbackWin1_1").style.background= "url('/obd_web/mapPic/history/btn_pbp_close.png')" ;
				$('#playbackWin1_1').css({"background-repeat":"no-repeat"});
				$('#playbackWin2').panel('open');
				playPanelStatus = 1;				
			}
			dockToRight();
		};


		function dockToRight(){
			var posToLeft = $(window).width() - $('#playbackWin').width();
			$('#playbackWin').offset({left:posToLeft,top:100});
		};
	</script>
	<script>		
		var optionPanelstatus = 1;		//1: on; 0: off
		function optionPanelOnOff(){			
			if (optionPanelstatus ==1){
				$('#optionPanel').panel('close');
				optionPanelstatus = 0;
			}else{
				$('#optionPanel').panel('open');
				optionPanelstatus = 1;	
			}
		}
	</script>		

<div id="container" style="position:absolute; z-index:1"></div>
<!-- 地图 -->	
<!-- <div id="mapView" width="100%" >-->
<!--<table  width="100%" cellpadding="0" cellspacing="0"><tbody>-->
	<!--<tr><td height="20px" >
		<div id="mapTool" >
			地图选择：<select id="mapEngine" onchange='mapEngine_onChange(this.value)'>
						<option value='baidu' checked>百度地图</option>
						<option value='google'>谷歌地图</option>
					 </select>		
		</div>
	</td></tr>-->
<!--	<tr><td>
		<div id="container" style="position:absolute; z-index:1"></div>
	</td></tr>
</tbody></table>-->
<!--</div>  -->

  
<script type="text/javascript">  
function doSearch(){
	var  textValue= $('#licenseNumber').val();
	var searchType=$('#searchType').combobox('getValue');
//	alert(searchType);
	
	var recent=$('#recent').combobox('getValue');;
//	alert(recent);
	
	var startTime=$('#beginTimeV').datetimebox('getValue');
	//alert(startTime);
	
	var stopTime=$('#endTimeV').datetimebox('getValue');
	//alert($stopTime);
	
	if(stopTime != null && stopTime !=''){
		if(startTime ==null || startTime==''){
			alert("请输入开始时间！");
			return;
		}
	}
	
	if(startTime != null && startTime !=''){
		if(stopTime ==null || stopTime==''){
			alert("请输入结束时间！");
			return;
		}
	}
	
	
	//不按时间段查询,而是按时间范围项中的条件来查询
	if((startTime == null || startTime =='') &&(stopTime ==null || stopTime=='') ){
			if(recent== "last3hr"){
					var now =new Date();
				    now.setHours(now.getHours()-3);
				    startTime=now.format('yyyy-MM-dd hh:mm:ss');
	                stopTime=new Date().format('yyyy-MM-dd hh:mm:ss');
					
			}
			else if(recent== "last6hr"){
				 var now =new Date();
				    now.setHours(now.getHours()-6);
				    startTime=now.format('yyyy-MM-dd hh:mm:ss');
	                stopTime=new Date().format('yyyy-MM-dd hh:mm:ss');
			}
			else if(recent== "last12hr"){
				    var now =new Date();
				    now.setHours(now.getHours()-12);
				    startTime=now.format('yyyy-MM-dd hh:mm:ss');
	                stopTime=new Date().format('yyyy-MM-dd hh:mm:ss');
			}
			
			else if(recent== "today"){
				    var now =new Date();
				    now.setHours(now.getHours()-24);
				    startTime=now.format('yyyy-MM-dd hh:mm:ss');
	                stopTime=new Date().format('yyyy-MM-dd hh:mm:ss');
			}
					
	}
	
	 $.post("../service/deviceInfo.php",
			  {
				 optype:searchType,
				 opvalue:textValue
				 
			  },
			  function(data,status){
			  	//alert(data.length);
				  var rows=eval(data);
				  if(rows.length==0){
				  	alert("没有数据！");
				  }
				  else{
				  	glob_deviceID=rows[0].deviceID;
				  //	 alert(_deviceID);
				  	getTrackLineData(startTime,stopTime);
				  	 
				  }
			
				
			  });
	
}

var map;

$(function(){
	map = anydata.mapapi.history.initMap(curMapEngine,"container");
	map.enableScrollWheelZoom();    // 启动鼠标滚轮操
})

var curMapEngine = "baidu";
function mapEngine_onChange(newEngine){
	if (newEngine == curMapEngine) return;
	curMapEngine = newEngine;
	map = anydata.mapapi.history.initMap(curMapEngine,"container");
}


//历史行程
var trackLine;
var trackData = null;
var deviceId;
var startTime;
var stopTime;
var eventIcons = new Array();

function clearTrackLine(){
	anydata.mapapi.history.clearPolyline(curMapEngine,map,trackLine);
	
	anydata.mapapi.history.clearIcons(curMapEngine,map,eventIcons);
	
}

//  add by zjf begin
var  glob_deviceID = "<?php echo $deviceId; ?>";	

function getTrackLineData(start_time,stop_time){
	//console.log(start_time+"->"+stop_time+":"+glob_deviceID);
		if (playbackStatus>0)  stopPlayback();
		$.get("../../zend_obd/jsonAPI/getTripDetail.php", {
			deviceId: 	glob_deviceID,
			startTime:	start_time, 
			stopTime:	stop_time,
			typeTrip: "1"
		},function(jsonStr,status){	
			if (status=="success") {		
				//过滤前面的错误信息
				
				jsonStr = jsonStr.substr(jsonStr.indexOf("{"));
				trackData = eval("(" +jsonStr+ ")");	
				if (trackData.total > 0){
					displayTrackLine();	//地图显示轨迹					
				}
				else trackData = null;
			}
		});
		getHistory(glob_deviceID,start_time,stop_time);	//数据窗口加载数据
}
var points = new Array();
var linePoints = new Array();
var date3 = 0;
//画轨迹
function displayTrackLine(){
	   var dateArr= new Array();
           clearTrackLine();
	// 轨迹线
	if (trackData== null) return;
	
	points.length = 0;
	linePoints.length = 0;
	
	var i=0; //非0的有效gps信息计数
	var centX =0.0, centY=0.0, maxX=0.0,maxY=0.0,minX=0.0,minY=0.0;
	for (var trip in trackData.rows) {		
		lat = parseFloat(trackData.rows[trip].baidu_latitude) ;
		lon = parseFloat(trackData.rows[trip].baidu_longitude);
		
		if(!isValidGps(lat, lon)){		
			lat = parseFloat(trackData.rows[trip].latitude) ;//
			lon = parseFloat(trackData.rows[trip].longitude);//
		}
		
		
		if (isNaN(lat) || isNaN(lon)) continue;

		if (isValidGps(lat,lon)){			
			if(i > 0){
				linePoints[i-1] = new anydata.mapapi.history.point(lat,lon);
			}
		
			points[i]= new anydata.mapapi.history.point(lat,lon);			
                        
		    if(i<2){
		  		dateArr[i]=trackData.rows[trip].gpsStamp;
			}
			i++;
		}			
	}
	if(i>0){
		centX /=i; 
		centY /=i;
	}	
	
//	var date1 = new Date(points[0].gpsStamp);    //开始时间
//	var date2 = new Date(points[1].gpsStamp);    //结束时间

	if(i<1){
		trackLine = anydata.mapapi.history.displayPolyline(curMapEngine,map,linePoints);
	}else{
	
		var date1 = new Date(Date.parse(dateArr[0].replace(/-/g,   "/")));    //开始时间
		var date2 = new Date(Date.parse(dateArr[1].replace(/-/g,   "/")));  
	
	
		date3=(date2.getTime()-date1.getTime())/1000;  //时间差的毫秒数
		//console.log(date2+" - "+date1+" = "+date3);
	
		
		if(date3 <= 60){
			trackLine = anydata.mapapi.history.displayPolyline(curMapEngine,map,points);
		}else{
			//画线
			trackLine = anydata.mapapi.history.displayPolyline(curMapEngine,map,linePoints);
		}
	}

	displayTrackLine_events();
		
} 


$("#show_path_line").click(function(){
	if($(this).is(':checked')){
		if(date3 <= 60){
			trackLine = anydata.mapapi.history.displayPolyline(curMapEngine,map,points);
		}else{
			//画线
			trackLine = anydata.mapapi.history.displayPolyline(curMapEngine,map,linePoints);
		}
	}else{
		anydata.mapapi.history.clearPolyline(curMapEngine,map,trackLine);
	}
});

var pointCollection = null;

var Point = {
	addPoints:function(){
		var allPoints = [];
		if(date3 <= 60){
			allPoints = points;
		}else{
			allPoints = linePoints;
		}
		if (document.createElement('canvas').getContext) {  // 判断当前浏览器是否支持绘制海量点
			
			var options = {
				/*size: BMAP_POINT_SIZE_SMALL,
				shape: BMAP_POINT_SHAPE_STAR,
				color: '#d340c3'*/
			}
			pointCollection = new BMap.PointCollection(allPoints, options);  // 初始化PointCollection
			
			map.addOverlay(pointCollection);  // 添加Overlay
	
		}else{
			//for(var i=0;i<ps.length;i++){
				/*var point = new BMap.Point(lon, lat);
				//添加标注
				var marker = new BMap.Marker(point);
				
				allPoints.push(marker);
				
				map.addOverlay(marker);	*/
			//}	
			alert("此浏览器不支持此功能,建议使用谷歌,火狐或ie9及以上的浏览器!");
		}

	},
	removePoints: function(){
		if(pointCollection != null)
			map.removeOverlay(pointCollection);	
		pointCollection = null;	
	}
}


$("#show_path_point").click(function(){
	if($(this).is(':checked')){
		//allPoints.length = 0;
		Point.addPoints();
	}else{
		Point.removePoints();
		//map.clearOverlays();    //清除地图上所有覆盖物
	}
});

function displayTrackLine_events(){
	//不处理纠偏问题
	
	//对事件轨迹点画图
	var iconUrl;
	var iconH =68;
	var iconW = 51;
	var offsetY = 64;
	var offsetX = 47;
	
	var lastLat,lastLon;	//上	一个有效gps
	var hasStopIcon = false;
	var  i=0;
	
	for (var tripindex in trackData.rows) {		
		lat = parseFloat(trackData.rows[tripindex].baidu_latitude) ;//latitude
		lon = parseFloat(trackData.rows[tripindex].baidu_longitude);//longitude
		
		if(!isValidGps(lat, lon)){		
			lat = parseFloat(trackData.rows[tripindex].latitude) ;//
			lon = parseFloat(trackData.rows[tripindex].longitude);//
		}
		//console.log(trackData.rows[tripindex].code);
		//console.log(lat+"==="+lon);
		
		if ( isNaN(lat) || isNaN(lon)) continue;
		
		if ( !isValidGps(lat, lon) )	continue;	

		switch (trackData.rows[tripindex].code){
			case "3015":
				iconUrl = "../mapPic/history/ev-36.png";
				break;
			case "3016":
				iconUrl = "../mapPic/history/ev-30.png";
				hasStopIcon = true;
				break;
			case "3004":
				//if (i==0)	iconUrl = "../mapPic/history/ev-36.png"; 	//第一个有效gps,强制加上起点图标
			default:
				iconUrl = "";
				break;
		};			
		
		if (i==0) {
			iconUrl = "../mapPic/history/ev-36.png"; //第一个有效gps,强制加上起点图标
		}
		
		if (iconUrl != "") {
			var markerOfPoint = anydata.mapapi.history.displayTrackPoint(curMapEngine,map,lat,lon,iconUrl,iconH,iconW,offsetY,offsetX);				
			var index = eventIcons.length;
			eventIcons[index]= markerOfPoint;
		}
		
		lastLat = lat;
		lastLon = lon;		
		i++;
	}	
	
	if (!hasStopIcon){	//最后一个有效gps,如果没有则强制加上终点图标
		var markerOfPoint = anydata.mapapi.history.displayTrackPoint(curMapEngine,map,lastLat,lastLon,"../mapPic/history/ev-30.png",iconH,iconW,offsetY,offsetX);
		var index = eventIcons.length;
		eventIcons[index]= markerOfPoint;		
	}
}

//播放历史轨迹 playback for trip info
var playbackStatus =0;	//0: stop; 1:playing; 2:Pause
var playInterval = 250;	//播放间隔
var playProgress = 0; 
var lastPoint =null;	
//var markerOfPoint;		//地图播放点

//改变 插入速度
$("#playSpeed").change(function(){
	playInterval = $("#playSpeed").val();
});

function playbackHistory(){
	if (playbackStatus==0){
		
		playbackStatus=1;
		//clearTrackLine();
		$('#playbackHistory').html("<span class='l-btn-left'><span class='l-btn-text'>暂停</span></span>");
		//alert(playbackStatus);		
		window.setTimeout(showOnePoint, 10);
	}
	else{
		if (playbackStatus ==1){
			playbackStatus = 2;	//pause
			$('#playbackHistory').html("<span class='l-btn-left'><span class='l-btn-text'>播放</span></span>");
			//alert(playbackStatus);
		}
		else{
			playbackStatus =1; //continue;
			$('#playbackHistory').html("<span class='l-btn-left'><span class='l-btn-text'>暂停</span></span>");		
			//alert(playbackStatus);	
		}
	} 	
	
}

function stopPlayback(){
//停止播放
	playbackStatus=0;
	playProgress = 0;
	removeTrackPoint(lastPoint);

	lastPoint = null;
	
	$('#playbackHistory').html("<span class='l-btn-left'><span class='l-btn-text'>播放</span></span>");
	$('#Test1').html(playProgress.toString());	
	
}

function showOnePoint() {
	if (playbackStatus == 2) {  //暂停则Idle
		window.setTimeout(showOnePoint, playInterval);
		return;
	} 
	if (playbackStatus == 0) return;
	
	var curLat,curLng;	
	$('#Test1').html(playProgress.toString());	
	if (trackData && trackData.total >0){
		anydata.mapapi.history.removeTrackPoint(curMapEngine, map,lastPoint);
		do {
			curHeading = parseFloat( trackData.rows[playProgress].heading) + 90.0;  
			curLat = trackData.rows[playProgress].baidu_latitude;
			curLng = trackData.rows[playProgress++].baidu_longitude;					
		}while (!isValidGps(curLat,curLng) && playProgress < trackData.total);	
		
		if (playProgress < trackData.total) {
			//displayTrackPoint (curLat,curLng);	
			
			if($("#show_bubble").is(':checked')){			
				lastPoint = anydata.mapapi.history.displayTrackPoint(
								curMapEngine, 
								map, 
								curLat, 
								curLng,
								"/obd_web/mapPic/car.png",
								21,43,10.5,21.5,
								curHeading,
								trackData.rows[playProgress]);	//画点
			}else{
				lastPoint = anydata.mapapi.history.displayTrackPoint(
								curMapEngine, 
								map, 
								curLat, 
								curLng,
								"/obd_web/mapPic/car.png",
								21,43,10.5,21.5,
								curHeading);	//画点
			}
			showNextPoint();
		}else {
			playbackStatus=0;
			playProgress=0;
			$('#playbackHistory').html("<span class='l-btn-left'><span class='l-btn-text'>播放</span></span>");
		}
	}
}

function showNextPoint(){
	if (playbackStatus == 2) {  //暂停则Idle
		window.setTimeout(showOnePoint, playInterval);
		return;
	} 
	if (playbackStatus == 0) return;
	
	//lastPoint = markerOfPoint;
	if (playProgress < trackData.total){
		//如果未结束,启动下个点的处理
		window.setTimeout(showOnePoint, playInterval);	
	}
}

function removeTrackPoint(lastPoint){
	//lastPoint: Type -- overlay
	map.removeOverlay(lastPoint);
}

function displayTrackPoint(lat,lon){
	//alert("displayTrackPoint");
	var point = new BMap.Point(lon, lat);
	var marker = new BMap.Marker(point);
	map.addOverlay(marker); //	保存句柄
	markerOfPoint = marker;
	//return marker;	//	返回句柄
	
}


var westStatus=1;
function  westSwitch(){
	if(westStatus==1){
		$('#cc').layout('hidden','south');
		westStatus=0;
	}
	else{
		$('#cc').layout('show','south');
		westStatus=1;
	}
	
}

function getHistory(deviceId,startTime,stopTime){
	var pageURL = '../../zend_obd/jsonAPI/getTripDetail.php?startTime='+startTime+'&'+'stopTime='+stopTime+'&deviceId='+deviceId+"&typeTrip=1";

	$('#dg').datagrid({  
	
		url:pageURL, 
		
		singleSelect:true,//是否单选
	
		pagination:true,//分页控件  
	
		rownumbers:true//行号

	});
	
	//设置分页控件  
	var p = $('#dg').datagrid('getPager');  
	
	$(p).pagination({  
	
		pageSize: 10,//每页显示的记录条数，默认为10  
	
		pageList: [5,10,15,20,30,40,50],//可以设置每页记录条数的列表  
	
		beforePageText: '第',//页数文本框前显示的汉字  
	
		afterPageText: '页 共 {pages} 页',  
	
		displayMsg: '当前显示 {from} - {to} 条记录   共 {total} 条记录'
	
	});	

}



</script>  
</div> 
</body>   
</html>
<?php
?>