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
<?php if ($_valid && !is_callable('content_4ebf01ed4b966')) {function content_4ebf01ed4b966($_smarty_tpl) {?><!DOCTYPE html 



 $_from = $_smarty_tpl->tpl_vars['results']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['r']->key => $_smarty_tpl->tpl_vars['r']->value){
$_smarty_tpl->tpl_vars['r']->_loop = true;
?>
</div>
</td>
</td>
</td>
</td>
</td>
</td>