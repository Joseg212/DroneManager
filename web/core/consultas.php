<?php 

// Permite ingresar los usuario al sistema
namespace DronesMaritime\Core;

use DronesMaritime\Core\App\ConsultApp;


if ($objInitApp->access_token())
{
	$objConsultApp = new ConsultApp();

	$action = filter_input(INPUT_GET, 'action');

	if (strlen($action)>0)
	{
		switch ($action) {
			case 'ver':
				$functions->registerAction($arrUser,'Consultando datos de las Misiones');
				$id_mission = $_GET['data'];

				$objConsultApp->view_mission($id_mission);
				break;
		}

	} else {
		// Se asume por defecto la lista de clientes.
		$functions->registerAction($arrUser,'En el modulo de Consulta de Misiones');		
		$objConsultApp->get_list();
	}

} else {
	header("Location: /");
	die();
}

