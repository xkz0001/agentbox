<?php /* Smarty version Smarty-3.1.3, created on 2011-11-13 11:36:51
         compiled from ".\templates\calendar.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18904ebf01ed22ebf8-82151465%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a44a2de5fc49dbae574cb18bd600156b337551e4' => 
    array (
      0 => '.\\templates\\calendar.tpl',
      1 => 1321144608,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18904ebf01ed22ebf8-82151465',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.3',
  'unifunc' => 'content_4ebf01ed4b966',
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
<?php if ($_valid && !is_callable('content_4ebf01ed4b966')) {function content_4ebf01ed4b966($_smarty_tpl) {?><!DOCTYPE html   PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">  <head>    <title>Calendar</title>    <link rel="stylesheet" href="style/structure.css" type="text/css" media="screen">    <script type="text/javascript" src="js/jquery-1.6.4.min.js"></script>      </head>  <body>    <h2>Calendar Events      <font size='2'>    <a href='index.php?module=contact&method=display'>Contacts</a>      <a href='index.php?module=abtask&method=display'>Tasks</a>    <a href='index.php?module=documentlibrary&method=df'>Document Library</a>    <a href='index.php?module=user&method=logout'>logout</a>    </font>    </h2>    <div>    <?php echo $_smarty_tpl->tpl_vars['link']->value;?>
    </div>    <div>    <?php echo $_smarty_tpl->tpl_vars['results_count']->value;?>
    </div>    <div>    <?php echo $_smarty_tpl->tpl_vars['google_sync']->value;?>
    </div>     <?php  $_smarty_tpl->tpl_vars['r'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['r']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['results']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['r']->key => $_smarty_tpl->tpl_vars['r']->value){
$_smarty_tpl->tpl_vars['r']->_loop = true;
?>    <div class="entry <?php if (($_smarty_tpl->tpl_vars['r']->value['is_deleted'])){?> deleted <?php }?>">      <div class="name"><?php echo $_smarty_tpl->tpl_vars['r']->value['title'];?>
</div>      <div class="data">        <table>          <tr>            <td class="header">Description:</td>            <td><?php echo $_smarty_tpl->tpl_vars['r']->value['description'];?>
</td>          </tr>					<tr>            <td class="header">Where:</td>            <td><?php echo $_smarty_tpl->tpl_vars['r']->value['location'];?>
</td>          </tr>	  <tr>            <td class="header">Who:</td>            <td><?php echo $_smarty_tpl->tpl_vars['r']->value['guest'];?>
</td>          </tr>          <tr>            <td class="header">Start:</td>            <td><?php echo $_smarty_tpl->tpl_vars['r']->value['startTime'];?>
</td>          </tr>          <tr>            <td class="header">End:</td>            <td><?php echo $_smarty_tpl->tpl_vars['r']->value['endTime'];?>
</td>          </tr>          <tr>            <td class="header">googleID:</td>            <td><?php echo get_google_id($_smarty_tpl->tpl_vars['r']->value['googleID']);?>
</td>          </tr>        </table>      </div>    </div>    <?php } ?>  </body></html><?php }} ?>