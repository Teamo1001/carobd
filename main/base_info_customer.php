	
<?php include("session.php"); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>基础信息管理</title>
	<link rel="stylesheet" type="text/css" href="../themes/default/easyui.css">
	<link rel="stylesheet" type="text/css" href="../themes/icon.css">
	<link rel="stylesheet" type="text/css" href="../demo.css">
    <link rel="stylesheet" type="text/css" href="../css/customTreeCommon.css">
	<script type="text/javascript" src="../jquery.min.js"></script>
	<script type="text/javascript" src="../jquery.easyui.min.js"></script>
</head>
<body>	

	<div  style="overflow:auto;padding:0px;">
				
			<div  style = "width:250px;float:left;right-margin:10px">
			         <div id="p" class="easyui-panel" title="客户"  style="width:230px;height:500px;">				
				<ul class="easyui-tree"   
				 <?php 
	          if($_SESSION["op"]=="deplogin")
	          { 
	        ?>
			url="../service/customer.php?defDep=-1" 
			<?php 
	          }
	          else 
	          { 
	       ?>
			 url="../service/customer.php?defDep=<?php echo  $userVo->depID ?>" 	
			 <?php 
	          }
	         
	       ?> 
				 data-options="animate:true,onlyLeafCheck:true"  id="depID">
				</ul>
			         </div>
			</div>
			
			
		<table id="custMan"  style="width:1200px;height:500px" data-options="
								rownumbers:true,
								singleSelect:true,
								autoRowHeight:false,
								pagination:true,
								pageSize:10">
		<thead>
			<tr>
								<th data-options="field:'name',width:350">名称</th>
							
								<th data-options="field:'time',width:180,align:'right'">注册时间</th>
						<!--		<th data-options="field:'lon',width:170,align:'right'">经度</th>
								<th data-options="field:'lat',width:170,align:'right'">纬度</th>   -->
								<th data-options="field:'parentName',width:165,align:'right'">父机构名称</th>
								
								<th data-options="field:'operation',width:80,align:'right'">操作 </th> 
							
								  
							</tr>
		</thead>
	</table>
	<div id ="tb" style="height:auto">
		<input type = "button" value = "添加客户" onclick="add_customer()">
	</div>
			
			
</div>

		<script>
		//初始为根节点，从页面参数传入
	    var depID = 1077;//'< ?php echo  $userVo->depID ?>';
	   
	    var traceWin1 = null;
	    var traceWin = null;
	    
		function add_customer()
		{			
			//判断该窗口(NewWindow)是否已经存在，如果已经存在，则先关闭窗口，然后再打开新窗口
			if(traceWin1){
				if(!traceWin1.closed)
					traceWin1.close();
			}
			
			traceWin1=window.open('addcustomer.html','编辑客户信息','height=400, width=680, top=200,left=500');

		}
		
		function adit_customer(id)
		{
			if(traceWin){
				if(!traceWin.closed)
					traceWin.close();
			}
			traceWin=window.open('aditcustomer.html?ID='+id,'编辑客户信息','height=400, width=450, top=200,left=500, toolbar=no, menubar=no, scrollbars=no, resizable=yes,location=yes, status=no');
		
		}
		
		

		function show_confirm(id)
		{
		
		var r=confirm("确认删除该客户吗？");
		
		
		if (r==true)
		  {
		
		   var url="../../zend_obd/jsonAPI/deletecustomer.php?vin="+id;
		   $.get("../../zend_obd/jsonAPI/deletecustomer.php",{
			   vin:id

			  },
			  function(data,status){
			// 	if(data=='200'){
					
					alert("删除成功!");
					
					$('#custMan').datagrid({   
						 url:'../../zend_obd/jsonAPI/customerManage.php?vin='+depID
					}); 
	//		 	}
				
			  });
	
		  }
		}
		
		$('#custMan').datagrid({
				toolbar: '#tb'
			});
		
		$('#custMan').datagrid({   
			 url:'../../zend_obd/jsonAPI/customerManage.php?vin='+depID
		}); 
		
		
		$('#depID').tree({
			onClick: function(node){		
				depID=node.id;
				
				$('#custMan').datagrid({   
					url:'../../zend_obd/jsonAPI/customer.php?vin='+node.id
				});
	
			}
		});
		
		function fresh(){

			$('#custMan').datagrid({   
			    url:'../../zend_obd/jsonAPI/customerManage.php?vin='+depID
			}); 
		
			$('#depID').tree({   
				 url:'../service/administration.php?defDep=<?php echo  $userVo->depID ?>'
			}); 
		}
		

		
		
		</script>
		
		
		<script>
		function getData(){
			var rows = [];
			for(var i=1; i<=800; i++){
				var amount = Math.floor(Math.random()*1000);
				var price = Math.floor(Math.random()*1000);
				rows.push({
					inv: 'Inv No '+i,
					date: $.fn.datebox.defaults.formatter(new Date()),
					name: 'Name '+i,
					amount: amount,
					price: price,
					cost: amount*price,
					note: 'Note '+i
				});
			}
			return rows;
		}
		
		function pagerFilter(data){
			if (typeof data.length == 'number' && typeof data.splice == 'function'){	// is array
				data = {
					total: data.length,
					rows: data
				}
			}
			var custMan = $(this);
			var opts = custMan.datagrid('options');
			var pager = custMan.datagrid('getPager');
			pager.pagination({
				onSelectPage:function(pageNum, pageSize){
					opts.pageNumber = pageNum;
					opts.pageSize = pageSize;
					pager.pagination('refresh',{
						pageNumber:pageNum,
						pageSize:pageSize
					});
					custMan.datagrid('loadData',data);
				}
			});
			if (!data.originalRows){
				data.originalRows = (data.rows);
			}
			var start = (opts.pageNumber-1)*parseInt(opts.pageSize);
			var end = start + parseInt(opts.pageSize);
			data.rows = (data.originalRows.slice(start, end));
			return data;
		}
		
		$(function(){
			//$('#custMan').datagrid({loadFilter:pagerFilter}).datagrid('loadData', getData());
			console.log($('#depID').tree('getData'));
		});
	</script>
		

	

</body>
</html>
	
	