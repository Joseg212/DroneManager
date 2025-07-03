<?php

namespace DronesMaritime\Core\Ajax;

use DronesMaritime\Core\App\MissionTypeApp;


$action = filter_input(INPUT_GET, 'action');

if (strlen($action)>0)
{
	$objMissionTypeApp = new MissionTypeApp();
} else {
	$objMissionTypeApp = null;
}

$ajaxResult = array();

switch ($action) {
	case 'listar':
                $search= $_POST['txtSearch'];
                $order = $_POST['order'];
                $length= $_POST['length'];
                $start =  $_POST['start'];


                /*$search= "";
                $order = array(0=>['dir'=>'asc','column'=>0]);
                $length= 5;
                $start =  0;*/

		$ajaxResult = $objMissionTypeApp->render_list($search, $order, $length, $start);

		break;
	case 'searchList':
                $search= $_POST['txtSearch'];
                $order = $_POST['order'];
                $length= $_POST['length'];
                $start =  $_POST['start'];


                /*$search= "";
                $order = array(0=>['dir'=>'asc','column'=>0]);
                $length= 5;
                $start =  0;*/

		$ajaxResult = $objMissionTypeApp->render_searchList($search, $order, $length, $start);

		break;
        case 'borrar':
		$idTipom = $_POST['idTipom'];

		$ajaxResult = $objMissionTypeApp->del_mtype($idTipom);

		break;
}

ob_clean();

header("Content-Type: application/json");
echo json_encode($ajaxResult);
exit();		
