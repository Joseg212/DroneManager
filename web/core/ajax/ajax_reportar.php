<?php

namespace DronesMaritime\Core\Ajax;

use DronesMaritime\Core\App\ReportarApp;


$action = filter_input(INPUT_GET, 'action');

if (strlen($action)>0)
{
	$objReportarApp = new ReportarApp();
} else {
	$objReportarApp = null;
}

$ajaxResult = array();

switch ($action) {
	case 'plainList':
                $search= $_POST['txtSearch'];
                $order = $_POST['order'];
                $length= $_POST['length'];
                $start =  $_POST['start'];


                /*$search= "";
                $order = array(0=>['dir'=>'asc','column'=>0]);
                $length= 5;
                $start =  0;*/

		$ajaxResult = $objReportarApp->render_plainList($search, $order, $length, $start);

		break;
        case 'itemsPilotos':
                $search= $_POST['txtSearch'];
                $order = $_POST['order'];
                $length= $_POST['length'];
                $start =  $_POST['start'];
                $id_plan = $_POST['id_plan'];


                /*$search= "";
                $order = array(0=>['dir'=>'asc','column'=>0]);
                $length= 5;
                $start =  0;
                $id_plan='PLAN100001';*/


                $ajaxResult = $objReportarApp->reportar_itemsPilots($search, $order, $length, $start,$id_plan);

                break;
        case 'itemsArchivos':
                $search= $_POST['txtSearch'];
                $order = $_POST['order'];
                $length= $_POST['length'];
                $start =  $_POST['start'];
                $id_mision = $_POST['idMision'];


                /*$search= "";
                $order = array(0=>['dir'=>'asc','column'=>0]);
                $length= 5;
                $start =  0;
                $id_mision='';*/


                $ajaxResult = $objReportarApp->upload_itemsArchivos($search, $order, $length, $start,$id_mision);

                break;
        case 'gestor_reporte':
                $token_id       = $_POST['token_id'];
                $fecha_final    = $_POST['fecha_final'];
                $observg_final  = $_POST['observg_final'];
                $idMision       = $_POST['idMision'];

                $ajaxResult = $objReportarApp->gestor_reporte($token_id, $fecha_final,$observg_final,$idMision);
                break;               
        case 'archivoBorrar':
                $idArchivo = $_POST['idArchivo'];
                $FileId = $_POST['FileId'];

                $ajaxResult = $objReportarApp->del_archivo($idArchivo,$FileId);

                break;
        case 'finishList':
                $search= $_POST['txtSearch'];
                $order = $_POST['order'];
                $length= $_POST['length'];
                $start =  $_POST['start'];


                /*$search= "";
                $order = array(0=>['dir'=>'asc','column'=>0]);
                $length= 5;
                $start =  0;*/

                $ajaxResult = $objReportarApp->render_finishList($search, $order, $length, $start);

                break;                
}

ob_clean();

header("Content-Type: application/json");
echo json_encode($ajaxResult);
exit();		
