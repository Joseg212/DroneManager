<?php

namespace DronesMaritime\Core\Ajax;

use DronesMaritime\Core\App\ClientApp;


$action = filter_input(INPUT_GET, 'action');

if (strlen($action)>0)
{
	$objClientApp = new ClientApp();
} else {
	$objClientApp = null;
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

		$ajaxResult = $objClientApp->render_list($search, $order, $length, $start);

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

		$ajaxResult = $objClientApp->render_searchList($search, $order, $length, $start);

		break;
	case 'borrar':
		$idClient = $_POST['idClient'];

		$ajaxResult = $objClientApp->del_client($idClient);

		break;
}

ob_clean();

header("Content-Type: application/json");
echo json_encode($ajaxResult);
exit();		
