<?php

namespace DronesMaritime\Core\Ajax;

use DronesMaritime\Core\App\InitApp;


$action = filter_input(INPUT_GET, 'action');

if (strlen($action)>0)
{
	$objInitApp = new InitApp();
} else {
	$objInitApp = null;
}

$ajaxResult = array();

switch ($action) {
	case 'viewLogs':


		$ajaxResult = $objInitApp->render_viewLogList();

		break;
}

ob_clean();

header("Content-Type: application/json");
echo json_encode($ajaxResult);
exit();		
