<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>File List</title>
<link rel="stylesheet" href="/agentbox/style/structure.css" type="text/css" media="screen">
<script type="text/javascript" src="js/jquery-1.6.4.min.js"></script>
    
<style type="text/css">
	.OverDragBox{
		cursor:move;border:0px solid #000000;background-color:#F9F7ED;
	}
	.DragDragBox{
		cursor:move;border:0px solid #000000;background-color:#F9F7ED;
	}
</style>
<script src="js/drag.js" type="text/javascript"></script>
<script language="javascript">
var uId = "<{$uId}>"; //current user
var gId = "<{$gId}>"; //current user group
var pId = "<{$pId}>";//current layer
var fPath = ""; //current folder path
var fId; //current file or folder
var cIndex; //current fInfo index
var opMethod = "createFolder";
var info;
var pathInfo; 
var $ = function (id) {
	return document.getElementById(id)
};
function openCreateFolder(){
	opMethod = "createFolder";
	openEditorFolder();
}

function openEditorFolder(){
	
	$('folderName').value = '';
	$('tags').value = '';
	$('pwd').value = '';
	$('folderEditor').style.display = 'block';
	closeUpDiv();
	
}
function closeCFDiv(){
	$('folderEditor').style.display = 'none';
}

function openUpDiv(){
	closeCFDiv();
	if (pId == 0 || pId == ""){
		alert("You can not upload file in root folder.")
		return false;
	}
	$('uploadFile').style.display = 'block';
	$('fpId').value = pId;
	
}

function closeUpDiv(){
	$('uploadFile').style.display = 'none';
}

function createUpArrow(){
	var divString = "<div class='folder'><a href='#' onclick='enter(-1)'><img border='0' src='images/up.gif' width='40' height='39' /></a></div>";
	var divEntire = setFolderDiv(divString);
	$('listTd').appendChild(divEntire);
}

function createFolderDiv(type, name, index){
	if(type == 1)
		var divString = "<div class='folder' id='dragT"+index+"' dragObj='1' overClass='OverDragBox' dragClass='DragDragBox' dragIndex="+index+"><img border='0' src='images/icon_folder.gif' width='46' height='43' /><br /><div class='folderEnter'><a href='#' onclick='enter("+index+")'>"+name+"</a></div><br /><div class='icon'><a href='#' onclick=editFolder("+index+")><img border='0' src='images/edit.gif' width='10' height='10'/></a>&nbsp;&nbsp;&nbsp;<a href='#' onclick=delFolder("+index+")><img border='0' src='images/del.gif' width='10' height='10' /></a></div></div>";
	else if(type == 2)
		var divString = "<div class='folder' id='dragT"+index+"' dragObj='1'overClass='OverDragBox' dragClass='DragDragBox' dragIndex="+index+"><img border='0' src='images/icon_folder_protected.gif' width='46' height='43' /><br /><div class='folderEnter'><a href='#' onclick='enter("+index+")'>"+name+"</a></div><br /><div class='icon'><a href='#' onclick=editFolder("+index+")><img border='0' src='images/edit.gif' width='10' height='10'/></a>&nbsp;&nbsp;&nbsp;<a href='#' onclick=delFolder("+index+")><img border='0' src='images/del.gif' width='10' height='10' /></a></div>	</div>";
	var divEntire = setFolderDiv(divString);
	//divEntire.appendChild(div);
	$('listTd').appendChild(divEntire);
}

function setFolderDiv(arg){
	var objE = document.createElement("div"); 
	objE.innerHTML = arg; 
	return objE;
}

function clearAllF(){
	while ($('listTd').firstChild) {
		oldNode = $('listTd').removeChild($('listTd').firstChild);
		oldNode = null;
	}
	while($('fileTb').firstChild){
		oldNode = $('fileTb').removeChild($('fileTb').firstChild);
		oldNode = null;
	}
	closeUpDiv();
	closeCFDiv();
	closePwd();
}

var xmlhttp;
function createxmlhttprequest()
{
	//var xmlhttp=null;
	try
	{
		// firefox, opera 8.0+, safari
		xmlhttp=new XMLHttpRequest();
	}
	catch(e)
	{
		// internet explorer
		try
		{
			xmlhttp=new ActiveXObject("msxml2.xmlhttp");
		}
		catch(e)
		{
			xmlhttp=new ActiveXObject("microsoft.xmlhttp");
		}
	}
	return xmlhttp;
}
function startrequest(url)
{
	createxmlhttprequest();
	try
	{
		xmlhttp.onreadystatechange=handlestatechange;
		xmlhttp.open("GET",url,true);
		xmlhttp.send(null);
	}
	catch(exception)
	{
		alert("xmlhttp fail");
	}
}
function handlestatechange()
{
	if(xmlhttp.readyState==4 || xmlhttp.readyState=="complete")
	{
		if(xmlhttp.status==200||xmlhttp.status==0)
		{
			var result=xmlhttp.responseText;
			
			if (result=="The password is incorrect.") {
				$('error_message').innerHTML = result;
				return false;
			} else {
				fInfo=eval("("+result+")");
				for(var i in fInfo){
				switch(fInfo[i][0]){
						case 1:
							listAll(fInfo[i])
							break;
						case 2:
							setPath(fInfo[i]);
							break;
					}
				}
			}
		}
	}
}

function insertTHEAD() {
    var THEADData = ["file","date","delete"]
    var newCell
    var theTable = $('fileTb');
    var newTHEAD = theTable.createTHead()
    var newRow = newTHEAD.insertRow(-1)
    for (var i = 0; i < THEADData.length; i++) {
	var th = document.createElement('th');
        th.innerHTML = THEADData[i];
	th.align = 'left'
	th.width = "40%";
	newRow.appendChild(th);
    }
}

function listAll(fInfo){
	clearAllF();
	createUpArrow();
	insertTHEAD();
	var f;
	var fileNum = 0;
	info = fInfo[1];
	for(f in info){
		var pwd = info[f].pwd == '' ? 1 : 2;
		if(info[f].name){
			if(info[f].isFolder == 1)
				createFolderDiv(pwd, info[f].name, f);
			else if(info[f].isFolder == 0){
				createFileTr(info[f], f);
				fileNum++;
			}
		}
	}
	if(fileNum == 0){
		var newTr = $('fileTb').insertRow(-1);
		var newTd = newTr.insertCell();
		newTd.innerHTML = "There are no files in this folder.";
		var newTd = newTr.insertCell(-1);
		var newTd = newTr.insertCell(-1);
	}
	pId = fInfo[2];
}

function setPath(pInfo){
	pathInfo = pInfo[1];
	var path = "";
	for(var i in pathInfo){
		if(pathInfo[i][1]){
			path = path + "<a href='#' onclick='jump("+pathInfo[i][0]+")'>"+pathInfo[i][1]+"</a> /";
		}
	}
	if($('cateTd').firstChild) {
		$('cateTd').removeChild($('cateTd').firstChild);
	}
	var objE = document.createElement("div"); 
	objE.innerHTML = "<h2>"+path+"</h2>";
	$('cateTd').appendChild(objE);
}

function changeFold(index, aId){
	if(aId != -1)
		aId = info[aId].fId;
	new startrequest("index.php?module=filelist&method=moveF&fId="+info[index].fId+"&aId=" + aId + "&pId=" + pId);
}

new startrequest("index.php?module=filelist&method=getEnterList&pId=" + pId);

function createFolder(){
	closeCFDiv();
	var url = "index.php?module=filelist&method=" + opMethod
	+ "&folderName=" + $('folderName').value + "&tags=" + $('tags').value + "&pwd=" + $('pwd').value + "&pId=" + pId;
	if(opMethod == "editF")
		url = url + "&fId=" + fId;
	new startrequest(url);
}

function editFolder(index){
	if(info[index].owner == uId || gId == 1){
		opMethod = "editF";
		fId = info[index].fId;
		openEditorFolder();
		$('folderName').value = info[index].name;
		$('tags').value = info[index].tags;
		$('pwd').value = info[index].pwd;
	}else
		alert('You can`t edit it.');
}

function delFolder(index){
	if(info[index].owner == uId || gId == 1){
		if(confirm('Are you sure you wish to delete it?')){
			var url = "index.php?module=filelist&method=delFolders&fId=" + info[index].fId + "&isfolder=" + info[index].isFolder + "&fpId=" + info[index].pId;
			console.log(url);
			new startrequest(url);
		}
	}else
		alert('You can`t delete it.');
}

function createFileTr(fileInfo, index){
	var newTr = $('fileTb').insertRow(-1);
	var newTd0 = newTr.insertCell(0);
	newTd0.width = "182";
	var tInfo0 = "<a href='#' onclick='enter("+index+")'>"+fileInfo.name+"</a>"
	//var tInfo0 = "<a href='upload/"+fileInfo.pname+"'>"+fileInfo.name+"</a>";
	newTd0.innerHTML = tInfo0;
	
	var d = new Date(fileInfo.pname.split('.')[0] * 1000); 
	var newTd1 = newTr.insertCell(1);
	newTd1.width = "196";
	var tInfo1 = (d.getDate())+"/"+(d.getMonth()+1)+"/"+(d.getFullYear());
	newTd1.innerHTML = tInfo1;
	
	var newTd3 = newTr.insertCell(2);
	newTd3.width = "54";
	var tInfo3 = "<a href='#' onclick=delFolder("+index+")>delete</a>";
	newTd3.innerHTML = tInfo3;
}

function jump(index){
	var url = "index.php?module=filelist&method=enterFolder&pId=" + index;
	new startrequest(url);
}

function enter(index, pwd){
	if(index != -1 && info[index].pwd && info[index].owner != uId && gId != 1 ){
		this.cIndex = index;
		$('enPwd').value = '';
		$('fId').value = info[index].fId;
		$('enterPwd').style.display = 'block';
		return;
	}
	if(index != -1 && info[index].type == 2){
		window.open("upload/"+info[index].pname);
	}else{
		var url = "index.php?module=filelist&method=";
		if(index == -1)
			url = url + "enterUpFolder&pId=" + pId; 
		else
			url = url + "enterFolder&pId=" + info[index].fId;
		new startrequest(url);
	}
}

function enterPwd(){
	//enter(this.cIndex, $('enPwd').value);
	var url = "index.php?module=filelist&method=ckpass&fId=" + $('fId').value + "&pwd=" + $('enPwd').value +"&fpId="+pId;
	new startrequest(url) 
	
}

function closePwd(){
	$("error_message").innerHTML = "";
	$('enterPwd').style.display = "none";
}

function searchF(){
	var url = "index.php?module=filelist&method=searchF&key=" + $('key').value;
	new startrequest(url);
}
</script>

</head>

<body>
 <h2>Document Library  
    <font size='2'>
    <a href='index.php?module=contact&method=display'>Contacts</a>  
    <a href='index.php?module=calendar&method=display'>Calendar Events</a>  
    <a href='index.php?module=abtask&method=display'>Tasks</a>
    <a href='index.php?module=user&method=logout'>logout</a>
    </font>
 </h2>
 <div class="hui-container">
 <div class="hui-block">
<table width="1000" border="0" class="hui-table hui-block-inner">
	<tr>
		<td height="48">
			<a href="#" onclick="openCreateFolder()">Create Folder</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="openUpDiv()">Upload File</a>&nbsp;&nbsp;&nbsp;
			<input type="text" name="key" id="key" /> <input type="submit" name="button" id="button" value="Search" onclick="searchF()"/><div id="root_path"><div>
		</td>
	</tr>
	<tr>
		<td id="cateTd"></td>
	</tr>
	<tr>
		<td >
			<div id="listTd" class = "hui-floatgrid">
			<div>
				<div class="folder">
					
					<a href="#"><img border="0" src="images/up.gif" width="40" height="39" /></a>
					
				</div>
			</div>
			</div>
		</td>
	</tr>
	<tr>
		<td style="height=100px"><td>
	</tr>
	<tr>
		<td>
		<div>
			<h2>File List</h2>
		</div>
		<div>
			<table width="500" border="0" id="fileTb" class = "hui-table hui-block-inner">
				<tr id="fileTr">
					<td width="182"><a href="#">if you see this message it means there is error on the page</a></td>
					<td width="196">&nbsp;</td>
					<td width="50"><a href="#">edit</a></td>
					<td width="54"><a href="#">delete</a></td>
				</tr>
			</table>
		</div>
	</td></tr>
</table>
</div>
</div>
<div id="folderEditor" style="display:none; margin-top = -200px; width: 679px; height: 200px; padding: 16px; border: 5px solid orange; background-color: white; z-index:1002; ">
	
	Folder Name:
		<input name="folderName" type="text" id="folderName" size="20" maxlength="20" />
		Max Character Length 20
	
	<p>TAGS:
		<input name="tags" type="text" id="tags" size="60" maxlength="60" />
	Max Character Length 60</p>
	<p>Visit Password:
		<input name="pwd" type="password" id="pwd" size="30" maxlength="30" />
	Everyone can visit this folder if set empty.</p>
	<p>
		<input type="submit" name="button2" id="button2" value="Submit" onclick="createFolder()"/>
		<input type="reset" name="Cancel" id="Cancel" value="Cancel" onclick="closeCFDiv()"/>
	</p>
	
</div>

<div id="uploadFile" style="display:none; width: 679px; height: 180px; padding: 16px; border: 5px solid orange; background-color: white; z-index:1002; ">
	<form enctype="multipart/form-data" method="post" action="index.php">
	<input type="hidden" name="fpId" id="fpId"/>
	<input type="hidden" name="module" id="module" value="filelist"/>
	<input type="hidden" name="method" id="method" value="upload"/>
	<p>TAGS:
		<input name="fileTags" type="text" id="fileTags" size="60" maxlength="60" />
	Max Character Length 60</p>
	<p>
			<input type="file" name="upFile" id="upFile" />
	</p>
	<p>
		<input type="submit" name="button2" id="button2" value="Submit"/>
		<input type="reset" name="Cancel" id="Cancel" value="Cancel" onclick="closeUpDiv()"/>
	</p>
	</form>
</div>

<div id="enterPwd" style="display:none; top: 214px; left: 172px; width: 425px; height: 28px; padding: 16px; border: 5px solid orange; background-color: white; z-index:1002;">
	<div id="error_message"></div>
	<label for="pwd"></label>
	Password:
	<input type="text" name="enPwd" id="enPwd" />
	<input type="hidden" name="fId" id="fId" />
	<input type="submit" name="button3" id="button3" value="Submit" onclick="enterPwd()"/>
	<input type="submit" name="enCancel" id="enCancel" value="Cancel" onclick="closePwd()"/>		
</div>
</body>
</html>
