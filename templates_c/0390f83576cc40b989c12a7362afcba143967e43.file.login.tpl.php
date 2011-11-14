<?php /* Smarty version Smarty-3.1.3, created on 2011-11-06 11:54:36
         compiled from ".\templates\login.tpl" */ ?>
<?php /*%%SmartyHeaderCode:74444eb6676ce6f418-43860272%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0390f83576cc40b989c12a7362afcba143967e43' => 
    array (
      0 => '.\\templates\\login.tpl',
      1 => 1318660077,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '74444eb6676ce6f418-43860272',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'errorInfo' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.3',
  'unifunc' => 'content_4eb6676cf4169',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4eb6676cf4169')) {function content_4eb6676cf4169($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login</title>
<style type="text/css">
.tLabel {
	text-align: right;
}
</style>
</head>

<body>
<div><font color='red'><?php echo $_smarty_tpl->tpl_vars['errorInfo']->value;?>
</font>
		<form action="index.php?module=User&method=auth" method="post">
		<table width="500" border="0" align="center">
				<tr>
						<td class="tLabel">Username:</td>
						<td><label for="username"></label>
						<input type="text" name="username" id="username" /></td>
				</tr>
				<tr>
						<td class="tLabel">Password:</td>
						<td><label for="pwd"></label>
						<input name="pwd" id="pwd" type="password"/></td>
				</tr>
				<tr>
						<td colspan="2" align="center"><input type="submit" name="submit" id="submit" value="Submit" /></td>
				</tr>
		</table>
		</form>
</div>
</body>
</html>
<?php }} ?>