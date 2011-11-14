<?php /* Smarty version Smarty-3.1.3, created on 2011-11-14 19:28:29
         compiled from "./templates/contact.tpl" */ ?>
<?php /*%%SmartyHeaderCode:13418801314ec0d12d808714-12044046%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a05a7b8b1f5e72808bdcb576f7ec0fe40032c1d7' => 
    array (
      0 => './templates/contact.tpl',
      1 => 1321258325,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13418801314ec0d12d808714-12044046',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'results_count' => 0,
    'google_sync' => 0,
    'results' => 0,
    'r' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.3',
  'unifunc' => 'content_4ec0d12d972e2',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4ec0d12d972e2')) {function content_4ec0d12d972e2($_smarty_tpl) {?><!DOCTYPE html   PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">  <head>    <title>Listing contacts</title>    <link rel="stylesheet" href="style/structure.css" type="text/css" media="screen">    <script type="text/javascript" src="js/jquery-1.6.4.min.js"></script>  </head>  <body>    <h2>Contacts      <font size='2'>    <a href='index.php?module=calendar&method=display'>Calendar Events</a>      <a href='index.php?module=abtask&method=display'>Tasks</a>    <a href='index.php?module=documentlibrary&method=df'>Document Library</a>    <a href='index.php?module=user&method=logout'>logout</a>    </font>    </h2>    <div>    <?php echo $_smarty_tpl->tpl_vars['link']->value;?>
    </div>    <div>    <?php echo $_smarty_tpl->tpl_vars['results_count']->value;?>
    </div>    <div>    <?php echo $_smarty_tpl->tpl_vars['google_sync']->value;?>
    </div>     <?php  $_smarty_tpl->tpl_vars['r'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['r']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['results']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['r']->key => $_smarty_tpl->tpl_vars['r']->value){
$_smarty_tpl->tpl_vars['r']->_loop = true;
?>   		<div class="entry <?php if (($_smarty_tpl->tpl_vars['r']->value['is_deleted'])){?> deleted <?php }?>">			<div class="name"><?php echo $_smarty_tpl->tpl_vars['r']->value['name'];?>
</div>			<div class="data">				<table>					<tr>						<td class="header">Organization:</td>						<td><?php echo $_smarty_tpl->tpl_vars['r']->value['orgName'];?>
</td>					</tr>					<tr>						<td class="header">Organization Title:</td>						<td><?php echo $_smarty_tpl->tpl_vars['r']->value['orgTitle'];?>
</td>					</tr>					<tr>						<td class="header">Email:</td>						<td><?php echo $_smarty_tpl->tpl_vars['r']->value['emailAddress'];?>
</td>					</tr>					<tr>						<td class="header">Phone:</td>						<td><?php echo $_smarty_tpl->tpl_vars['r']->value['phoneNumber'];?>
</td>					</tr>					<tr>						<td class="header">Web:</td>						<td><?php echo $_smarty_tpl->tpl_vars['r']->value['website'];?>
</td>					</tr>					<tr>						<td class="header">googleID:</td>						<td><?php echo get_google_id($_smarty_tpl->tpl_vars['r']->value['googleID']);?>
</td>					</tr>					<tr>						<td class="header">updated:</td>						<td><?php echo $_smarty_tpl->tpl_vars['r']->value['updated'];?>
</td>					</tr>				</table>			</div>		</div>		<?php } ?>  </body></html><?php }} ?>