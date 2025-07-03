<?php

namespace DronesMaritime\Core\Ajax;

use DronesMaritime\Core\App\MissionApp;


$action = filter_input(INPUT_GET, 'action');

if (strlen($action)>0)
{
	$objMissionApp = new MissionApp();
} else {
	$objMissionApp = null;
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

		$ajaxResult = $objMissionApp->render_list($search, $order, $length, $start);

		break;
        case 'planesListar':
                $search= $_POST['txtSearch'];
                $order = $_POST['order'];
                $length= $_POST['length'];
                $start =  $_POST['start'];


                /*$search= "";
                $order = array(0=>['dir'=>'asc','column'=>0]);
                $length= 5;
                $start =  0;*/

                $ajaxResult = $objMissionApp->render_plainList($search, $order, $length, $start);

                break;
	case 'planSeleccion':
                $search= $_POST['txtSearch'];
                $order = $_POST['order'];
                $length= $_POST['length'];
                $start =  $_POST['start'];

                /*$search= "";
                $order = array(0=>['dir'=>'asc','column'=>0]);
                $length= 5;
                $start =  0;*/

		$ajaxResult = $objMissionApp->render_plainSelect01($search, $order, $length, $start);

		break;
	case 'borrar':
		$idMission = $_POST['idMission'];

		$ajaxResult = $objMissionApp->del_mission($idMission);

		break;
        case 'borrarplan':
                $idPlain = $_POST['idPlain'];

                $ajaxResult = $objMissionApp->del_plain($idPlain);

                break;
        case 'addPilot':
                $ajaxResult = $objMissionApp->add_pilot();
                break;

        case 'itemsPilot':
                $search= $_POST['txtSearch'];
                $order = $_POST['order'];
                $length= $_POST['length'];
                $start =  $_POST['start'];
                $tmp_id =  $_POST['tmp_id'];

                /*
                $search= "";    
                $order = array(0=>['dir'=>'asc','column'=>0]);
                $length= 5;
                $start =  0;
                $tmp_id = "493034e848";
                */


                $ajaxResult = $objMissionApp->load_itemsPilot($search, $order, $length, $start, $tmp_id);

                break;
        case 'delete_itemPilot':
                $idPiloto       = $_POST['idPiloto'];
                $tmp_id         = $_POST['tmp_id'];

                $ajaxResult = $objMissionApp->del_plainPilotItem($idPiloto, $tmp_id);

                break;        
}

//ob_clean();

header("Content-Type: application/json");
echo json_encode($ajaxResult);
exit();		
