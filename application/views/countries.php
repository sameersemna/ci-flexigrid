<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>CodeIgniter Flexigrid Countries</title>

<link href="<?php echo $this->config->item('base_url') ?>assets/jquery/jquery-ui-1.11.2/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $this->config->item('base_url');?>assets/flexigrid/css/flexigrid.css" rel="stylesheet" type="text/css" />
<?php /*<link href="<?php echo $this->config->item('base_url');?>assets/flexigrid/css/style.css" rel="stylesheet" type="text/css" />*/ ?>

<script type="text/javascript" src="<?php echo $this->config->item('base_url') ?>assets/jquery/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('base_url') ?>assets/jquery/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('base_url') ?>assets/jquery/jquery-ui-1.11.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('base_url');?>assets/flexigrid/js/flexigrid.js"></script>

<style type="text/css">

	::selection{ background-color: #E13300; color: white; }
	::moz-selection{ background-color: #E13300; color: white; }
	::webkit-selection{ background-color: #E13300; color: white; }

	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body{
		margin: 0 15px 0 15px;
	}
	
	p.footer{
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #D0D0D0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}
	
	#container{
		margin: 10px;
		border: 1px solid #D0D0D0;
		-webkit-box-shadow: 0 0 8px #D0D0D0;
	}
</style>

</head>
<body>
<?php
echo $js_grid;
?>
<script type="text/javascript">

function test(com,grid){
    if (com=='Select All'){
		$('.bDiv tbody tr',grid).addClass('trSelected');
    }
    
    if (com=='DeSelect All'){
		$('.bDiv tbody tr',grid).removeClass('trSelected');
    }
    
    if (com=='Delete'){
	   if($('.trSelected',grid).length>0){
		   if(confirm('Delete ' + $('.trSelected',grid).length + ' items?')){
				var items = $('.trSelected',grid);
				var itemlist ='';
				for(i=0;i<items.length;i++){
					itemlist+= items[i].id.substr(3)+",";
				}
				$.ajax({
				   type: "POST",
				   url: "<?php echo site_url("/countries_feed/deletec");?>",
				   data: "items="+itemlist,
				   success: function(data){
					$('#flex1').flexReload();
					alert(data);
				   }
				});
			}
		} else {
			return false;
		} 
	}          
} 

///Filter for Alphabet Buttons
function filter_alpha(alpha,grid){ 
	//check if letter selected is # for all
	alpha = (alpha == '#')?'%%':alpha;
	var filters = {"groupOp":"AND","rules":[{"field":"name","op":"bw","data":alpha}]};
	filters_value = JSON.stringify(filters);
	$('#flex1').flexOptions({
		newp:1,
		params:[
			{name:'filters', value: filters_value},
			{name:'qtype', value: $('select[name=qtype]').val()}
		]
	});
	
	$('#flex1').flexReload();
} 

</script>


<div id="container">
	<h1>Welcome to CodeIgniter with Flexigrid Demo!</h1>

	<div id="body">
	    <table id="flex1" style="display:none"></table>
	</div>

	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds</p>
</div>

</body>
</html>
