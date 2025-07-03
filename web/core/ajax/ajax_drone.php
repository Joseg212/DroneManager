<?php

namespace DronesMaritime\Core\Ajax;

use DronesMaritime\Core\App\DroneApp;


$action = filter_input(INPUT_GET, 'action');

if (strlen($action)>0)
{
	$objDroneApp = new DroneApp();
} else {
	$objDroneApp = null;
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

		$ajaxResult = $objDroneApp->render_list($search, $order, $length, $start);

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

		$ajaxResult = $objDroneApp->render_searchList($search, $order, $length, $start);

		break;		
	case 'borrar':
		$idDrone = $_POST['idDrone'];

		$ajaxResult = $objDroneApp->del_drone($idDrone);

		break;
}

ob_clean();

header("Content-Type: application/json");
echo json_encode($ajaxResult);
exit();		
