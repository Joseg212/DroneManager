<?php

namespace DronesMaritime\Core\Ajax;

use DronesMaritime\Core\App\TowApp;


$action = filter_input(INPUT_GET, 'action');

if (strlen($action)>0)
{
	$objTowApp = new TowApp();
} else {
	$objTowApp = null;
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

		$ajaxResult = $objTowApp->render_list($search, $order, $length, $start);

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

		$ajaxResult = $objTowApp->render_searchList($search, $order, $length, $start);

		break;
	case 'borrar':
		$idVehiculo = $_POST['idVehiculo'];

		$ajaxResult = $objTowApp->del_tow($idVehiculo);

		break;
}

ob_clean();

header("Content-Type: application/json");
echo json_encode($ajaxResult);
exit();		
