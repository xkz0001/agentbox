<?php
/**
 *
 * All request should be routed by this router.
 *
 * @author      michael xi
 * @package     agentbox
 * 
 */
/* Set the error reporting. */
//error_reporting(1);

/* Start output buffer. */
ob_start();

/* Load the config. */
require 'configs/config.php';

/* Load the smarty. */
require 'libs/smarty/Smarty.class.php';

$smarty = new Smarty();

$smarty->left_delimiter = '<{';
$smarty->right_delimiter = '}>';


/* Load the framework. */
include './framework/router.class.php';
include './framework/control.class.php';
include './framework/model.class.php';

/* Instance the app. */
$app = router::createApp('index', dirname(dirname(__FILE__)));

/* Check the reqeust is getconfig or not. Check installed or not. */
if(isset($_GET['mode']) and $_GET['mode'] == 'getconfig') die($app->exportConfig());  // 

$moudle = isset($_GET ['module']) ?  strtolower($_GET ['module']) : (isset($_POST ['module']) ? strtolower($_POST ['module']) : 'user');
$method = isset($_GET ['method']) ?  $_GET ['method'] : (isset($_POST ['method']) ? strtolower($_POST ['method']) : 'df');

/* Run the app. */
$app->loadModule($moudle, $method);

/* Flush the buffer. */
ob_end_flush();
