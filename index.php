<?php
/*if($_SERVER['REMOTE_ADDR'] == '80.78.51.210'){*/

define('VERSION', '1.5');
error_reporting(0);

$yii=dirname(__FILE__).'/framework/yii.php';
$db_error_file = dirname(__FILE__).'/db_error.php';
define('DB_ERROR_FILE', $db_error_file);
//$config_file = (($_SERVER['REMOTE_ADDR'] == '127.0.0.1') || ($_SERVER['REMOTE_ADDR'] == '185.68.16.179')) ? 'local.php' : 'main.php';

//$config_file = (($_SERVER['HTTP_HOST'] == 'flowers3.loc') || ($_SERVER['HTTP_HOST'] == 'id20.andkorol.com')) ? 'local.php' : 'main.php';
$config_file = (($_SERVER['HTTP_HOST'] == '1-7roses.com.vps')) ? 'local.php' : 'main.php';
$config=dirname(__FILE__).'/protected/config/' . $config_file;

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

require $yii;
require 'protected/components/SWebApplication.php';

Yii::createApplication('SWebApplication', $config)->run();
/*}else{
	echo "<img src='cooming.jpg' style='display:block; margin:0 auto' />";
}
*/
