<?php

namespace DronesMaritime\Core\Ajax;

use DronesMaritime\Core\App\ConsultApp;


$action = filter_input(INPUT_GET, 'action');

if (strlen($action)>0)
{
	$objConsultApp = new ConsultApp();
} else {
	$objConsultApp = null;
}

$ajaxResult = array();

switch ($action) {
	case 'listar':
                /*$search= "";
                $order = array(0=>['dir'=>'asc','column'=>0]);
                $length= 5;
                $start =  0;*/

		$ajaxResult = $objConsultApp->render_list();

		break;
}

ob_clean();

header("Content-Type: application/json");
echo json_encode($ajaxResult);
exit();		
