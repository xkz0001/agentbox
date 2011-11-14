<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="stylesheet" href="style/structure.css" type="text/css" media="screen">
<title>SimpleTree Drag&Drop</title>
<style>

</style>
<script type="text/javascript" src="js/jquery-1.6.4.min.js"></script>
<script type="text/javascript" src="js/jquery.simple.tree.js"></script>

<script type="text/javascript">
var simpleTreeCollection;
$(document).ready(function(){

	$.ajax({
		url:'index.php?module=documentlibrary&method=result_response',
		type:'post',
		//dataType:'json',
		//contentType: "String",
		data:'id=0',
		success:function(result){
			var fInfo=$.parseJSON(result);			
			for(var i in fInfo){
				$("#root_ul").append("<li id='"+fInfo[i].id+"'><span class=text>"+fInfo[i].title+"</span><ul class='ajax'><li id='f"+fInfo[i].id+"'>{url:index.php?module=documentlibrary&method=get_child_doc_html&pid="+fInfo[i].id+"}</li></ul></li>");
			}

			simpleTreeCollection = $('.simpleTree').simpleTree({
				autoclose: false,
				afterContextMenu: true,
				afterClick:function(node){
					$.ajax({
						url:'index.php?module=documentlibrary&method=get_permission',
						type:'post',
						data:'id='+node.attr('id'),
						success:function(result){
							var pInfo=$.parseJSON(result);
							setpermission(pInfo, node);
						}
					});
					
				},
				afterDblClick:function(node){
					if (node.attr('class')=='doc' || node.attr('class')=='doc-last' ){
						alert("view the file depend on current user role and file permission");
					}
					
				},
				afterMove:function(destination, source, pos){
					$.ajax({
						url:'index.php?module=documentlibrary&method=move',
						type:'post',
						data:'did='+destination.attr('id')+'&sid='+source.attr('id')+'&pos='+pos,
						success:function(result){
							var pInfo=$.parseJSON(result);
							setpermission(pInfo, source);
						}
					});
				},
				afterAjax:function()
				{
					//alert('Loaded');
				},
				animate:true
				
			});
		}
	});



	$("#create_folder").click(function(){
		init_create_folder();
		$("#ed_div").show();
	});
	
	$("#create_file").click(function(){
		init_create_file();
		$("#ed_div").show();
	});

	$("#folder_cancel").click(function(){
		$("#folderEditor").hide();
	});
	
	$("#file_cancel").click(function(){
		$("#fileEditor").hide();
	});

	$("#edit").click(function(){
		var is_folder=$('#doc_is_folder')


		$("#ed_div").show();
	});

	function init_create_folder(){
		//set editor div element
		$("#ed_div_header").html('Create Folder');
		
		$("#ed_div_file_selector_li").hide();
		$("#ed_div_file_tags_li").hide();
		$("#ed_div_folder_title_li").show();
		
		$("#ed_div_id").val('');
		$("#ed_div_parent_id").val($("#doc_parent_id"));
		$("#ed_div_is_folder").val('1');
		$("#ed_div_folder_name").val('');
		$("input[name='ed_div_permission'][value=0]").attr("checked",true);
		
	}

	function init_create_file(){
		//set editor div element
		$("#ed_div_header").html('Upload File');
				
		$("#ed_div_file_selector_li").show();
		$("#ed_div_file_tags_li").show();

		$("#ed_div_folder_title_li").hide();

		$("#ed_div_id").val('');
		$("#ed_div_parent_id").val($("#doc_parent_id"));
		$("#ed_div_is_folder").val('0');
		$("#ed_div_file_tags").val('');
		$("input[name='ed_div_permission'][value=0]").attr("checked",true);
		
	}

	

	function setpermission(pInfo, node){
		if(pInfo.create_folder==0)
			$("#create_folder").attr("disabled", true); 
		else
			$('#create_folder').removeAttr("disabled"); 
		if(pInfo.create_file==0)
			$("#create_file").attr("disabled", true);
		else
			$('#create_file').removeAttr("disabled"); 
		if(pInfo.edit==0)
			$("#edit").attr("disabled", true);
		else
			$('#edit').removeAttr("disabled"); 
		if(pInfo.delete==0)
			$("#delete").attr("disabled", true);
		else
			$('#delete').removeAttr("disabled"); 
		
		$("#doc_id").val(pInfo.id);
		$("#doc_title").val(pInfo.title);
		$("#doc_tags").val(pInfo.tags);
		$("#doc_permission").val(pInfo.permission);
		$("#doc_is_folder").val(pInfo.is_folder);

		$("#ed_div").hide();
	}
});
</script>
</head>

<body>

<table width="1000" border="0" class="hui-table hui-block-inner">
	<tr>
		<td height="48">
			<input type="button" id="create_folder" disabled value = "Create Folder" >

			<input type="button" id="create_file" disabled value = "Create File">

			<input type="button" id="edit" disabled value = "Edit">

			<input type="button" id="delete" disabled value = "Delete">

			<input type="text" name="key" id="key" />
			<input type="button" id="search" value = "Search">

		</td>
	</tr>
	<tr>
		<td>
			<input type="hidden" id="doc_id" value = "" >
			<input type="hidden" id="doc_title" value = "" >
			<input type="hidden" id="doc_tags" value = "" >
			<input type="hidden" id="doc_premission" value = "" >
			<input type="hidden" id="doc_is_folder" value = "" >
		</td>
	</tr>
</table>

<div>
<ul class="simpleTree">
	<li class="root" id='1'><span>Dodument Library</span>
		<ul id="root_ul">

		</ul>
	</li>
</ul>
</div>

<div id="ed_div" class="hui-block" style = "display:none">
	<form class="hui-form hui-block-inner"  enctype="multipart/form-data" method="post" action="">
		<input type="hidden" name = "ed_div_id" id="ed_div_id" >
		<input type="hidden" name = "ed_div_parent_id" id="ed_div_parent_id" >
		<input type="hidden" name="ed_div_is_folder" id="ed_div_is_folder" value="0">
		<input type="hidden" name="module" id="module" value="documentlibrary"/>
		<input type="hidden" name="method" id="method" value="update_doc"/>
		<ul class="hui-form-block">
			<li class="hui-form-section">
				<h3 id="ed_div_header"></h3>
			</li>
			<li id="ed_div_folder_title_li">
				<label class="desc">Folder Name</label>
				<div>
					<input type="text"  name="ed_div_folder_title"  id="ed_div_folder_title" class="field text small"  maxlength="250" value="">
				</div>
			</li>
			<li id="ed_div_file_selector_li">
				<input type="file" name="ed_div_file" id="ed_div_file" />
			</li>
			<li id="ed_div_file_tags_li">
				<label class="desc">Tags</label>
				<div>
					<input type="text"  name="ed_div_tags"  id="ed_div_tags" class="field text small"  maxlength="250" value="">
				</div>
			</li>
			<li>
				<label class="desc">Permission Setting</label>
				<div>
					<span>
						<input class="field radio" name="ed_div_permission" type="radio" value="0" checked >
						<label class="choice">
						<b>Public</b>
						</label>
					</span>
					<span>
						<input class="field radio" name="ed_div_permission" type="radio" value="1" >
						<label class="choice">
						<b>User Read Only</b>
						</label>
					</span>
					<span>
						<input class="field radio" name="ed_div_permission" type="radio" value="2" >
						<label class="choice">
						<b>User Edit/Delete</b>
						</label>
					</span>
					<span>
						<input class="field radio" name="ed_div_permission" type="radio" value="3" >
						<label class="choice">
						<b>Master Only</b>
						</label>
					</span>
				</div>
			</li>

		</ul>
		<div class="hui-form-submit">
                    <input class="hui-submit hui-submit-confirm hui-submit-primary" type="submit" value="Update">
		    <input type='reset' id='ed_div_cancel' value="Cancel">
                </div>
	</form>
</div>

</body>

</html>
