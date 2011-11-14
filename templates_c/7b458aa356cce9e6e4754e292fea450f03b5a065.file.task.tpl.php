<?php /* Smarty version Smarty-3.1.3, created on 2011-11-14 09:24:03
         compiled from ".\templates\task.tpl" */ ?>
<?php /*%%SmartyHeaderCode:245334ebf1961445f55-65204583%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7b458aa356cce9e6e4754e292fea450f03b5a065' => 
    array (
      0 => '.\\templates\\task.tpl',
      1 => 1321222969,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '245334ebf1961445f55-65204583',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.3',
  'unifunc' => 'content_4ebf1961517d7',
  'variables' => 
  array (
    'link' => 0,
    'results_count' => 0,
    'google_sync' => 0,
    'results' => 0,
    'r' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4ebf1961517d7')) {function content_4ebf1961517d7($_smarty_tpl) {?><!DOCTYPE html 
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Listing contacts</title>
    <link rel="stylesheet" href="style/structure.css" type="text/css" media="screen">
    <script type="text/javascript" src="js/jquery-1.6.4.min.js"></script>
  </head>
  <body>
    <h2>Tasks  
    <font size='2'>
    <a href='index.php?module=contact&method=display'>Contacts</a>
    <a href='index.php?module=calendar&method=display'>Calendar Events</a>  
    <a href='index.php?module=documentlibrary&method=df'>Document Library</a>
    <a href='index.php?module=user&method=logout'>logout</a>
    </font>
    </h2>
    <div>
    <?php echo $_smarty_tpl->tpl_vars['link']->value;?>

    </div>
    <div>
    <?php echo $_smarty_tpl->tpl_vars['results_count']->value;?>

    </div>
    <div>
    <?php echo $_smarty_tpl->tpl_vars['google_sync']->value;?>

    </div> 
    <?php  $_smarty_tpl->tpl_vars['r'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['r']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['results']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['r']->key => $_smarty_tpl->tpl_vars['r']->value){
$_smarty_tpl->tpl_vars['r']->_loop = true;
?>   
		<div class="entry <?php if (($_smarty_tpl->tpl_vars['r']->value['is_deleted'])){?> deleted <?php }?>">
			<div class="name"><?php echo $_smarty_tpl->tpl_vars['r']->value['title'];?>
</div>
			<div class="data">
				<table>
					<tr>
						<td class="header">description:</td>
						<td><?php echo $_smarty_tpl->tpl_vars['r']->value['description'];?>
</td>
					</tr>
					<tr>
						<td class="header">date</td>
						<td><?php echo $_smarty_tpl->tpl_vars['r']->value['date'];?>
</td>
					</tr>
					<tr>
						<td class="header">status:</td>
						<td><?php echo $_smarty_tpl->tpl_vars['r']->value['status'];?>
</td>
					</tr>
					<tr>
						<td class="header">googleID:</td>
						<td><?php echo $_smarty_tpl->tpl_vars['r']->value['googleID'];?>
</td>
					</tr>
				</table>
			</div>
		</div>
		<?php } ?>
  </body>
</html><?php }} ?>