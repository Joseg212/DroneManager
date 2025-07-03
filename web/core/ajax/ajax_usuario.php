<?php

namespace DronesMaritime\Core\Ajax;

use DronesMaritime\Core\App\UserApp;


$action = filter_input(INPUT_GET, 'action');

if (strlen($action)>0)
{
	$objUserApp = new UserApp();
} else {
	$objUserApp = null;
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

		$ajaxResult = $objUserApp->render_list($search, $order, $length, $start);

		break;
	case 'searchList':
                $search= $_POST['txtSearch'];
                $order = $_POST['order'];
                $length= $_POST['length'];
                $start =  $_POST['start'];
                $type_list = $_POST['type_list'];


                /*$search= "";
                $order = array(0=>['dir'=>'asc','column'=>0]);
                $length= 5;
                $start =  0;*/

		$ajaxResult = $objUserApp->render_searchList($search, $order, $length, $start,$type_list);

		break;
	case 'borrar':
		$idUser = $_POST['idUser'];

		$ajaxResult = $objUserApp->del_user($idUser);

		break;
}

ob_clean();

header("Content-Type: application/json");
echo json_encode($ajaxResult);
exit();		
